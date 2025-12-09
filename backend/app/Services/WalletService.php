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
            ['balance' => 0, 'status' => 'active'] // Mặc định 0đ
        );
    }

    // Nạp tiền (Chỉ cộng số dư, không lưu Transaction)
    public function deposit($userId, $amount)
    {
        return DB::transaction(function () use ($userId, $amount) {
            $wallet = $this->getWallet($userId);
            
            // Cộng tiền vào balance
            $wallet->balance += $amount;
            $wallet->save();

            return $wallet;
        });
    }

    // Rút tiền
    public function withdraw($userId, $amount)
    {
        return DB::transaction(function () use ($userId, $amount) {
            $wallet = $this->getWallet($userId);

            if ($wallet->balance < $amount) {
                throw new Exception("Số dư ví không đủ.");
            }

            // Trừ tiền khỏi balance
            $wallet->balance -= $amount;
            $wallet->save();

            return $wallet;
        });
    }

    // Lấy lịch sử giao dịch
    // Logic: Lấy tất cả Transaction thuộc về các Đơn hàng của User này
    public function getHistory($userId)
    {
        return Transaction::whereHas('order', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->orderBy('created_at', 'desc')
        ->get();
    }
}