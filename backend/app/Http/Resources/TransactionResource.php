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
        // Nếu số tiền âm => Rút tiền
        if ($this->amount < 0) return 'withdraw';
        
        // Nếu số tiền dương:
        // - Có order_id => Bán hàng
        // - Không có order_id => Nạp tiền
        return $this->order_id ? 'income' : 'deposit';
    }
}