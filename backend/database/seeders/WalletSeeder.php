<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Kiểm tra wallet đã tồn tại chưa
            $walletExists = DB::table('wallets')->where('user_id', $user->id)->exists();

            if (!$walletExists) {
                // Tạo wallet với balance = 0 (balance sẽ được tính từ transactions)
                $walletId = DB::table('wallets')->insertGetId([
                    'user_id' => $user->id,
                    'balance' => 0, // Balance tính từ transactions
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Xác định số tiền khởi tạo theo role
                $initialAmount = match ($user->role) {
                    'admin' => 10000000,
                    'seller' => 5000000,
                    default => 500000,
                };

                // Tạo giao dịch khởi tạo (Initial Deposit)
                DB::table('transactions')->insert([
                    'user_id' => $user->id,
                    'order_id' => null, // Không liên quan đến đơn hàng
                    'amount' => $initialAmount, // Số dương = nạp tiền
                    'payment_method' => 'system',
                    'status' => 'completed',
                    'transaction_code' => 'INIT-' . strtoupper(Str::random(10)),
                    'transaction_date' => now(),
                    'payment_gateway' => 'system',
                    'response_data' => json_encode(['note' => 'Số dư khởi tạo tài khoản']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
