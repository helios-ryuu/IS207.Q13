<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Exception;

class WalletService
{
    // Lấy ví của user (Nếu chưa có thì tự tạo)
    public function getWallet($userId)
    {
        return Wallet::firstOrCreate(
            ['user_id' => $userId],
            ['balance' => 0, 'status' => 'active']
        );
    }

    // Nạp tiền (Logic đã sửa: Cộng tiền + Tạo Transaction)
    public function deposit($userId, $amount)
    {
        return DB::transaction(function () use ($userId, $amount) {
            $wallet = $this->getWallet($userId);
            
            // 1. Cộng tiền vào ví
            $wallet->balance += $amount;
            $wallet->save();

            // 2. [QUAN TRỌNG] Tạo lịch sử giao dịch
            Transaction::create([
                'user_id' => $userId, // Gắn với user
                'order_id' => null,   // Nạp tiền thì không có đơn hàng
                'amount' => $amount,  // Số dương
                'payment_method' => 'bank_transfer', // Hoặc 'system'
                'status' => 'completed',
                'transaction_code' => 'DEP_' . strtoupper(uniqid()), // Mã giao dịch duy nhất
                'transaction_date' => now(),
                'response_data' => json_encode(['note' => 'Nạp tiền vào ví']),
            ]);

            return $wallet;
        });
    }

    // Rút tiền (Logic đã sửa: Trừ tiền + Tạo Transaction)
    public function withdraw($userId, $amount)
    {
        return DB::transaction(function () use ($userId, $amount) {
            $wallet = $this->getWallet($userId);

            if ($wallet->balance < $amount) {
                throw new Exception("Số dư ví không đủ.");
            }

            // 1. Trừ tiền
            $wallet->balance -= $amount;
            $wallet->save();

            // 2. Tạo lịch sử giao dịch
            Transaction::create([
                'user_id' => $userId,
                'order_id' => null,
                'amount' => -$amount, // Số âm
                'payment_method' => 'bank_transfer',
                'status' => 'pending', // Chờ xử lý
                'transaction_code' => 'WDR_' . strtoupper(uniqid()),
                'transaction_date' => now(),
                'response_data' => json_encode(['note' => 'Yêu cầu rút tiền']),
            ]);

            return $wallet;
        });
    }

    // Lấy lịch sử giao dịch
    public function getHistory($userId)
    {
        // Logic mới: Lấy tất cả giao dịch của User (bao gồm cả Nạp/Rút và Đơn hàng)
        return Transaction::where('user_id', $userId)
            ->orWhereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }
}