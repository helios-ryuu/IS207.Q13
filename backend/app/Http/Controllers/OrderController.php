<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Http\Resources\OrderResource;
use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

use App\Services\NotificationService;

class OrderController extends Controller
{
    protected $orderService;
    protected $notificationService;

    public function __construct(OrderService $orderService, NotificationService $notificationService)
    {
        $this->orderService = $orderService;
        $this->notificationService = $notificationService;
    }

    // 1. POST /api/orders (Tạo đơn)
    public function store(CreateOrderRequest $request)
    {
        try {
            $userId = Auth::id();
            $order = $this->orderService->createOrder($userId, $request->validated());

            // Notify User
            $this->notificationService->create($userId, 'Đơn hàng mới', "Bạn đã đặt đơn hàng #{$order->tracking_code} thành công.", 'order');

            // Notify Seller
            $firstDetail = $order->orderDetails->first();
            $sellerId = $firstDetail?->variant?->product?->user_id;
            if ($sellerId) {
                $this->notificationService->create($sellerId, 'Đơn hàng mới', "Bạn có đơn hàng mới #{$order->tracking_code}.", 'order');
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Đặt hàng thành công!',
                'data' => new OrderResource($order)
            ], 201);

        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // 2. GET /api/orders (Danh sách đơn mua)
    public function index()
    {
        $userId = Auth::id();
        $orders = $this->orderService->getUserOrders($userId);
        return response()->json(['status' => 'success', 'data' => OrderResource::collection($orders)]);
    }

    // 3. GET /api/orders/{id} (Chi tiết đơn hàng)
    public function show($id)
    {
        try {
            $userId = Auth::id();
            $order = $this->orderService->getOrderDetail($userId, $id);
            return response()->json(['status' => 'success', 'data' => new OrderResource($order)]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy đơn hàng'], 404);
        }
    }

    // 4. PUT /api/orders/{id}/cancel (Hủy đơn mua)
    public function cancel(Request $request, $id)
    {
        try {
            $userId = Auth::id();
            $reason = $request->input('reason', '');
            $order = $this->orderService->cancelOrder($userId, $id, $reason);

            // Notify Seller
            $firstDetail = $order->orderDetails->first();
            $sellerId = $firstDetail?->variant?->product?->user_id;
            if ($sellerId) {
                $this->notificationService->create($sellerId, 'Hủy đơn hàng', "Đơn hàng #{$order->tracking_code} đã bị khách hàng hủy. Lý do: {$reason}", 'order');
            }

            return response()->json(['status' => 'success', 'message' => 'Đã hủy đơn hàng']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // 5. PUT /api/orders/{id}/status (Cập nhật trạng thái & Cộng tiền ví)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,confirmed,processing,shipped,delivered,completed,cancelled,refunded'
        ]);

        DB::beginTransaction();
        try {
            $order = Order::findOrFail($id);
            $oldStatus = $order->status;
            $newStatus = $request->status;

            // 1. Cập nhật trạng thái
            $this->orderService->updateStatus($id, $newStatus);

            // 2. LOGIC VÍ: Cộng tiền cho Seller khi đơn hoàn tất
            if ($newStatus === 'completed' && $oldStatus !== 'completed') {

                $firstDetail = $order->orderDetails->first();
                $sellerId = $firstDetail?->variant?->product?->user_id;

                if ($sellerId) {
                    $wallet = Wallet::firstOrCreate(
                        ['user_id' => $sellerId],
                        ['balance' => 0, 'status' => 'active']
                    );

                    $income = $order->total_amount;
                    $wallet->balance += $income;
                    $wallet->save();

                    Transaction::create([
                        'user_id' => $sellerId,
                        'order_id' => $order->id,
                        'amount' => $income,
                        'payment_method' => 'wallet', // system/wallet
                        'status' => 'completed',
                        'transaction_code' => 'INC_' . $order->id . '_' . time(),
                        'transaction_date' => now(),
                        'response_data' => json_encode(['note' => 'Doanh thu bán hàng']),
                    ]);

                    // Notify Seller
                    $this->notificationService->create($sellerId, 'Đơn hàng hoàn tất', "Đơn hàng #{$order->tracking_code} đã hoàn tất. Tiền đã được cộng vào ví.", 'order');
                }

                // Notify Buyer
                $this->notificationService->create($order->user_id, 'Đơn hàng hoàn tất', "Đơn hàng #{$order->tracking_code} đã hoàn tất. Cảm ơn bạn đã xác nhận nhận hàng.", 'order');
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Cập nhật trạng thái thành công']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // 6. GET /api/seller/orders (Danh sách đơn bán - Có filter)
    public function getSellerOrders(Request $request)
    {
        try {
            $sellerId = Auth::id();
            $status = $request->query('status'); // Filter by status if provided
            $orders = $this->orderService->getSellerOrders($sellerId, $status);

            return response()->json([
                'status' => 'success',
                'data' => OrderResource::collection($orders)
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // --- CÁC HÀM MỚI TỪ NHÁNH MAIN (MERGE VÀO) ---

    // 7. PUT /api/seller/orders/{id}/accept - Chấp nhận đơn hàng
    public function acceptOrder($id)
    {
        try {
            $sellerId = Auth::id();
            $order = $this->orderService->acceptOrder($sellerId, $id);

            // Notify Buyer
            $this->notificationService->create($order->user_id, 'Đơn hàng đang vận chuyển', "Đơn hàng #{$order->tracking_code} của bạn đã được xác nhận và đang được vận chuyển.", 'order');

            return response()->json([
                'status' => 'success',
                'message' => 'Đã xác nhận và chuyển sang vận chuyển',
                'data' => new OrderResource($order)
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // 8. PUT /api/seller/orders/{id}/ship - Chuyển sang vận chuyển
    public function shipOrder($id)
    {
        try {
            \Log::info("Ship order request received for ID: $id");
            $sellerId = Auth::id();
            $order = $this->orderService->shipOrder($sellerId, $id);

            // Notify Buyer
            $this->notificationService->create($order->user_id, 'Đơn hàng đang vận chuyển', "Đơn hàng #{$order->tracking_code} đã được đóng gói và đang vận chuyển.", 'order');

            return response()->json([
                'status' => 'success',
                'message' => 'Đã chuyển đơn hàng sang vận chuyển',
                'data' => new OrderResource($order)
            ]);
        } catch (Exception $e) {
            \Log::error("Ship order failed: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // 9. PUT /api/seller/orders/{id}/cancel - Seller hủy đơn
    public function sellerCancelOrder(Request $request, $id)
    {
        try {
            $sellerId = Auth::id();
            $reason = $request->input('reason', '');
            $order = $this->orderService->sellerCancelOrder($sellerId, $id, $reason);

            // Notify Buyer
            $this->notificationService->create($order->user_id, 'Đơn hàng bị hủy', "Đơn hàng #{$order->tracking_code} đã bị người bán hủy. Lý do: {$reason}", 'order');

            return response()->json([
                'status' => 'success',
                'message' => 'Đã hủy đơn hàng',
                'data' => new OrderResource($order)
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // 10. PUT /api/seller/orders/{id}/confirm-return - Xác nhận hoàn hàng
    public function confirmReturn($id)
    {
        try {
            $sellerId = Auth::id();
            $order = $this->orderService->confirmReturn($sellerId, $id);
            return response()->json([
                'status' => 'success',
                'message' => 'Đã xác nhận hoàn hàng và hoàn tiền',
                'data' => new OrderResource($order)
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // 11. PUT /api/seller/orders/{id}/complete - Hoàn tất vận chuyển
    public function completeOrder($id)
    {
        try {
            DB::beginTransaction();
            \Log::info("CompleteOrder API called for ID: $id");
            $sellerId = Auth::id();
            \Log::info("Auth Seller ID: $sellerId");

            $order = $this->orderService->completeOrder($sellerId, $id);
            \Log::info("Order retrieved. Buyer ID: " . $order->user_id);

            // LOGIC VÍ: Cộng tiền cho Seller
            $firstDetail = $order->orderDetails->first(); // Note: $order from service might not have relations loaded if not requested. Service returns findOrFail result.
            // OrderService completeOrder returns $order (fresh?).
            // Let's reload to be safe or rely on lazy loading

            $checkSellerId = $firstDetail?->variant?->product?->user_id;
            \Log::info("Derived Seller ID from Product: " . ($checkSellerId ?? 'NULL'));

            // Logic giống updateStatus
            if ($checkSellerId) {
                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $checkSellerId],
                    ['balance' => 0, 'status' => 'active']
                );

                $income = $order->total_amount;
                $wallet->balance += $income;
                $wallet->save();

                Transaction::create([
                    'user_id' => $checkSellerId,
                    'order_id' => $order->id,
                    'amount' => $income,
                    'payment_method' => 'wallet',
                    'status' => 'completed',
                    'transaction_code' => 'INC_' . $order->id . '_' . time(),
                    'transaction_date' => now(),
                    'response_data' => json_encode(['note' => 'Doanh thu bán hàng']),
                ]);

                // Notify Seller
                $this->notificationService->create($checkSellerId, 'Đơn hàng hoàn tất', "Đơn hàng #{$order->tracking_code} đã hoàn tất. Tiền đã được cộng vào ví.", 'order');
            }

            // Notify Buyer
            $this->notificationService->create($order->user_id, 'Đơn hàng hoàn tất', "Đơn hàng #{$order->tracking_code} đã được giao thành công.", 'order');

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Đơn hàng đã hoàn thành và đã cộng doanh thu',
                'data' => new OrderResource($order)
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}