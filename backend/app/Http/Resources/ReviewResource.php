<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'content' => $this->content,
            'review_date' => $this->review_date->format('Y-m-d'),
            'reviewer' => [
                'name' => $this->user->full_name ?? $this->user->name,
                'avatar' => $this->user->avatar ?? null,
            ],
        ];
    }
}