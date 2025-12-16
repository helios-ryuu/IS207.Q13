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
                        'url' => $img->full_image_url // Sử dụng accessor để xử lý cả GCS và local
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
            'is_favorited' => $isFavorited,

            'images' => $allImages,
            'thumbnail' => $allImages->first()['url'] ?? null,

            // Thêm variants để frontend có thể lấy price và các thông tin khác
            'variants' => $variants->map(function ($v) {
                return [
                    'id' => $v->id,
                    'price' => $v->price,
                    'quantity' => $v->quantity ?? 1,
                    'color' => $v->color ?? null,
                    'size' => $v->size ?? null,
                ];
            }),

            'seller' => [
                'id' => $this->seller_id,
                'name' => $this->seller->full_name ?? $this->seller->name ?? 'Unknown',
                'username' => $this->seller->username ?? null,
                'avatar' => $this->seller->avatar_url ?? null, // Sử dụng accessor để lấy URL đầy đủ
            ],

            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}