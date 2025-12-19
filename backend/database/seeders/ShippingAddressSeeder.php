<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingAddressSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'seller')->get();

        $provinces = ['TP. Hồ Chí Minh', 'Hà Nội', 'Đà Nẵng', 'Cần Thơ', 'Hải Phòng'];
        $districts = [
            'TP. Hồ Chí Minh' => ['Quận 1', 'Quận 3', 'Quận 5', 'Quận 7', 'Quận Bình Thạnh', 'Quận Gò Vấp', 'Quận Tân Bình'],
            'Hà Nội' => ['Quận Hoàn Kiếm', 'Quận Ba Đình', 'Quận Cầu Giấy', 'Quận Đống Đa', 'Quận Hai Bà Trưng'],
            'Đà Nẵng' => ['Quận Hải Châu', 'Quận Thanh Khê', 'Quận Sơn Trà'],
            'Cần Thơ' => ['Quận Ninh Kiều', 'Quận Bình Thủy'],
            'Hải Phòng' => ['Quận Hồng Bàng', 'Quận Lê Chân', 'Quận Ngô Quyền'],
        ];

        $streets = [
            'Đường Nguyễn Huệ',
            'Đường Lê Lợi',
            'Đường Trần Hưng Đạo',
            'Đường Hai Bà Trưng',
            'Đường Nguyễn Trãi',
            'Đường Võ Văn Tần',
            'Đường Điện Biên Phủ',
            'Đường Cách Mạng Tháng 8',
            'Đường Lý Thường Kiệt',
            'Đường Pasteur',
            'Đường Nam Kỳ Khởi Nghĩa',
            'Đường Nguyễn Văn Linh',
        ];

        $addressCount = 0;
        $isFirst = [];

        foreach ($users as $user) {
            // Each user gets 1-3 addresses
            $numAddresses = rand(1, 3);
            $isFirst[$user->id] = true;

            for ($i = 0; $i < $numAddresses && $addressCount < 20; $i++) {
                $province = $provinces[array_rand($provinces)];
                $district = $districts[$province][array_rand($districts[$province])];
                $street = rand(1, 500) . ' ' . $streets[array_rand($streets)];

                $exists = DB::table('shipping_addresses')
                    ->where('user_id', $user->id)
                    ->where('street_address', $street)
                    ->exists();

                if ($exists)
                    continue;

                DB::table('shipping_addresses')->insert([
                    'user_id' => $user->id,
                    'receiver_name' => $user->full_name,
                    'phone_number' => '09' . rand(10000000, 99999999),
                    'street_address' => $street,
                    'province' => $province,
                    'district' => $district,
                    'ward' => 'Phường ' . rand(1, 20),
                    'is_default' => $isFirst[$user->id],
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $isFirst[$user->id] = false;
                $addressCount++;
            }
        }
    }
}
