<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Xóa dữ liệu cũ để tránh trùng lặp
        DB::table('categories')->delete();

        $categories = [
            ['name' => 'Đồ điện tử', 'slug' => 'do-dien-tu', 'icon' => 'laptop'],
            ['name' => 'Xe cộ', 'slug' => 'xe-co', 'icon' => 'car'],
            ['name' => 'Đồ gia dụng, Nội thất, Cây cảnh', 'slug' => 'noi-that', 'icon' => 'couch'],
            ['name' => 'Tủ lạnh, Máy lạnh, Máy giặt', 'slug' => 'dien-lanh', 'icon' => 'snowflake'],
            ['name' => 'Thời trang, Đồ dùng cá nhân', 'slug' => 'thoi-trang', 'icon' => 'tshirt'],
            ['name' => 'Giải trí, Thể thao, Sở thích', 'slug' => 'the-thao', 'icon' => 'futbol'],
            ['name' => 'Thú cưng', 'slug' => 'thu-cung', 'icon' => 'paw'],
            ['name' => 'Đồ dùng văn phòng, Công nông nghiệp', 'slug' => 'van-phong', 'icon' => 'briefcase'],
            ['name' => 'Đồ ăn, Thực phẩm và các loại khác', 'slug' => 'thuc-pham', 'icon' => 'utensils'],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->insert([
                'name' => $cat['name'],
                'slug' => $cat['slug'], // Giả sử bảng categories có cột slug, nếu không có thì bỏ dòng này
                // 'icon' => $cat['icon'], // Nếu bảng có cột icon
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}