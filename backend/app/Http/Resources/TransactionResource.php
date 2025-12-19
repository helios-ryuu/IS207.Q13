<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaction_code' => $this->transaction_code,
            'amount' => (float) $this->amount, // Ép kiểu số thực để không bị lỗi 0 đ
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'transaction_date' => $this->transaction_date,
            'created_at' => $this->created_at,
            'order_id' => $this->order_id,
            // Logic để Frontend phân biệt loại giao dịch dễ hơn
            'type' => $this->determineType(),
        ];
    }

    private function determineType()
    {
        // 1. Số tiền âm:
        if ($this->amount < 0) {
            // Có đơn hàng => Thanh toán mua hàng (expense)
            // Kông có đơn => Rút tiền (withdraw)
            return $this->order_id ? 'expense' : 'withdraw';
        }

        // 2. Số tiền dương:
        // Có đơn hàng => Bán hàng (income)
        // Không có đơn => Nạp tiền (deposit)
        return $this->order_id ? 'income' : 'deposit';
    }
}