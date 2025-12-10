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
        // Admin
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'full_name' => 'Admin User',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
            ]);
        }

        // Seller
        if (!User::where('email', 'seller@example.com')->exists()) {
            $seller = User::create([
                'full_name' => 'Shop VietMarket',
                'username' => 'shop_vietmarket',
                'email' => 'seller@example.com',
                'password' => Hash::make('password'),
                'role' => 'seller',
                'status' => 'active',
            ]);

            // Create wallet using DB if Model issue
            DB::table('wallets')->updateOrInsert(
                ['user_id' => $seller->id],
                ['balance' => 0, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // Buyer
        if (!User::where('email', 'buyer@example.com')->exists()) {
            $buyer = User::create([
                'full_name' => 'Nguyen Van Mua',
                'username' => 'buyer123',
                'email' => 'buyer@example.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'status' => 'active',
            ]);

            DB::table('wallets')->updateOrInsert(
                ['user_id' => $buyer->id],
                ['balance' => 500000, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
