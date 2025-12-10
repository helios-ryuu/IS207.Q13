<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Đẩy tin lên đầu',
                'description' => 'Tin đăng của bạn sẽ được đẩy lên đầu danh sách trong 24 giờ',
                'price' => 10000,
            ],
            [
                'name' => 'Tin VIP 3 ngày',
                'description' => 'Tin đăng được đánh dấu VIP, hiển thị nổi bật trong 3 ngày',
                'price' => 25000,
            ],
            [
                'name' => 'Tin VIP 7 ngày',
                'description' => 'Tin đăng được đánh dấu VIP, hiển thị nổi bật trong 7 ngày',
                'price' => 50000,
            ],
            [
                'name' => 'Gói Bán Chuyên',
                'description' => 'Dành cho người bán chuyên nghiệp, tối đa 50 tin/tháng',
                'price' => 200000,
            ],
        ];

        foreach ($services as $service) {
            $exists = DB::table('services')->where('name', $service['name'])->exists();

            if (!$exists) {
                DB::table('services')->insert(array_merge($service, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }
}
