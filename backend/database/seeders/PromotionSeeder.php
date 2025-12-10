<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        $promotions = [
            [
                'name' => 'Giảm 10% đơn đầu tiên',
                'description' => 'Giảm 10% cho đơn hàng đầu tiên của khách hàng mới',
                'type' => 'percentage',
                'discount_amount' => 10,
                'conditions' => 'Đơn hàng tối thiểu 100.000đ',
                'start_date' => now()->subDays(30),
                'end_date' => now()->addMonths(3),
                'status' => 'active',
                'max_usage_limit' => 1000,
                'usage_count' => 0,
            ],
            [
                'name' => 'Miễn phí vận chuyển',
                'description' => 'Miễn phí vận chuyển cho đơn từ 500k',
                'type' => 'fixed',
                'discount_amount' => 30000,
                'conditions' => 'Đơn hàng tối thiểu 500.000đ',
                'start_date' => now()->subDays(10),
                'end_date' => now()->addMonths(1),
                'status' => 'active',
                'max_usage_limit' => 500,
                'usage_count' => 0,
            ],
            [
                'name' => 'Năm mới 2025',
                'description' => 'Giảm 20% mừng năm mới 2025',
                'type' => 'percentage',
                'discount_amount' => 20,
                'conditions' => 'Đơn hàng tối thiểu 200.000đ, tối đa giảm 100.000đ',
                'start_date' => now(),
                'end_date' => now()->addDays(15),
                'status' => 'active',
                'max_usage_limit' => 200,
                'usage_count' => 0,
            ],
        ];

        foreach ($promotions as $promo) {
            $exists = DB::table('promotions')->where('name', $promo['name'])->exists();

            if (!$exists) {
                DB::table('promotions')->insert(array_merge($promo, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }
}
