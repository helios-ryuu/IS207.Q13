<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'receiver_name' => $this->receiver_name,
            'phone_number' => $this->phone_number,
            'full_address' => $this->street_address . ', ' . $this->ward . ', ' . $this->district . ', ' . $this->province,
            'street_address' => $this->street_address,
            'ward' => $this->ward,
            'district' => $this->district,
            'province' => $this->province,
            'is_default' => (bool) $this->is_default, // Trả về true/false
        ];
    }
}