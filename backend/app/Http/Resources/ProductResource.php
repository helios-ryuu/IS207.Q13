<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Tính toán khoảng giá hiển thị
        $minPrice = $this->variants->min('price');
        $maxPrice = $this->variants->max('price');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'price_range' => [
                'min' => $minPrice,
                'max' => $maxPrice,
            ],
            'thumbnail' => $this->variants->first()?->images->first()?->image_url ?? null,
            'seller' => [
                'id' => $this->seller_id,
                'name' => $this->seller->full_name ?? $this->seller->name ?? 'Unknown',
            ],
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'variants' => $this->whenLoaded('variants', function() {
                return $this->variants->map(function($variant) {
                    return [
                        'id' => $variant->id,
                        'color' => $variant->color,
                        'size' => $variant->size,
                        'price' => $variant->price,
                        'quantity' => $variant->quantity,
                        'images' => $variant->images->pluck('image_url'),
                    ];
                });
            }),
            'created_at' => $this->created_at,
        ];
    }
}