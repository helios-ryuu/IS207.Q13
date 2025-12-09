<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'sent_date' => $this->sent_date ? $this->sent_date->format('Y-m-d H:i:s') : null,
            'status' => $this->status,
            // Trả về thông tin người gửi/nhận cơ bản
            'sender' => [
                'id' => $this->sender_id,
                'name' => $this->sender->full_name ?? $this->sender->name, // Lấy full_name từ User.php
                'avatar' => $this->sender->avatar ?? null, // Giả sử user có avatar
            ],
            'receiver' => [
                'id' => $this->receiver_id,
                'name' => $this->receiver->full_name ?? $this->receiver->name,
            ],
        ];
    }
}