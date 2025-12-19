<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // [FIX] Lấy tổng tiền từ cột total_amount trong DB, fallback về tính từ order details
        $totalAmount = $this->total_amount > 0
            ? $this->total_amount
            : $this->orderDetails->sum(fn($d) => $d->quantity * $d->unit_price) + $this->shipping_fee;

        // Status labels tiếng Việt cho seller view
        $statusLabels = [
            'pending' => 'CHỜ XÁC NHẬN',
            'processing' => 'ĐANG XỬ LÝ',
            'shipping' => 'ĐANG VẬN CHUYỂN',
            'delivered' => 'ĐÃ GIAO',
            'completed' => 'HOÀN THÀNH',
            'cancelled' => 'ĐÃ HỦY',
            'return' => 'YÊU CẦU TRẢ HÀNG',
            'refunded' => 'ĐÃ HOÀN TIỀN',
        ];

        return [
            'id' => $this->id,
            'tracking_code' => $this->tracking_code,
            'status' => $this->status,
            'status_label' => $statusLabels[$this->status] ?? strtoupper($this->status),
            'order_date' => $this->order_date ? $this->order_date->format('d/m/Y H:i') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('d/m/Y H:i') : null,

            // Hiển thị tiền
            'total_amount' => $totalAmount,
            'total_amount_formatted' => number_format($totalAmount) . ' VNĐ',
            'shipping_fee' => $this->shipping_fee,
            'shipping_fee_formatted' => number_format($this->shipping_fee) . ' VNĐ',

            'payment_method' => $this->payment_method,
            'notes' => $this->notes,

            // Thông tin khách hàng (buyer)
            'customer' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->full_name ?? $this->user->name ?? 'Khách hàng',
                'avatar' => $this->user->avatar_url ?? $this->user->avatar,
            ] : null,

            // Địa chỉ giao hàng
            'shipping_address' => $this->shippingAddress ? [
                'full_address' => $this->shippingAddress->street_address . ', ' .
                    $this->shippingAddress->ward . ', ' .
                    $this->shippingAddress->district . ', ' .
                    $this->shippingAddress->province,
                'receiver_name' => $this->shippingAddress->receiver_name,
                'phone_number' => $this->shippingAddress->phone_number,
            ] : null,

            // Danh sách sản phẩm
            'items' => $this->orderDetails->map(function ($item) {
                $product = $item->variant?->product;
                $seller = $product?->seller;

                // Lấy ảnh từ variant hoặc product (sử dụng accessor full_image_url)
                $variantImage = $item->variant?->images?->first();
                $imageUrl = $variantImage?->full_image_url ?? null;

                // Lấy category name đầu tiên từ product
                $categoryName = $product?->categories?->first()?->name ?? 'Khác';

                return [
                    'variant_id' => $item->variant_id,
                    'product_id' => $product?->id,
                    'product_name' => $product?->name ?? 'Sản phẩm', // Sử dụng 'name' thay vì 'product_name'
                    'variant' => $item->variant ? ($item->variant->color . '/' . $item->variant->size) : '',
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price ?? 0,
                    'unit_price_formatted' => number_format($item->unit_price ?? 0) . ' VNĐ',
                    'image' => $imageUrl,
                    'category' => $categoryName, // Thêm category cho frontend filter
    
                    // Thêm seller info
                    'seller_id' => $seller?->id,
                    'seller_name' => $seller?->full_name ?? $seller?->name ?? 'Người bán',
                    'seller_avatar' => $seller?->avatar_url ?? null,
                ];
            }),
        ];
    }
}