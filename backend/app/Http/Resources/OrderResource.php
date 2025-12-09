<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Lấy tổng tiền từ bảng transactions (lấy giao dịch đầu tiên hoặc cộng tổng)
        $totalAmount = $this->transactions->sum('amount'); 

        return [
            'id' => $this->id,
            'tracking_code' => $this->tracking_code,
            'status' => $this->status,
            'order_date' => $this->order_date ? $this->order_date->format('d/m/Y H:i') : null,
            
            // Hiển thị tiền (Lấy từ transaction)
            'total_amount' => number_format($totalAmount) . ' VNĐ',
            'shipping_fee' => number_format($this->shipping_fee) . ' VNĐ',
            
            'payment_method' => $this->payment_method,
            'notes' => $this->notes,
            
            // Địa chỉ (Check null để tránh lỗi)
            'shipping_address' => $this->shippingAddress ? ($this->shippingAddress->street_address . ', ' . $this->shippingAddress->ward) : 'N/A',
            'receiver_name' => $this->shippingAddress ? $this->shippingAddress->receiver_name : '',

            // Danh sách món ăn
            'items' => $this->orderDetails->map(function($item) {
                return [
                    'product_name' => $item->variant && $item->variant->product ? $item->variant->product->product_name : 'Unknown',
                    'variant' => $item->variant ? $item->variant->color . '/' . $item->variant->size : '',
                    'quantity' => $item->quantity,
                    // Lưu ý: bảng order_details của bạn dùng cột unit_price hay price? 
                    // Nếu Model OrderDetail chưa có, nhớ check lại. Ở đây mình giả định là unit_price theo code service
                    'price' => number_format($item->unit_price ?? $item->price ?? 0) . ' VNĐ',
                ];
            }),
        ];
    }
}