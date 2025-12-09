<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Electronics', 'Fashion', 'Home & Garden', 'Toys', 'Books'];
        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat]);
        }
    }
}