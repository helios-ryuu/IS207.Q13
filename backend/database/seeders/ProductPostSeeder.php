<?php

namespace Database\Seeders;

use App\Models\ProductPost;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductPostSeeder extends Seeder
{
    public function run(): void
    {
        $seller = User::where('role', 'seller')->first();
        
        if (!$seller) {
            $this->command->warn('No seller found. Skipping ProductPost seeding.');
            return;
        }

        $products = Product::where('seller_id', $seller->id)->take(5)->get();

        if ($products->isEmpty()) {
            $this->command->warn('No products found. Skipping ProductPost seeding.');
            return;
        }

        foreach ($products as $product) {
            ProductPost::create([
                'title' => 'Bán ' . $product->name,
                'description' => 'Sản phẩm ' . $product->name . ' chất lượng cao, giá tốt.',
                'posted_date' => now(),
                'status' => 'draft',
                'product_id' => $product->id,
                'seller_id' => $seller->id,
            ]);
        }

        $this->command->info('Product posts seeded successfully!');
    }
}
