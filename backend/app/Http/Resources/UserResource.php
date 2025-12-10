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
            'avatar' => $this->avatar,
            'avatar_url' => $this->avatar_url, // Full URL from accessor
            'bio' => $this->bio,
            'website' => $this->website,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'twitter' => $this->twitter,
            'role' => $this->role,
            'joined_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}