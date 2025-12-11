<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductPostSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::with('seller')->where('status', 'active')->get();

        if ($products->isEmpty())
            return;

        $postCount = 0;
        $targetCount = 20;

        foreach ($products as $product) {
            if ($postCount >= $targetCount)
                break;

            $exists = DB::table('product_posts')
                ->where('product_id', $product->id)
                ->exists();

            if ($exists)
                continue;

            // Valid statuses: draft, published, hidden, rejected
            $statuses = ['published', 'published', 'published', 'draft', 'hidden'];
            $createdDays = rand(1, 30);

            DB::table('product_posts')->insert([
                'title' => 'Bán ' . $product->name,
                'description' => 'Sản phẩm ' . $product->name . ' chất lượng cao, giá tốt. Liên hệ shop để được tư vấn chi tiết.',
                'posted_date' => now()->subDays($createdDays),
                'status' => $statuses[array_rand($statuses)],
                'product_id' => $product->id,
                'seller_id' => $product->seller_id,
                'created_at' => now()->subDays($createdDays),
                'updated_at' => now()->subDays(max(0, $createdDays - rand(0, 5))),
            ]);

            $postCount++;
        }
    }
}
