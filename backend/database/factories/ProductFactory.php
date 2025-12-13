<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(4), // Tên sản phẩm giả
            'description' => $this->faker->paragraph(5), // Mô tả giả
            'seller_id' => User::inRandomOrder()->first()->id ?? 1, // Lấy random user làm người bán
            'status' => 'active',
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}