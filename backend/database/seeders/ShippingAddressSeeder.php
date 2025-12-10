<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingAddressSeeder extends Seeder
{
    public function run(): void
    {
        $buyer = User::where('email', 'buyer@example.com')->first();
        $seller = User::where('email', 'seller@example.com')->first();

        $addresses = [
            [
                'user_id' => $buyer?->id,
                'receiver_name' => 'Nguyễn Văn Mua',
                'phone_number' => '0901234567',
                'street_address' => '123 Đường Nguyễn Huệ',
                'province' => 'TP. Hồ Chí Minh',
                'district' => 'Quận 1',
                'ward' => 'Phường Bến Nghé',
                'is_default' => true,
                'status' => 'active',
            ],
            [
                'user_id' => $buyer?->id,
                'receiver_name' => 'Nguyễn Văn Mua',
                'phone_number' => '0901234567',
                'street_address' => '456 Đường Lê Lợi',
                'province' => 'TP. Hồ Chí Minh',
                'district' => 'Quận 3',
                'ward' => 'Phường 1',
                'is_default' => false,
                'status' => 'active',
            ],
            [
                'user_id' => $seller?->id,
                'receiver_name' => 'Shop VietMarket',
                'phone_number' => '0909876543',
                'street_address' => '789 Đường Trần Hưng Đạo',
                'province' => 'TP. Hồ Chí Minh',
                'district' => 'Quận 5',
                'ward' => 'Phường 14',
                'is_default' => true,
                'status' => 'active',
            ],
        ];

        foreach ($addresses as $addr) {
            if (!$addr['user_id'])
                continue;

            $exists = DB::table('shipping_addresses')
                ->where('user_id', $addr['user_id'])
                ->where('street_address', $addr['street_address'])
                ->exists();

            if (!$exists) {
                DB::table('shipping_addresses')->insert(array_merge($addr, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }
}
