<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'username' => $this->username,
            'full_name' => $this->full_name,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'gender' => $this->gender,
            'avatar' => $this->avatar, // Giả sử có cột avatar, nếu chưa có thì null
            'role' => $this->role,
            'joined_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}