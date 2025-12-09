<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentUserId = Auth::id();
        // Xác định xem "người kia" là ai
        $otherUser = $this->sender_id == $currentUserId ? $this->receiver : $this->sender;

        return [
            'partner' => [
                'id' => $otherUser->id,
                'name' => $otherUser->full_name ?? $otherUser->name,
                'avatar' => $otherUser->avatar ?? null,
            ],
            'last_message' => [
                'content' => $this->content,
                'sent_date' => $this->sent_date ? $this->sent_date->format('Y-m-d H:i:s') : null,
                'is_sender' => $this->sender_id == $currentUserId, // Để biết tin cuối là mình gửi hay họ gửi
                'status' => $this->status,
            ],
        ];
    }
}