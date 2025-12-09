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
            'order_id' => $this->order_id, // Giao dịch này trả cho đơn hàng nào
            'amount' => number_format($this->amount) . ' VNĐ',
            'payment_method' => $this->payment_method,
            'payment_gateway' => $this->payment_gateway,
            'status' => $this->status, // pending, completed, failed
            'date' => $this->transaction_date ? $this->transaction_date->format('d/m/Y H:i') : null,
        ];
    }
}