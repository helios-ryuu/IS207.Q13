<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // 1. Xử lý Variants (Kiểm tra nếu variants null thì trả về collection rỗng)
        $variants = $this->relationLoaded('variants') ? $this->variants : collect();
        
        $minPrice = $variants->min('price') ?? 0;
        $maxPrice = $variants->max('price') ?? 0;

        // 2. Xử lý Ảnh (An toàn)
        $allImages = $variants->flatMap(function ($variant) {
            // Kiểm tra relation images có tồn tại không
            if ($variant->relationLoaded('images')) {
                return $variant->images->map(function ($img) {
                    return [
                        'id' => $img->id,
                        'url' => $img->image_url
                    ];
                });
            }
            return [];
        })->values();

        // 3. Xử lý Favorite (Kiểm tra Auth an toàn)
        $isFavorited = false;
        try {
            // Chỉ check nếu user đăng nhập VÀ model Product có hàm favoritedBy
            if (Auth::guard('sanctum')->check() && method_exists($this->resource, 'favoritedBy')) {
                $isFavorited = $this->favoritedBy()
                    ->where('user_id', Auth::guard('sanctum')->id())
                    ->exists();
            }
        } catch (\Exception $e) {
            // Bỏ qua lỗi nếu bảng favorites chưa setup đúng
            $isFavorited = false;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status ?? 'active',
            'price_range' => [
                'min' => $minPrice,
                'max' => $maxPrice,
            ],
            'is_favorited' => $isFavorited, // <--- Đã xử lý an toàn
            
            'images' => $allImages, // <--- Trả về mảng ảnh

            'thumbnail' => $allImages->first()['url'] ?? null, // Lấy ảnh đầu tiên làm thumbnail

            'seller' => [
                'id' => $this->seller_id,
                'name' => $this->seller->full_name ?? $this->seller->name ?? 'Unknown',
                'avatar' => $this->seller->avatar ?? null,
            ],
            
            // ... (Phần còn lại giữ nguyên)
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'created_at' => $this->created_at,
        ];
    }
}