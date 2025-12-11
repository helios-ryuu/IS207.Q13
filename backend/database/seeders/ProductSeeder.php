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
        $sellers = User::where('role', 'seller')->get();
        $categories = Category::all();

        if ($sellers->isEmpty() || $categories->isEmpty())
            return;

        $productTemplates = [
            // Điện tử
            ['name' => 'iPhone 15 Pro Max 256GB', 'cat' => 'Điện tử', 'price' => 25000000, 'desc' => 'Máy mới 99%, pin 100%, còn bảo hành Apple Care.'],
            ['name' => 'Samsung Galaxy S24 Ultra', 'cat' => 'Điện tử', 'price' => 28000000, 'desc' => 'Fullbox, chưa active, bảo hành 12 tháng.'],
            ['name' => 'MacBook Pro M3 14 inch', 'cat' => 'Điện tử', 'price' => 45000000, 'desc' => 'Ram 16GB, SSD 512GB, mới 100%.'],
            ['name' => 'iPad Pro 12.9 M2', 'cat' => 'Điện tử', 'price' => 25000000, 'desc' => 'Wifi + Cellular, 256GB, đen.'],
            ['name' => 'AirPods Pro 2', 'cat' => 'Điện tử', 'price' => 4500000, 'desc' => 'Mới 100%, seal nguyên hộp.'],
            ['name' => 'Apple Watch Series 9', 'cat' => 'Điện tử', 'price' => 9500000, 'desc' => '45mm, GPS + Cellular, đen.'],

            // Thời trang
            ['name' => 'Áo thun Uniqlo Premium', 'cat' => 'Thời trang', 'price' => 350000, 'desc' => 'Chất liệu cotton cao cấp, nhiều màu sắc.'],
            ['name' => 'Quần jeans Levis 501', 'cat' => 'Thời trang', 'price' => 1200000, 'desc' => 'Original fit, xanh đậm, size 28-36.'],
            ['name' => 'Giày Nike Air Max 97', 'cat' => 'Thời trang', 'price' => 3500000, 'desc' => 'Authentic, đủ size, freeship.'],
            ['name' => 'Túi xách LV Neverfull', 'cat' => 'Thời trang', 'price' => 35000000, 'desc' => 'Like new 99%, fullbox, có hóa đơn.'],
            ['name' => 'Áo khoác da Zara', 'cat' => 'Thời trang', 'price' => 2500000, 'desc' => 'Da thật, size M/L, đen klasik.'],

            // Xe cộ
            ['name' => 'Honda SH 125i 2023', 'cat' => 'Xe cộ', 'price' => 75000000, 'desc' => 'Đã đi 5000km, biển đẹp, full giấy tờ.'],
            ['name' => 'Vespa Sprint 125', 'cat' => 'Xe cộ', 'price' => 65000000, 'desc' => 'Màu xanh, đời 2022, bảo dưỡng định kỳ.'],
            ['name' => 'Yamaha Exciter 155 VVA', 'cat' => 'Xe cộ', 'price' => 48000000, 'desc' => 'Mới 100%, đủ màu, trả góp 0%.'],
            ['name' => 'Honda Wave Alpha', 'cat' => 'Xe cộ', 'price' => 18000000, 'desc' => 'Xe gia đình, ít sử dụng, tiết kiệm xăng.'],

            // Nội thất
            ['name' => 'Bàn làm việc gỗ óc chó', 'cat' => 'Nội thất', 'price' => 8500000, 'desc' => 'Kích thước 120x60cm, chân sắt sơn tĩnh điện.'],
            ['name' => 'Ghế công thái học Ergohuman', 'cat' => 'Nội thất', 'price' => 12000000, 'desc' => 'Hàng chính hãng, bảo hành 5 năm.'],
            ['name' => 'Sofa góc L da bò', 'cat' => 'Nội thất', 'price' => 25000000, 'desc' => '3.2m x 1.8m, màu nâu, kèm ghế đơn.'],
            ['name' => 'Giường ngủ 1m8 nhập khẩu', 'cat' => 'Nội thất', 'price' => 18000000, 'desc' => 'Gỗ sồi tự nhiên, kèm đệm Tatana.'],

            // Thú cưng
            ['name' => 'Chó Poodle Tiny 2 tháng', 'cat' => 'Thú cưng', 'price' => 8000000, 'desc' => 'Đã tiêm 2 mũi, có sổ tiêm phòng.'],
            ['name' => 'Mèo Anh lông ngắn', 'cat' => 'Thú cưng', 'price' => 5000000, 'desc' => 'Màu xám, đực, 3 tháng tuổi.'],
            ['name' => 'Bể cá cảnh 1m2', 'cat' => 'Thú cưng', 'price' => 3500000, 'desc' => 'Kính cường lực, full phụ kiện, có sẵn cá.'],
            ['name' => 'Lồng chim hoàng yến', 'cat' => 'Thú cưng', 'price' => 1500000, 'desc' => 'Lồng tre cao cấp, kèm 2 chim.'],

            // Làm đẹp
            ['name' => 'Son MAC Retro Matte', 'cat' => 'Làm đẹp', 'price' => 550000, 'desc' => 'Hàng chính hãng, nhiều màu hot.'],
            ['name' => 'Kem dưỡng La Mer', 'cat' => 'Làm đẹp', 'price' => 8500000, 'desc' => 'Creme de la Mer 60ml, nguyên seal.'],
            ['name' => 'Nước hoa Chanel No.5', 'cat' => 'Làm đẹp', 'price' => 3500000, 'desc' => 'Eau de Parfum 100ml, hàng Pháp.'],

            // Sách & Văn phòng
            ['name' => 'Đắc Nhân Tâm - Dale Carnegie', 'cat' => 'Sách & Văn phòng phẩm', 'price' => 85000, 'desc' => 'Bản dịch mới nhất, bìa cứng.'],
            ['name' => 'Bút Parker Jotter', 'cat' => 'Sách & Văn phòng phẩm', 'price' => 450000, 'desc' => 'Bút bi cao cấp, thép không gỉ.'],
            ['name' => 'Máy in Canon LBP6030', 'cat' => 'Sách & Văn phòng phẩm', 'price' => 2800000, 'desc' => 'Laser trắng đen, mới 100%.'],

            // Đồ chơi
            ['name' => 'LEGO Star Wars 75192', 'cat' => 'Đồ chơi', 'price' => 15000000, 'desc' => 'Millennium Falcon UCS, nguyên seal.'],
            ['name' => 'Xe điều khiển Traxxas', 'cat' => 'Đồ chơi', 'price' => 8500000, 'desc' => 'Tỷ lệ 1/10, brushless motor.'],

            // Thể thao
            ['name' => 'Vợt tennis Wilson Pro', 'cat' => 'Thể thao', 'price' => 4500000, 'desc' => 'Sợi carbon, 300g, grip size 2.'],
            ['name' => 'Máy chạy bộ Elip Sport', 'cat' => 'Thể thao', 'price' => 12000000, 'desc' => '2.5HP, đai chạy 50cm, gấp gọn.'],
            ['name' => 'Xe đạp Giant Escape', 'cat' => 'Thể thao', 'price' => 8500000, 'desc' => 'Size M, khung nhôm 6061.'],

            // Công nghệ
            ['name' => 'Camera Sony A7IV', 'cat' => 'Công nghệ', 'price' => 55000000, 'desc' => 'Body only, like new, shutter 5k.'],
            ['name' => 'Drone DJI Mini 3 Pro', 'cat' => 'Công nghệ', 'price' => 22000000, 'desc' => 'Fly More Combo, nguyên seal.'],
            ['name' => 'Loa Marshall Stanmore II', 'cat' => 'Công nghệ', 'price' => 8500000, 'desc' => 'Bluetooth, màu đen classic.'],

            // Mẹ & Bé
            ['name' => 'Xe đẩy Combi F2', 'cat' => 'Mẹ & Bé', 'price' => 4500000, 'desc' => 'Như mới, siêu nhẹ 3.6kg.'],
            ['name' => 'Nôi điện Autoru', 'cat' => 'Mẹ & Bé', 'price' => 2500000, 'desc' => 'Tự động đung đưa, phát nhạc.'],
            ['name' => 'Ghế ăn dặm Joie', 'cat' => 'Mẹ & Bé', 'price' => 3500000, 'desc' => 'Mimzy LX, 6 tháng - 3 tuổi.'],
        ];

        $colors = ['Đen', 'Trắng', 'Xám', 'Xanh', 'Đỏ', 'Vàng', 'Tím', 'Hồng', 'Nâu', 'Bạc'];
        $sizes = ['S', 'M', 'L', 'XL', 'Standard', 'Free Size', '256GB', '512GB', '1TB'];

        foreach ($productTemplates as $index => $template) {
            $cat = $categories->where('name', $template['cat'])->first();
            if (!$cat)
                continue;

            $seller = $sellers[$index % count($sellers)];

            if (Product::where('name', $template['name'])->exists())
                continue;

            $product = Product::create([
                'seller_id' => $seller->id,
                'name' => $template['name'],
                'description' => $template['desc'],
                'status' => 'active',
            ]);

            // Pivot category
            DB::table('product_categories')->insert([
                'product_id' => $product->id,
                'category_id' => $cat->id,
                'created_at' => now(),
            ]);

            // Create 1-2 variants per product
            $numVariants = rand(1, 2);
            for ($v = 0; $v < $numVariants; $v++) {
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'color' => $colors[array_rand($colors)],
                    'size' => $sizes[array_rand($sizes)],
                    'price' => $template['price'] * (1 + $v * 0.1), // +10% for second variant
                    'quantity' => rand(5, 50),
                    'status' => 'active',
                ]);

                // Add image
                DB::table('product_images')->insert([
                    'variant_id' => $variant->id,
                    'image_url' => "https://via.placeholder.com/600x400/eeeeee/cccccc?text=" . urlencode(substr($template['name'], 0, 20)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}