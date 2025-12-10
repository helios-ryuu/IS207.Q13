<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Điện tử',
            'Thời trang',
            'Xe cộ',
            'Nội thất',
            'Thú cưng',
            'Bất động sản'
        ];

        foreach ($categories as $cat) {
            // Check if exists to avoid unique constraint error
            if (!Category::where('name', $cat)->exists()) {
                Category::create(['name' => $cat]);
            }
        }
    }
}