<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category; // Đảm bảo import Model Category
use App\Models\User;     // Đảm bảo import Model User
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Dọn dẹp dữ liệu cũ
        // Chỉ cần tắt kiểm tra khóa ngoại để tránh lỗi khi insert
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // XÓA HOẶC COMMENT CÁC DÒNG NÀY ĐỂ TRÁNH LỖI "TABLE NOT FOUND":
        // DB::table('products')->truncate();
        // DB::table('product_variants')->truncate();
        // DB::table('product_images')->truncate();
        // DB::table('category_product')->truncate(); // <--- Dòng gây lỗi chính là đây

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Định nghĩa dữ liệu mẫu "Xịn" (Tiếng Việt)
        // Cấu trúc: 'Tên Danh Mục' => ['Sản phẩm 1', 'Sản phẩm 2'...]
        // Lưu ý: Tên danh mục phải khớp với CategorySeeder
        $realData = [
            'Đồ điện tử' => [
                ['name' => 'iPhone 14 Pro Max 256GB VNA', 'price' => 24500000, 'img_keyword' => 'iphone'],
                ['name' => 'Laptop MacBook Air M2 2022', 'price' => 26990000, 'img_keyword' => 'macbook'],
                ['name' => 'Tai nghe AirPods Pro 2', 'price' => 5200000, 'img_keyword' => 'headphone'],
                ['name' => 'Samsung Galaxy S23 Ultra', 'price' => 21000000, 'img_keyword' => 'samsung phone'],
                ['name' => 'Máy ảnh Sony Alpha A6400', 'price' => 18500000, 'img_keyword' => 'camera'],
                ['name' => 'Loa Bluetooth JBL Flip 6', 'price' => 2900000, 'img_keyword' => 'speaker'],
            ],
            'Xe cộ' => [
                ['name' => 'Xe máy Honda Vision 2023', 'price' => 32000000, 'img_keyword' => 'scooter'],
                ['name' => 'Yamaha Exciter 155 VVA', 'price' => 47000000, 'img_keyword' => 'motorcycle'],
                ['name' => 'Xe đạp địa hình Giant', 'price' => 8500000, 'img_keyword' => 'bicycle'],
                ['name' => 'VinFast VF e34 (Lướt)', 'price' => 550000000, 'img_keyword' => 'car'],
                ['name' => 'Honda SH 150i ABS', 'price' => 98000000, 'img_keyword' => 'scooter'],
            ],
            'Thời trang, Đồ dùng cá nhân' => [
                ['name' => 'Áo thun nam Cotton Compact', 'price' => 150000, 'img_keyword' => 't-shirt'],
                ['name' => 'Đầm hoa nhí Vintage', 'price' => 350000, 'img_keyword' => 'dress'],
                ['name' => 'Giày Nike Air Force 1', 'price' => 2800000, 'img_keyword' => 'sneakers'],
                ['name' => 'Túi xách nữ da thật', 'price' => 1200000, 'img_keyword' => 'handbag'],
                ['name' => 'Đồng hồ Casio G-Shock', 'price' => 3500000, 'img_keyword' => 'watch'],
            ],
            'Đồ gia dụng, Nội thất, Cây cảnh' => [
                ['name' => 'Sofa da bò nhập khẩu', 'price' => 15000000, 'img_keyword' => 'sofa'],
                ['name' => 'Bàn ăn gỗ sồi 6 ghế', 'price' => 8500000, 'img_keyword' => 'table'],
                ['name' => 'Cây Bàng Singapore (Cao 1m5)', 'price' => 450000, 'img_keyword' => 'plant'],
                ['name' => 'Đèn ngủ để bàn decor', 'price' => 250000, 'img_keyword' => 'lamp'],
                ['name' => 'Robot hút bụi Xiaomi', 'price' => 5600000, 'img_keyword' => 'robot vacuum'],
            ],
            'Thú cưng' => [
                ['name' => 'Chó Corgi thuần chủng', 'price' => 8000000, 'img_keyword' => 'corgi dog'],
                ['name' => 'Mèo Anh lông ngắn (xám)', 'price' => 4500000, 'img_keyword' => 'cat'],
                ['name' => 'Chuồng nuôi thú cưng inox', 'price' => 800000, 'img_keyword' => 'pet cage'],
                ['name' => 'Thức ăn cho mèo Royal Canin', 'price' => 150000, 'img_keyword' => 'cat food'],
            ]
        ];

        // 3. Tiến hành tạo dữ liệu
        foreach ($realData as $categoryName => $products) {
            // Tìm category trong DB (nếu không thấy thì bỏ qua hoặc tạo mới)
            $category = Category::where('name', 'like', "%$categoryName%")->first();
            
            if (!$category) {
                // Nếu chưa có category thì tạo tạm để không lỗi
                $category = Category::create(['name' => $categoryName, 'slug' => \Illuminate\Support\Str::slug($categoryName)]);
            }

            foreach ($products as $item) {
                // Random người bán
                $seller = User::inRandomOrder()->first() ?? User::factory()->create();

                // Tạo Product
                $product = Product::create([
                    'name' => $item['name'],
                    'description' => "Cần bán " . $item['name'] . " còn mới 98%. Hàng chính hãng, chưa qua sửa chữa. Mọi chức năng hoạt động hoàn hảo.\n\n- Bao test 7 ngày.\n- Giao dịch trực tiếp tại nhà.\n- Có fix nhẹ cho anh em thiện chí.",
                    'seller_id' => $seller->id,
                    'status' => 'active',
                ]);

                // Gắn Category
                $product->categories()->attach($category->id);

                // Tạo Variant (Giá & Số lượng)
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'price' => $item['price'],
                    'quantity' => rand(1, 10),
                    'color' => fake()->randomElement(['Đen', 'Trắng', 'Xanh', 'Đỏ', 'Bạc']),
                    'size' => fake()->randomElement(['S', 'M', 'L', 'XL', 'FreeSize']),
                ]);

                // Tạo Ảnh (Sử dụng loremflickr để lấy ảnh thật theo từ khóa)
                // LoremFlickr ổn định hơn Unsplash Source hiện tại
                for ($i = 0; $i < 3; $i++) {
                    DB::table('product_images')->insert([
                        'product_id' => $product->id,
                        'variant_id' => $variant->id,
                        // Thêm tham số lock hoặc random để các ảnh không bị giống hệt nhau
                        'image_url' => "https://loremflickr.com/640/480/" . urlencode($item['img_keyword']) . "?lock=" . ($product->id * 10 + $i),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}