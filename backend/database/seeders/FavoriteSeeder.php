<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FavoriteSeeder extends Seeder
{
    public function run(): void
    {
        $buyer = User::where('email', 'buyer@example.com')->first();

        if (!$buyer)
            return;

        // Lấy 5 sản phẩm ngẫu nhiên để thêm vào yêu thích
        $products = Product::inRandomOrder()->take(5)->get();

        foreach ($products as $product) {
            $exists = DB::table('favorites')
                ->where('user_id', $buyer->id)
                ->where('product_id', $product->id)
                ->exists();

            if ($exists)
                continue;

            DB::table('favorites')->insert([
                'user_id' => $buyer->id,
                'product_id' => $product->id,
                'created_at' => now()->subDays(rand(1, 14)),
                'updated_at' => now()->subDays(rand(1, 7)),
            ]);
        }
    }
}
