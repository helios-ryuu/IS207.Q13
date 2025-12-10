<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $seller = User::where('email', 'seller@example.com')->first();
        if (!$seller)
            return;

        $elecCat = Category::where('name', 'Điện tử')->first();
        $fashionCat = Category::where('name', 'Thời trang')->first();

        // Product 1: iPhone
        if ($elecCat && !Product::where('name', 'iPhone 15 Pro Max 256GB')->exists()) {
            $p1 = Product::create([
                'seller_id' => $seller->id,
                'name' => 'iPhone 15 Pro Max 256GB',
                'description' => 'Máy mới 99%, pin 100%, còn bảo hành Apple Care.',
                'status' => 'active',
            ]);

            // Pivot category
            DB::table('product_categories')->insert([
                'product_id' => $p1->id,
                'category_id' => $elecCat->id,
                'created_at' => now(),
            ]);

            // Variant (Price here) with image
            $v1 = ProductVariant::create([
                'product_id' => $p1->id,
                'color' => 'Titan Tự Nhiên',
                'size' => '256GB',
                'price' => 25000000,
                'quantity' => 2,
                'status' => 'active',
            ]);

            // Image linked to variant
            DB::table('product_images')->insert([
                'variant_id' => $v1->id,
                'image_url' => 'https://via.placeholder.com/600x400/eeeeee/cccccc?text=iPhone+15+Pro+Max',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create 20 random products
        for ($i = 1; $i <= 20; $i++) {
            $cat = ($i % 2 == 0) ? $elecCat : $fashionCat;
            if (!$cat)
                continue;

            $pName = "Sản phẩm Demo $i";
            if (Product::where('name', $pName)->exists())
                continue;

            $p = Product::create([
                'seller_id' => $seller->id,
                'name' => $pName,
                'description' => "Mô tả chi tiết cho sản phẩm $i. Hàng chất lượng cao.",
                'status' => 'active',
            ]);

            DB::table('product_categories')->insert([
                'product_id' => $p->id,
                'category_id' => $cat->id,
                'created_at' => now(),
            ]);

            $variant = ProductVariant::create([
                'product_id' => $p->id,
                'color' => 'Màu Ngẫu Nhiên',
                'size' => 'Standard',
                'price' => 100000 * $i,
                'quantity' => 10,
                'status' => 'active',
            ]);

            // Image linked to variant
            DB::table('product_images')->insert([
                'variant_id' => $variant->id,
                'image_url' => "https://via.placeholder.com/600x400/eeeeee/cccccc?text=Product+$i",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}