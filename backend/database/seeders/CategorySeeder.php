<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Original 6
            'Điện tử',
            'Thời trang',
            'Xe cộ',
            'Nội thất',
            'Thú cưng',
            'Bất động sản',
            // New 6
            'Làm đẹp',
            'Sách & Văn phòng phẩm',
            'Đồ chơi',
            'Thể thao',
            'Công nghệ',
            'Mẹ & Bé',
        ];

        foreach ($categories as $cat) {
            if (!Category::where('name', $cat)->exists()) {
                Category::create(['name' => $cat]);
            }
        }
    }
}