<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'balance' => $this->calculated_balance, // Tính từ transactions
            'stored_balance' => (float) $this->balance, // Giá trị lưu trong DB (backup)
            'status' => $this->status,
            'user_id' => $this->user_id,
        ];
    }
}