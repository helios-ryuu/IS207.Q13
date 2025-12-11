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
        $customers = User::where('role', 'customer')->get();
        $variants = ProductVariant::where('status', 'active')
            ->where('quantity', '>', 0)
            ->get();

        if ($customers->isEmpty() || $variants->isEmpty())
            return;

        $cartCount = 0;
        $targetCount = 15;

        foreach ($customers as $customer) {
            // Each customer has 1-4 items in cart
            $numItems = rand(1, 4);
            $selectedVariants = $variants->random(min($numItems, $variants->count()));

            foreach ($selectedVariants as $variant) {
                if ($cartCount >= $targetCount)
                    break 2;

                $exists = DB::table('cart_items')
                    ->where('user_id', $customer->id)
                    ->where('variant_id', $variant->id)
                    ->exists();

                if ($exists)
                    continue;

                DB::table('cart_items')->insert([
                    'user_id' => $customer->id,
                    'variant_id' => $variant->id,
                    'quantity' => rand(1, 3),
                    'created_at' => now()->subHours(rand(1, 168)),
                    'updated_at' => now()->subHours(rand(1, 48)),
                ]);

                $cartCount++;
            }
        }
    }
}
