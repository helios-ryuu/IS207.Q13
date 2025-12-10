<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $buyer = User::where('email', 'buyer@example.com')->first();
        $seller = User::where('email', 'seller@example.com')->first();

        if (!$buyer || !$seller)
            return;

        // Lấy các sản phẩm của seller
        $products = Product::where('seller_id', $seller->id)->take(5)->get();

        // Tạo reviews cho sản phẩm
        $reviewContents = [
            ['rating' => 5, 'content' => 'Sản phẩm rất tốt, đóng gói cẩn thận, giao hàng nhanh. Sẽ ủng hộ shop tiếp.'],
            ['rating' => 4, 'content' => 'Chất lượng tốt so với giá tiền. Có một vài điểm nhỏ cần cải thiện nhưng nhìn chung hài lòng.'],
            ['rating' => 5, 'content' => 'Tuyệt vời! Đúng như mô tả, shop phản hồi nhanh.'],
            ['rating' => 3, 'content' => 'Sản phẩm tạm ổn, giao hàng hơi chậm một chút.'],
            ['rating' => 4, 'content' => 'Đã mua lần 2, shop uy tín, hàng chính hãng.'],
        ];

        foreach ($products as $index => $product) {
            $reviewData = $reviewContents[$index % count($reviewContents)];

            $exists = DB::table('reviews')
                ->where('user_id', $buyer->id)
                ->where('product_id', $product->id)
                ->exists();

            if ($exists)
                continue;

            DB::table('reviews')->insert([
                'user_id' => $buyer->id,
                'product_id' => $product->id,
                'rating' => $reviewData['rating'],
                'content' => $reviewData['content'],
                'review_date' => now()->subDays(rand(1, 30)),
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(1, 15)),
            ]);
        }

        // Lấy order đã delivered để tạo seller review (cần order_id)
        $deliveredOrder = DB::table('orders')
            ->where('user_id', $buyer->id)
            ->where('status', 'delivered')
            ->first();

        if ($deliveredOrder) {
            $exists = DB::table('seller_reviews')
                ->where('user_id', $buyer->id)
                ->where('seller_id', $seller->id)
                ->where('order_id', $deliveredOrder->id)
                ->exists();

            if (!$exists) {
                DB::table('seller_reviews')->insert([
                    'user_id' => $buyer->id,
                    'seller_id' => $seller->id,
                    'order_id' => $deliveredOrder->id,
                    'rating' => 5,
                    'content' => 'Shop rất uy tín, sản phẩm chất lượng, giá cả hợp lý. Rất recommend!',
                    'response_time' => 5,
                    'shipping_quality' => 5,
                    'review_date' => now()->subDays(5),
                    'created_at' => now()->subDays(5),
                    'updated_at' => now()->subDays(5),
                ]);
            }
        }
    }
}
