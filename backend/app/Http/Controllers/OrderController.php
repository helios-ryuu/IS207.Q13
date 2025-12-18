<?php

namespace App\Http\Controllers;


use App\Models\Order;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Services\OrderService;
use App\Http\Resources\OrderResource;
use App\Http\Requests\CreateOrderRequest; // <--- Import Request mới
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    // 1. POST /api/orders (Tạo đơn)
    // Thay Request thường bằng CreateOrderRequest
    public function store(CreateOrderRequest $request) 
    {
        try {
            $userId = Auth::id();
            // $request->validated() sẽ trả về dữ liệu đã được kiểm tra (tên, sđt, địa chỉ...)
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

    // ... Giữ nguyên các hàm index, show, cancel, updateStatus ...
    public function index()
    {
        $userId = Auth::id();
        $orders = $this->orderService->getUserOrders($userId);
        return response()->json(['status' => 'success', 'data' => OrderResource::collection($orders)]);
    }

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

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,confirmed,processing,shipped,delivered,completed,cancelled,refunded'
        ]);

        DB::beginTransaction(); // Bắt đầu giao dịch bảo đảm an toàn
        try {
            $order = Order::findOrFail($id);
            $oldStatus = $order->status;
            $newStatus = $request->status;

            // 1. Cập nhật trạng thái đơn hàng
            // (Nếu Service của bạn chỉ update status đơn giản thì giữ dòng này, 
            // nếu không hãy dùng $order->update(['status' => $newStatus]);)
            $this->orderService->updateStatus($id, $newStatus);

            // 2. LOGIC MỚI: Cộng tiền cho Seller khi đơn hoàn tất
            if ($newStatus === 'completed' && $oldStatus !== 'completed') {
                
                // Lấy Seller từ sản phẩm đầu tiên trong đơn
                // (Giả định chi tiết đơn nằm trong quan hệ 'details' và product có 'user_id')
                $firstDetail = $order->details->first(); // Hoặc $order->orderDetails->first() tùy model của bạn
                $sellerId = $firstDetail ? $firstDetail->product->user_id : null;

                if ($sellerId) {
                    // Tìm ví của seller (hoặc tạo mới nếu chưa có)
                    $wallet = Wallet::firstOrCreate(
                        ['user_id' => $sellerId],
                        ['balance' => 0, 'status' => 'active']
                    );

                    // Cộng tiền (Ví dụ: Trừ 5% phí sàn, Seller nhận 95%)
                    $income = $order->total_amount; 
                    // $income = $order->total_amount * 0.95; // Nếu muốn trừ phí

                    $wallet->balance += $income;
                    $wallet->save();

                    // Lưu lịch sử biến động số dư
                    Transaction::create([
                        'order_id' => $order->id, // Gắn với đơn hàng
                        'amount' => $income,      // Số dương = tiền vào
                        'payment_method' => 'system',
                        'status' => 'completed',
                        'transaction_code' => 'INCOME_' . $order->id,
                        'transaction_date' => now(),
                        // 'user_id' => $sellerId, // Bỏ comment nếu bảng transaction có cột user_id
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
    // 6. GET /api/seller/orders (Danh sách đơn bán)
    public function getSellerOrders()
    {
        try {
            $sellerId = Auth::id(); // Lấy ID người đang đăng nhập
            $orders = $this->orderService->getSellerOrders($sellerId);
            
            // Tái sử dụng OrderResource hoặc trả về dữ liệu gốc
            // Ở đây mình trả về dạng Resource collection cho chuẩn
            return response()->json([
                'status' => 'success', 
                'data' => OrderResource::collection($orders)
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    
}