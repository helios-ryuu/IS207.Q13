<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $buyer = User::where('email', 'buyer@example.com')->first();

        if (!$buyer)
            return;

        // Lấy 2 variants ngẫu nhiên để thêm vào giỏ hàng
        $variants = ProductVariant::with('product')
            ->where('status', 'active')
            ->where('quantity', '>', 0)
            ->inRandomOrder()
            ->take(2)
            ->get();

        foreach ($variants as $variant) {
            $exists = DB::table('cart_items')
                ->where('user_id', $buyer->id)
                ->where('variant_id', $variant->id)
                ->exists();

            if ($exists)
                continue;

            DB::table('cart_items')->insert([
                'user_id' => $buyer->id,
                'variant_id' => $variant->id,
                'quantity' => rand(1, 3),
                'created_at' => now()->subHours(rand(1, 48)),
                'updated_at' => now()->subHours(rand(1, 24)),
            ]);
        }
    }
}
