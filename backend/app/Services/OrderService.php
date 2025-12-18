<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Transaction;
use App\Models\CartItem;
use App\Models\ShippingAddress;
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

            // B. Tự động TẠO ĐỊA CHỈ GIAO HÀNG MỚI
            // Vì user nhập form trực tiếp, ta lưu địa chỉ này lại
            $address = ShippingAddress::create([
                'user_id' => $userId,
                'receiver_name' => $data['receiver_name'],
                'phone_number' => $data['phone_number'],
                'street_address' => $data['street_address'],
                'province' => $data['province'],
                'district' => $data['district'],
                'ward' => $data['ward'],
                'is_default' => false, // Mặc định không set default để tránh conflict
                'status' => 'active'
            ]);

            // C. Tính tiền thủ công
            $subtotal = 0;
            foreach ($cartItems as $item) {
                if (!$item->variant)
                    continue;

                // Check tồn kho
                if ($item->variant->quantity < $item->quantity) {
                    throw new Exception("Sản phẩm {$item->variant->product->product_name} không đủ hàng.");
                }

                $subtotal += $item->quantity * $item->variant->price;
            }

            $shippingFee = 30000;
            // Logic miễn phí vận chuyển nếu trên 500k (giống Frontend đang tính)
            if ($subtotal > 500000) {
                $shippingFee = 0;
            }

            $totalAmount = $subtotal + $shippingFee;

            // D. Tạo Order (Dùng ID của địa chỉ vừa tạo ở trên)
            $order = Order::create([
                'user_id' => $userId,
                'address_id' => $address->id, // <--- Dùng ID vừa tạo
                'order_date' => now(),
                'delivery_date' => now()->addDays(3),
                'shipping_fee' => $shippingFee,
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'tracking_code' => 'ORD-' . strtoupper(Str::random(10)),
            ]);

            // E. Lưu chi tiết đơn hàng & Trừ kho
            foreach ($cartItems as $item) {
                if (!$item->variant)
                    continue;

                OrderDetail::create([
                    'order_id' => $order->id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->variant->price,
                ]);
                $item->variant->decrement('quantity', $item->quantity);
            }

            // F. TẠO TRANSACTION
            Transaction::create([
                'order_id' => $order->id,
                'amount' => $totalAmount,
                'payment_method' => $order->payment_method,
                'status' => 'pending',
                'transaction_code' => 'TRX-' . strtoupper(Str::random(10)),
                'transaction_date' => now(),
                'payment_gateway' => 'system'
            ]);

            // G. Xóa giỏ hàng
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

    // 6. LẤY DANH SÁCH ĐƠN BÁN (Cho Seller)
    public function getSellerOrders($sellerId, $status = null)
    {
        // Logic: Lấy các đơn hàng MÀ trong chi tiết đơn hàng đó có sản phẩm của tôi
        $query = Order::whereHas('orderDetails.variant.product', function ($query) use ($sellerId) {
            $query->where('seller_id', $sellerId);
        })
            ->with([
                'transactions',
                'shippingAddress',
                'user',
                'orderDetails' => function ($query) use ($sellerId) {
                    // Quan trọng: Chỉ load những chi tiết đơn hàng thuộc về người bán này
                    $query->whereHas('variant.product', function ($q) use ($sellerId) {
                        $q->where('seller_id', $sellerId);
                    })->with([
                                'variant.images',           // Load variant images
                                'variant.product.seller'    // Load seller info
                            ]);
                }
            ]);

        // Filter by status if provided
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    // 7. CHẤP NHẬN ĐƠN HÀNG (pending -> processing)
    public function acceptOrder($sellerId, $orderId)
    {
        $order = $this->getSellerOrderOrFail($sellerId, $orderId);

        if ($order->status !== 'pending') {
            throw new Exception("Chỉ có thể chấp nhận đơn hàng đang chờ xác nhận.");
        }

        $order->status = 'processing';
        $order->save();
        return $order;
    }

    // 8. GIAO CHO VẬN CHUYỂN (processing -> shipping)
    public function shipOrder($sellerId, $orderId)
    {
        $order = $this->getSellerOrderOrFail($sellerId, $orderId);

        if ($order->status !== 'processing') {
            throw new Exception("Chỉ có thể giao vận chuyển đơn hàng đang xử lý.");
        }

        $order->status = 'shipping';
        $order->save();
        return $order;
    }

    // 9. SELLER HỦY ĐƠN
    public function sellerCancelOrder($sellerId, $orderId)
    {
        $order = $this->getSellerOrderOrFail($sellerId, $orderId);

        if (!in_array($order->status, ['pending', 'processing'])) {
            throw new Exception("Không thể hủy đơn hàng đã giao cho vận chuyển.");
        }

        $order->status = 'cancelled';
        $order->save();

        // TODO: Hoàn tiền nếu đã thanh toán
        return $order;
    }

    // 10. XÁC NHẬN HOÀN HÀNG (return -> refunded)
    public function confirmReturn($sellerId, $orderId)
    {
        $order = $this->getSellerOrderOrFail($sellerId, $orderId);

        if ($order->status !== 'return') {
            throw new Exception("Đơn hàng không ở trạng thái yêu cầu hoàn.");
        }

        $order->status = 'refunded';
        $order->save();

        // TODO: Xử lý hoàn tiền thực tế
        return $order;
    }

    // Helper: Lấy đơn hàng thuộc seller hoặc throw exception
    private function getSellerOrderOrFail($sellerId, $orderId)
    {
        $order = Order::whereHas('orderDetails.variant.product', function ($query) use ($sellerId) {
            $query->where('seller_id', $sellerId);
        })->where('id', $orderId)->first();

        if (!$order) {
            throw new Exception("Không tìm thấy đơn hàng hoặc bạn không có quyền truy cập.");
        }

        return $order;
    }
}

