<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\ProductVariant;
use Exception;

class CartService
{
    // Lấy giỏ hàng
    public function getCart($userId)
    {
        return CartItem::with(['variant.product.seller', 'variant.images'])
            ->where('user_id', $userId)
            ->get();
    }

    // Thêm vào giỏ
    public function addToCart($userId, $data)
    {
        $variantId = $data['variant_id']; // Dùng đúng tên cột 'variant_id'
        $quantity = $data['quantity'];

        // 1. Kiểm tra sản phẩm
        $variant = ProductVariant::find($variantId);
        if (!$variant) {
            throw new Exception("Sản phẩm không tồn tại.");
        }

        // 2. Check kho
        if ($variant->quantity < $quantity) {
            throw new Exception("Kho không đủ hàng. Chỉ còn: " . $variant->quantity);
        }

        // 3. Tìm item trong giỏ (Dùng 2 điều kiện vì khóa chính phức hợp)
        $cartItem = CartItem::where('user_id', $userId)
            ->where('variant_id', $variantId)
            ->first();

        if ($cartItem) {
            // Có rồi thì cộng dồn
            $newQuantity = $cartItem->quantity + $quantity;

            if ($variant->quantity < $newQuantity) {
                throw new Exception("Tổng số lượng vượt quá tồn kho.");
            }

            // Lưu ý: Với khóa phức hợp, ta không dùng $cartItem->save() bình thường được nếu Laravel không hỗ trợ native
            // Nên dùng update query cho chắc chắn
            CartItem::where('user_id', $userId)
                ->where('variant_id', $variantId)
                ->update(['quantity' => $newQuantity]);

        } else {
            // Tạo mới
            CartItem::create([
                'user_id' => $userId,
                'variant_id' => $variantId,
                'quantity' => $quantity
            ]);
        }

        return true;
    }

    // Cập nhật số lượng
    // $itemId ở đây chính là variant_id truyền từ URL
    public function updateItem($userId, $variantId, $quantity)
    {
        // Tìm đúng sản phẩm đó của user đó
        $cartItem = CartItem::where('user_id', $userId)
            ->where('variant_id', $variantId)
            ->first();

        if (!$cartItem) {
            throw new Exception("Sản phẩm này không có trong giỏ hàng.");
        }

        if ($quantity <= 0) {
            // Xóa luôn nếu số lượng = 0
            CartItem::where('user_id', $userId)
                ->where('variant_id', $variantId)
                ->delete();
            return null;
        }

        // Check kho
        $variant = $cartItem->variant;
        if ($variant && $variant->quantity < $quantity) {
            throw new Exception("Kho không đủ hàng.");
        }

        // Update số lượng
        CartItem::where('user_id', $userId)
            ->where('variant_id', $variantId)
            ->update(['quantity' => $quantity]);

        return true;
    }

    // Xóa 1 món
    public function removeItem($userId, $variantId)
    {
        return CartItem::where('user_id', $userId)
            ->where('variant_id', $variantId)
            ->delete();
    }

    // Xóa sạch
    public function clearCart($userId)
    {
        return CartItem::where('user_id', $userId)->delete();
    }
}