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
            'balance' => (float) $this->balance, // Quan trọng: ép kiểu float
            'status' => $this->status,
            'user_id' => $this->user_id,
        ];
    }
}