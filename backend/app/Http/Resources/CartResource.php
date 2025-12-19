<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $variant = $this->variant;
        $product = $variant ? $variant->product : null;

        // Lấy ảnh
        $image = ($variant && $variant->images && $variant->images->first())
            ? $variant->images->first()->image_url
            : null;

        return [
            // Bảng không có ID riêng, nên dùng variant_id làm định danh
            'cart_id' => $this->variant_id,
            'product_id' => $product ? $product->id : null,
            'product_name' => $product ? $product->name : 'Unknown',
            'variant_id' => $this->variant_id, // Quan trọng: Frontend sẽ dùng cái này để gọi API xóa/sửa
            'color' => $variant ? $variant->color : null,
            'size' => $variant ? $variant->size : null,
            'price' => $variant ? (float) $variant->price : 0,
            'quantity' => (int) $this->quantity,
            'total_price' => $variant ? (float) $variant->price * $this->quantity : 0,
            'image' => $image,
            'seller' => $product && $product->seller ? [
                'id' => $product->seller->id,
                'name' => $product->seller->full_name,
            ] : null,
        ];
    }
}