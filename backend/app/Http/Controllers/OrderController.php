<?php

namespace App\Http\Controllers;

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
            'status' => 'required|string|in:pending,confirmed,processing,shipped,delivered,cancelled,refunded'
        ]);

        try {
            $this->orderService->updateStatus($id, $request->status);
            return response()->json(['status' => 'success', 'message' => 'Cập nhật trạng thái thành công']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // 6. GET /api/seller/orders (Danh sách đơn bán)
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
}