<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Http\Resources\OrderResource;
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
    public function store(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:shipping_addresses,id',
            'payment_method' => 'required|string|in:cash,bank_transfer,wallet,credit_card',
            'notes' => 'nullable|string'
        ]);

        try {
            $userId = Auth::id();
            $order = $this->orderService->createOrder($userId, $request->all());
            
            return response()->json([
                'status' => 'success', 
                'message' => 'Đặt hàng thành công!',
                'data' => new OrderResource($order)
            ], 201);

        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // 2. GET /api/orders (Danh sách đơn)
    public function index()
    {
        $userId = Auth::id();
        $orders = $this->orderService->getUserOrders($userId);
        return response()->json(['status' => 'success', 'data' => OrderResource::collection($orders)]);
    }

    // 3. GET /api/orders/{id} (Chi tiết đơn)
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

    // 4. POST /api/orders/{id}/cancel (Hủy đơn)
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

    // 5. PUT /api/orders/{id}/status (Cập nhật trạng thái)
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
}