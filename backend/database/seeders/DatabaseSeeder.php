<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Gọi các seeder khác trước
        $this->call([
            CategorySeeder::class,
            // ProductSeeder::class, // Bỏ comment sau khi fix xong user
        ]);

        // Tạo 1 User Admin mẫu
        User::factory()->create([
            'full_name' => 'Admin User', // Sửa 'name' thành 'full_name'
            'username' => 'admin',       // Thêm username
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => bcrypt('password'), // Mật khẩu là 'password'
        ]);

        // Tạo 1 User Seller mẫu
        User::factory()->create([
            'full_name' => 'Seller User',
            'username' => 'seller',
            'email' => 'seller@example.com',
            'role' => 'seller',
        ]);
        
        // Chạy ProductSeeder sau khi đã có User
        $this->call([
            ProductSeeder::class,
            ProductPostSeeder::class,
        ]);
    }
}