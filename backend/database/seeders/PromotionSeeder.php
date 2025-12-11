<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        $promotions = [
            // Original 3
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
            // New 7
            [
                'name' => 'Giảm 50K đơn 300K',
                'description' => 'Giảm ngay 50.000đ cho đơn hàng từ 300.000đ',
                'type' => 'fixed',
                'discount_amount' => 50000,
                'conditions' => 'Đơn hàng tối thiểu 300.000đ',
                'start_date' => now()->subDays(5),
                'end_date' => now()->addMonths(2),
                'status' => 'active',
                'max_usage_limit' => 300,
                'usage_count' => 25,
            ],
            [
                'name' => 'Flash Sale 15%',
                'description' => 'Giảm 15% tất cả sản phẩm điện tử',
                'type' => 'percentage',
                'discount_amount' => 15,
                'conditions' => 'Áp dụng cho danh mục Điện tử',
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(5),
                'status' => 'active',
                'max_usage_limit' => 100,
                'usage_count' => 10,
            ],
            [
                'name' => 'Sinh nhật VietMarket',
                'description' => 'Giảm 25% mừng sinh nhật VietMarket',
                'type' => 'percentage',
                'discount_amount' => 25,
                'conditions' => 'Đơn hàng tối thiểu 500.000đ, tối đa giảm 200.000đ',
                'start_date' => now()->addDays(10),
                'end_date' => now()->addDays(17),
                'status' => 'active',
                'max_usage_limit' => 500,
                'usage_count' => 0,
            ],
            [
                'name' => 'Thành viên mới',
                'description' => 'Ưu đãi đặc biệt cho thành viên mới đăng ký',
                'type' => 'fixed',
                'discount_amount' => 20000,
                'conditions' => 'Đơn hàng đầu tiên, không giới hạn giá trị',
                'start_date' => now()->subMonths(1),
                'end_date' => now()->addMonths(6),
                'status' => 'active',
                'max_usage_limit' => 2000,
                'usage_count' => 150,
            ],
            [
                'name' => 'Cuối tuần vui vẻ',
                'description' => 'Giảm 5% mọi đơn hàng vào cuối tuần',
                'type' => 'percentage',
                'discount_amount' => 5,
                'conditions' => 'Chỉ áp dụng Thứ 7 và Chủ nhật',
                'start_date' => now()->subDays(14),
                'end_date' => now()->addMonths(3),
                'status' => 'active',
                'max_usage_limit' => 1000,
                'usage_count' => 80,
            ],
            [
                'name' => 'VIP Member',
                'description' => 'Giảm 30% cho thành viên VIP',
                'type' => 'percentage',
                'discount_amount' => 30,
                'conditions' => 'Dành riêng cho thành viên VIP, tối đa giảm 500.000đ',
                'start_date' => now()->subMonths(2),
                'end_date' => now()->addMonths(12),
                'status' => 'active',
                'max_usage_limit' => 100,
                'usage_count' => 5,
            ],
            [
                'name' => 'Hết hạn - Thử nghiệm',
                'description' => 'Mã giảm giá đã hết hạn',
                'type' => 'fixed',
                'discount_amount' => 100000,
                'conditions' => 'Đã hết hạn',
                'start_date' => now()->subMonths(3),
                'end_date' => now()->subDays(10),
                'status' => 'inactive',
                'max_usage_limit' => 50,
                'usage_count' => 50,
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
