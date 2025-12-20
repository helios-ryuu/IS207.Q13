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

    // Nạp tiền (Chỉ tạo Transaction - balance tính từ sum)
    public function deposit($userId, $amount)
    {
        return DB::transaction(function () use ($userId, $amount) {
            $wallet = $this->getWallet($userId);

            // Tạo giao dịch nạp tiền (số dương)
            Transaction::create([
                'user_id' => $userId,
                'order_id' => null,
                'amount' => $amount, // Số dương = nạp tiền
                'payment_method' => 'bank_transfer',
                'status' => 'completed',
                'transaction_code' => 'DEP_' . strtoupper(uniqid()),
                'transaction_date' => now(),
                'response_data' => json_encode(['note' => 'Nạp tiền vào ví']),
            ]);

            return $wallet;
        });
    }

    // Rút tiền (Kiểm tra calculated_balance, chỉ tạo Transaction)
    // Môi trường test: Transaction completed ngay lập tức (tiền trừ ngay)
    public function withdraw($userId, $amount)
    {
        return DB::transaction(function () use ($userId, $amount) {
            $wallet = $this->getWallet($userId);

            // Kiểm tra số dư (tính từ transactions)
            $currentBalance = $wallet->calculated_balance;
            if ($currentBalance < $amount) {
                throw new Exception("Số dư ví không đủ. Hiện có: " . number_format($currentBalance) . "đ");
            }

            // Tạo giao dịch rút tiền (số âm, completed ngay cho test)
            Transaction::create([
                'user_id' => $userId,
                'order_id' => null,
                'amount' => -$amount, // Số âm = rút tiền
                'payment_method' => 'bank_transfer',
                'status' => 'completed', // Thành công ngay cho môi trường test
                'transaction_code' => 'WDR_' . strtoupper(uniqid()),
                'transaction_date' => now(),
                'response_data' => json_encode(['note' => 'Rút tiền về ngân hàng']),
            ]);

            return $wallet;
        });
    }

    // Lấy lịch sử giao dịch
    public function getHistory($userId)
    {
        return Transaction::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}