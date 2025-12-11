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
        $customers = User::where('role', 'customer')->get();
        $products = Product::where('status', 'active')->get();

        if ($customers->isEmpty() || $products->isEmpty())
            return;

        $favoriteCount = 0;
        $targetCount = 30;

        foreach ($customers as $customer) {
            // Each customer favorites 3-8 products
            $numFavorites = rand(3, 8);
            $selectedProducts = $products->random(min($numFavorites, $products->count()));

            foreach ($selectedProducts as $product) {
                if ($favoriteCount >= $targetCount)
                    break 2;

                $exists = DB::table('favorites')
                    ->where('user_id', $customer->id)
                    ->where('product_id', $product->id)
                    ->exists();

                if ($exists)
                    continue;

                DB::table('favorites')->insert([
                    'user_id' => $customer->id,
                    'product_id' => $product->id,
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now()->subDays(rand(0, 7)),
                ]);

                $favoriteCount++;
            }
        }
    }
}
