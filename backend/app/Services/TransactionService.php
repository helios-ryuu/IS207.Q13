<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Exception;

class TransactionService
{
    // 1. Lấy danh sách giao dịch (Của các đơn hàng đã đặt)
    public function getUserTransactions($userId)
    {
        // Logic: Tìm tất cả giao dịch thuộc về các Order của User này
        // Vì bảng transaction không có user_id, ta phải query qua quan hệ 'order'
        return Transaction::whereHas('order', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->orderBy('created_at', 'desc')
        ->get();
    }

    // 2. Xem chi tiết 1 giao dịch
    public function getTransactionDetail($userId, $id)
    {
        // Phải đảm bảo User chỉ xem được giao dịch của chính mình
        $transaction = Transaction::with('order') // Load kèm thông tin đơn hàng
            ->where('id', $id)
            ->whereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->first();

        if (!$transaction) {
            throw new Exception("Giao dịch không tồn tại hoặc bạn không có quyền xem.");
        }

        return $transaction;
    }

    // 3. Xác thực thanh toán (Giả lập Webhook từ ngân hàng/Cổng thanh toán)
    public function verifyTransaction($transactionCode, $status)
    {
        return DB::transaction(function () use ($transactionCode, $status) {
            // Tìm giao dịch theo mã code (ví dụ: TRX-ABC...)
            $transaction = Transaction::where('transaction_code', $transactionCode)->first();

            if (!$transaction) {
                throw new Exception("Mã giao dịch không tồn tại.");
            }

            // Kiểm tra trạng thái trong DB (ENUM: pending, completed, failed, refunded)
            // Lưu ý: Kiểm tra kỹ enum trong database của bạn là 'completed' hay 'success'
            // Ở đây mình giả định enum giống ảnh ERD bạn gửi là 'completed'
            if ($transaction->status === 'completed' || $transaction->status === 'success') { 
                throw new Exception("Giao dịch này đã được xác thực trước đó rồi.");
            }

            // Map trạng thái từ API (success/failed) sang Database
            // Nếu DB dùng 'completed', ta map 'success' -> 'completed'
            $newStatus = ($status == 'success') ? 'completed' : 'failed';
            
            // Nếu DB dùng 'success', thì giữ nguyên:
            // $newStatus = $status; 

            $transaction->status = $newStatus;
            $transaction->response_data = json_encode(['message' => 'Verified via API', 'time' => now()]); // Lưu log
            $transaction->save();

            // Nếu thanh toán thành công -> Cập nhật trạng thái Đơn hàng
            // Từ 'pending' -> 'confirmed' (hoặc 'processing' tùy quy trình bên bạn)
            if (($newStatus == 'completed' || $newStatus == 'success') && $transaction->order) {
                $transaction->order->update(['status' => 'confirmed']);
            }

            return $transaction;
        });
    }
}