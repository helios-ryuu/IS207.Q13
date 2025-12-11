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
        $customers = User::where('role', 'customer')->get();
        $products = Product::with('seller')->where('status', 'active')->get();

        if ($customers->isEmpty() || $products->isEmpty())
            return;

        $reviewContents = [
            ['rating' => 5, 'content' => 'Sản phẩm rất tốt, đóng gói cẩn thận, giao hàng nhanh. Sẽ ủng hộ shop tiếp.'],
            ['rating' => 5, 'content' => 'Tuyệt vời! Đúng như mô tả, shop phản hồi nhanh.'],
            ['rating' => 5, 'content' => 'Hàng chính hãng, chất lượng cao, giá tốt. Rất recommend!'],
            ['rating' => 4, 'content' => 'Chất lượng tốt so với giá tiền. Có một vài điểm nhỏ cần cải thiện nhưng nhìn chung hài lòng.'],
            ['rating' => 4, 'content' => 'Đã mua lần 2, shop uy tín, hàng chính hãng.'],
            ['rating' => 4, 'content' => 'Sản phẩm đẹp, chất lượng ổn, sẽ quay lại mua tiếp.'],
            ['rating' => 3, 'content' => 'Sản phẩm tạm ổn, giao hàng hơi chậm một chút.'],
            ['rating' => 3, 'content' => 'Được, nhưng đóng gói cần cẩn thận hơn.'],
            ['rating' => 2, 'content' => 'Hàng không như mô tả, hơi thất vọng.'],
            ['rating' => 1, 'content' => 'Giao sai hàng, liên hệ shop mãi không trả lời.'],
        ];

        $reviewCount = 0;
        $targetCount = 35;

        foreach ($customers as $customer) {
            // Each customer reviews 3-8 products
            $numReviews = rand(3, 8);
            $selectedProducts = $products->random(min($numReviews, $products->count()));

            foreach ($selectedProducts as $product) {
                if ($reviewCount >= $targetCount)
                    break 2;

                $exists = DB::table('reviews')
                    ->where('user_id', $customer->id)
                    ->where('product_id', $product->id)
                    ->exists();

                if ($exists)
                    continue;

                $reviewData = $reviewContents[array_rand($reviewContents)];
                $createdDays = rand(1, 45);

                DB::table('reviews')->insert([
                    'user_id' => $customer->id,
                    'product_id' => $product->id,
                    'rating' => $reviewData['rating'],
                    'content' => $reviewData['content'],
                    'review_date' => now()->subDays($createdDays),
                    'created_at' => now()->subDays($createdDays),
                    'updated_at' => now()->subDays(max(0, $createdDays - rand(0, 5))),
                ]);

                $reviewCount++;
            }
        }

        // Seed seller_reviews from delivered orders
        $this->seedSellerReviews($customers);
    }

    private function seedSellerReviews($customers): void
    {
        $sellerReviewContents = [
            ['rating' => 5, 'response_time' => 5, 'shipping_quality' => 5, 'content' => 'Shop rất uy tín, sản phẩm chất lượng, giá cả hợp lý. Rất recommend!'],
            ['rating' => 5, 'response_time' => 5, 'shipping_quality' => 4, 'content' => 'Shop phản hồi nhanh, đóng gói cẩn thận, sẽ mua tiếp.'],
            ['rating' => 4, 'response_time' => 4, 'shipping_quality' => 5, 'content' => 'Giao hàng nhanh, sản phẩm đúng mô tả. Tốt!'],
            ['rating' => 4, 'response_time' => 3, 'shipping_quality' => 4, 'content' => 'Shop ổn, nhưng phản hồi hơi chậm.'],
            ['rating' => 3, 'response_time' => 2, 'shipping_quality' => 3, 'content' => 'Trung bình, cần cải thiện thời gian ship.'],
        ];

        $targetCount = 15;
        $reviewCount = 0;

        foreach ($customers as $customer) {
            $deliveredOrders = DB::table('orders')
                ->where('user_id', $customer->id)
                ->where('status', 'delivered')
                ->get();

            foreach ($deliveredOrders as $order) {
                if ($reviewCount >= $targetCount)
                    break 2;

                // Get seller from order details
                $orderDetail = DB::table('order_details')
                    ->join('product_variants', 'order_details.variant_id', '=', 'product_variants.id')
                    ->join('products', 'product_variants.product_id', '=', 'products.id')
                    ->where('order_details.order_id', $order->id)
                    ->select('products.seller_id')
                    ->first();

                if (!$orderDetail)
                    continue;

                $exists = DB::table('seller_reviews')
                    ->where('user_id', $customer->id)
                    ->where('order_id', $order->id)
                    ->exists();

                if ($exists)
                    continue;

                $reviewData = $sellerReviewContents[array_rand($sellerReviewContents)];
                $createdDays = rand(1, 20);

                DB::table('seller_reviews')->insert([
                    'user_id' => $customer->id,
                    'seller_id' => $orderDetail->seller_id,
                    'order_id' => $order->id,
                    'rating' => $reviewData['rating'],
                    'content' => $reviewData['content'],
                    'response_time' => $reviewData['response_time'],
                    'shipping_quality' => $reviewData['shipping_quality'],
                    'review_date' => now()->subDays($createdDays),
                    'created_at' => now()->subDays($createdDays),
                    'updated_at' => now()->subDays($createdDays),
                ]);

                $reviewCount++;
            }
        }
    }
}
