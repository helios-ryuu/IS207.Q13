<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Http\Resources\CartResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        try {
            $userId = Auth::id();
            $cartItems = $this->cartService->getCart($userId);

            $totalAmount = $cartItems->sum(function($item) {
                $price = $item->variant ? $item->variant->price : 0;
                return $item->quantity * $price;
            });

            return response()->json([
                'status' => 'success',
                'data' => CartResource::collection($cartItems),
                'total_amount' => $totalAmount
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|integer',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $userId = Auth::id();
            $this->cartService->addToCart($userId, $request->all());
            return response()->json(['status' => 'success', 'message' => 'Đã thêm vào giỏ hàng'], 201);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // PUT /api/cart/items/{id} 
    // Lưu ý: {id} ở đây chính là variant_id muốn sửa
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0'
        ]);

        try {
            $userId = Auth::id();
            // Gọi service với $id (là variant_id)
            $this->cartService->updateItem($userId, $id, $request->quantity);
            return response()->json(['status' => 'success', 'message' => 'Cập nhật thành công']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // DELETE /api/cart/items/{id}
    // {id} ở đây là variant_id muốn xóa
    public function destroy($id)
    {
        try {
            $userId = Auth::id();
            $this->cartService->removeItem($userId, $id);
            return response()->json(['status' => 'success', 'message' => 'Đã xóa sản phẩm']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function clear()
    {
        try {
            $userId = Auth::id();
            $this->cartService->clearCart($userId);
            return response()->json(['status' => 'success', 'message' => 'Giỏ hàng đã được làm trống']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}