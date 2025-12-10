<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WalletSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Kiểm tra wallet đã tồn tại chưa
            $exists = DB::table('wallets')->where('user_id', $user->id)->exists();

            if (!$exists) {
                $balance = match ($user->role) {
                    'admin' => 10000000,
                    'seller' => 5000000,
                    default => 500000,
                };

                DB::table('wallets')->insert([
                    'user_id' => $user->id,
                    'balance' => $balance,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
