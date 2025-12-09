<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Đảm bảo có seller
        $seller = User::where('role', 'seller')->first();
        if (!$seller) {
            $seller = User::factory()->create(['role' => 'seller']);
        }
        
        $categories = Category::all();

        // Tạo 10 sản phẩm mẫu
        for ($i = 0; $i < 10; $i++) {
            $product = Product::create([
                'name' => 'Sample Product ' . ($i + 1),
                'description' => 'This is a description for product ' . ($i + 1),
                'status' => 'active',
                'seller_id' => $seller->id,
            ]);

            // Attach random category
            if ($categories->count() > 0) {
                $product->categories()->attach($categories->random()->id);
            }

            // Create variants
            ProductVariant::create([
                'product_id' => $product->id,
                'color' => 'Red',
                'size' => 'M',
                'quantity' => 100,
                'price' => rand(100, 1000) * 1000,
                'status' => 'active',
            ]);
            
            ProductVariant::create([
                'product_id' => $product->id,
                'color' => 'Blue',
                'size' => 'L',
                'quantity' => 50,
                'price' => rand(100, 1000) * 1000,
                'status' => 'active',
            ]);
        }
    }
}