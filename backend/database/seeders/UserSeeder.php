<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // ===== ADMINS =====
            [
                'full_name' => 'Admin User',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'role' => 'admin',
                'status' => 'active',
            ],
            [
                'full_name' => 'Quản trị viên Hệ thống',
                'username' => 'sysadmin',
                'email' => 'sysadmin@vietmarket.com',
                'role' => 'admin',
                'status' => 'active',
            ],

            // ===== SELLERS =====
            [
                'full_name' => 'Shop VietMarket',
                'username' => 'shop_vietmarket',
                'email' => 'seller@example.com',
                'role' => 'seller',
                'status' => 'active',
            ],
            [
                'full_name' => 'Điện Thoại 24h',
                'username' => 'dienthoai24h',
                'email' => 'dienthoai24h@gmail.com',
                'role' => 'seller',
                'status' => 'active',
            ],
            [
                'full_name' => 'Thời Trang Xinh',
                'username' => 'thoitrangxinh',
                'email' => 'thoitrangxinh@gmail.com',
                'role' => 'seller',
                'status' => 'active',
            ],
            [
                'full_name' => 'Nội Thất Đẹp',
                'username' => 'noithatdep',
                'email' => 'noithatdep@gmail.com',
                'role' => 'seller',
                'status' => 'active',
            ],
            [
                'full_name' => 'Shop Xe Máy SG',
                'username' => 'xemaysg',
                'email' => 'xemaysg@gmail.com',
                'role' => 'seller',
                'status' => 'active',
            ],
            [
                'full_name' => 'Pet Shop Cute',
                'username' => 'petshopcute',
                'email' => 'petshopcute@gmail.com',
                'role' => 'seller',
                'status' => 'active',
            ],

            // ===== CUSTOMERS =====
            [
                'full_name' => 'Nguyen Van Mua',
                'username' => 'buyer123',
                'email' => 'buyer@example.com',
                'role' => 'customer',
                'status' => 'active',
            ],
            [
                'full_name' => 'Trần Thị Hoa',
                'username' => 'hoatran',
                'email' => 'hoatran@gmail.com',
                'role' => 'customer',
                'status' => 'active',
            ],
            [
                'full_name' => 'Lê Văn Minh',
                'username' => 'minhle',
                'email' => 'minhle@gmail.com',
                'role' => 'customer',
                'status' => 'active',
            ],
            [
                'full_name' => 'Phạm Thu Hương',
                'username' => 'huongpham',
                'email' => 'huongpham@gmail.com',
                'role' => 'customer',
                'status' => 'active',
            ],
            [
                'full_name' => 'Hoàng Đức Anh',
                'username' => 'anhhoang',
                'email' => 'anhhoang@gmail.com',
                'role' => 'customer',
                'status' => 'active',
            ],
            [
                'full_name' => 'Vũ Thị Lan',
                'username' => 'lanvu',
                'email' => 'lanvu@gmail.com',
                'role' => 'customer',
                'status' => 'active',
            ],
            [
                'full_name' => 'Ngô Quốc Bảo',
                'username' => 'baongo',
                'email' => 'baongo@gmail.com',
                'role' => 'customer',
                'status' => 'active',
            ],
        ];

        foreach ($users as $userData) {
            if (!User::where('email', $userData['email'])->exists()) {
                User::create(array_merge($userData, [
                    'password' => Hash::make('password'),
                ]));
            }
        }
    }
}
