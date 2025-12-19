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

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    // 1. POST /api/orders (Tạo đơn)
    public function store(CreateOrderRequest $request)
    {
        try {
            $userId = Auth::id();
            $order = $this->orderService->createOrder($userId, $request->validated());

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
    public function cancel($id)
    {
        try {
            $userId = Auth::id();
            $this->orderService->cancelOrder($userId, $id);
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

                $firstDetail = $order->details->first();
                $sellerId = $firstDetail ? $firstDetail->product->user_id : null;

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
                }
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
            return response()->json([
                'status' => 'success',
                'message' => 'Đã chấp nhận đơn hàng',
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
    public function sellerCancelOrder($id)
    {
        try {
            $sellerId = Auth::id();
            $order = $this->orderService->sellerCancelOrder($sellerId, $id);
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
            $sellerId = Auth::id();
            $order = $this->orderService->completeOrder($sellerId, $id);
            return response()->json([
                'status' => 'success',
                'message' => 'Đơn hàng đã hoàn thành',
                'data' => new OrderResource($order)
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}