<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Transaction;
use App\Models\CartItem;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class OrderService
{
    // 1. TẠO ĐƠN HÀNG (Checkout)
    public function createOrder($userId, $data)
    {
        return DB::transaction(function () use ($userId, $data) {
            // A. Lấy giỏ hàng
            $cartItems = CartItem::with('variant.product')->where('user_id', $userId)->get();

            if ($cartItems->isEmpty()) {
                throw new Exception("Giỏ hàng trống.");
            }

            // B. Tính tiền thủ công (Vì bảng Order ko lưu)
            $subtotal = 0;
            foreach ($cartItems as $item) {
                if (!$item->variant) continue;
                
                // Check tồn kho
                if ($item->variant->quantity < $item->quantity) {
                    throw new Exception("Sản phẩm {$item->variant->product->product_name} không đủ hàng.");
                }
                
                $subtotal += $item->quantity * $item->variant->price;
            }

            $shippingFee = 30000; // Phí ship mặc định
            $totalAmount = $subtotal + $shippingFee;

            // C. Tạo Order (Khớp hoàn toàn với Model Order bạn đưa)
            $order = Order::create([
                'user_id' => $userId,
                'address_id' => $data['address_id'],
                'order_date' => now(),
                'delivery_date' => now()->addDays(3),
                'shipping_fee' => $shippingFee,
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'tracking_code' => 'ORD-' . strtoupper(Str::random(10)),
            ]);

            // D. Lưu chi tiết đơn hàng & Trừ kho
            foreach ($cartItems as $item) {
                if (!$item->variant) continue;

                OrderDetail::create([
                    'order_id' => $order->id,
                    'variant_id' => $item->variant_id, // Lưu ý: DB bạn dùng variant_id
                    'quantity' => $item->quantity,
                    'unit_price' => $item->variant->price, // Lưu ý: DB bạn dùng unit_price
                ]);

                // Trừ kho
                $item->variant->decrement('quantity', $item->quantity);
            }

            // E. TẠO TRANSACTION (Quan trọng: Lưu tổng tiền vào đây)
            Transaction::create([
                'order_id' => $order->id,
                'amount' => $totalAmount, // <--- TIỀN NẰM Ở ĐÂY
                'payment_method' => $order->payment_method,
                'status' => 'pending',
                'transaction_code' => 'TRX-' . strtoupper(Str::random(10)),
                'transaction_date' => now(),
                'payment_gateway' => 'system'
            ]);

            // F. Xóa giỏ hàng
            CartItem::where('user_id', $userId)->delete();

            return $order;
        });
    }

    // 2. LẤY DANH SÁCH ĐƠN
    public function getUserOrders($userId)
    {
        // Load kèm transactions để lấy số tiền hiển thị
        return Order::with(['transactions', 'orderDetails.variant.product', 'shippingAddress'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // 3. CHI TIẾT ĐƠN
    public function getOrderDetail($userId, $orderId)
    {
        return Order::with(['transactions', 'orderDetails.variant.product', 'shippingAddress'])
            ->where('user_id', $userId)
            ->where('id', $orderId)
            ->firstOrFail();
    }

    // 4. HỦY ĐƠN
    public function cancelOrder($userId, $orderId)
    {
        $order = Order::where('user_id', $userId)->where('id', $orderId)->firstOrFail();
        
        if ($order->status !== 'pending') {
            throw new Exception("Chỉ có thể hủy đơn hàng đang chờ (pending).");
        }

        $order->status = 'cancelled';
        $order->save();
        
        return $order;
    }

    // 5. CẬP NHẬT TRẠNG THÁI (Seller/Admin)
    public function updateStatus($orderId, $status)
    {
        $order = Order::findOrFail($orderId);
        $order->status = $status;
        $order->save();
        return $order;
    }
}