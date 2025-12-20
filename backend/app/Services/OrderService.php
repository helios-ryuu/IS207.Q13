<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Transaction;
use App\Models\CartItem;
use App\Models\ShippingAddress;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class OrderService
{
    // 1. TẠO ĐƠN HÀNG (Checkout) - Tách theo từng người bán
    public function createOrder($userId, $data)
    {
        return DB::transaction(function () use ($userId, $data) {
            // A. Lấy giỏ hàng
            $cartItems = CartItem::with('variant.product')->where('user_id', $userId)->get();

            if ($cartItems->isEmpty()) {
                throw new Exception("Giỏ hàng trống.");
            }

            // B. Tự động TẠO ĐỊA CHỈ GIAO HÀNG MỚI
            $address = ShippingAddress::create([
                'user_id' => $userId,
                'receiver_name' => $data['receiver_name'],
                'phone_number' => $data['phone_number'],
                'street_address' => $data['street_address'],
                'province' => $data['province'],
                'district' => $data['district'] ?? '',
                'ward' => $data['ward'] ?? '',
                'is_default' => false,
                'status' => 'active'
            ]);

            // C. NHÓM CART ITEMS THEO SELLER_ID
            $itemsBySeller = [];
            foreach ($cartItems as $item) {
                if (!$item->variant || !$item->variant->product)
                    continue;

                $sellerId = $item->variant->product->seller_id;
                if (!isset($itemsBySeller[$sellerId])) {
                    $itemsBySeller[$sellerId] = [];
                }
                $itemsBySeller[$sellerId][] = $item;
            }

            // D. TẠO RIÊNG MỖI ĐƠN HÀNG CHO TỪNG SELLER
            $createdOrders = [];
            $grandTotal = 0;

            foreach ($itemsBySeller as $sellerId => $sellerItems) {
                // Tính tiền cho đơn hàng của seller này
                $subtotal = 0;
                foreach ($sellerItems as $item) {
                    $subtotal += $item->quantity * $item->variant->price;
                }

                // Phí ship cho mỗi đơn
                $shippingFee = 30000;
                if ($subtotal > 500000) {
                    $shippingFee = 0;
                }

                $totalAmount = $subtotal + $shippingFee;
                $grandTotal += $totalAmount;

                // Tạo Order riêng cho seller này
                $order = Order::create([
                    'user_id' => $userId,
                    'address_id' => $address->id,
                    'order_date' => now(),
                    'delivery_date' => now()->addDays(3),
                    'shipping_fee' => $shippingFee,
                    'status' => 'pending',
                    'notes' => $data['notes'] ?? null,
                    'payment_method' => $data['payment_method'] ?? 'cash',
                    'tracking_code' => 'ORD-' . strtoupper(Str::random(10)),
                    'total_amount' => $totalAmount,
                ]);

                // Lưu chi tiết đơn hàng & Trừ kho
                foreach ($sellerItems as $item) {
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'variant_id' => $item->variant_id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->variant->price,
                    ]);
                    $item->variant->decrement('quantity', $item->quantity);
                }

                $createdOrders[] = $order;
            }

            // E. TẠO TRANSACTION & TRỪ VÍ (Nếu thanh toán qua Ví)
            $paymentMethod = $data['payment_method'] ?? 'cash';
            if ($paymentMethod === 'wallet') {
                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $userId],
                    ['balance' => 0, 'status' => 'active']
                );

                // Kiểm tra số dư (tính từ tổng transactions)
                $currentBalance = $wallet->calculated_balance;
                if ($currentBalance < $grandTotal) {
                    throw new Exception('Số dư ví không đủ để thanh toán. Hiện có: ' . number_format($currentBalance) . 'đ');
                }

                // Tạo transaction cho từng đơn (balance sẽ tự động được tính lại từ transactions)
                foreach ($createdOrders as $order) {
                    Transaction::create([
                        'user_id' => $userId,
                        'order_id' => $order->id,
                        'amount' => -$order->total_amount, // Số âm = chi tiêu
                        'payment_method' => 'wallet',
                        'status' => 'completed',
                        'transaction_code' => 'PAY-' . strtoupper(Str::random(10)),
                        'transaction_date' => now(),
                        'payment_gateway' => 'system'
                    ]);
                }
            }

            // F. Xóa giỏ hàng
            CartItem::where('user_id', $userId)->delete();

            // Trả về đơn hàng đầu tiên (hoặc tất cả)
            return count($createdOrders) === 1 ? $createdOrders[0] : $createdOrders;
        });
    }

    // 2. LẤY DANH SÁCH ĐƠN
    public function getUserOrders($userId)
    {
        // Load kèm transactions để lấy số tiền hiển thị, thêm categories để filter
        return Order::with(['transactions', 'orderDetails.variant.product.categories', 'shippingAddress'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // 3. CHI TIẾT ĐƠN
    public function getOrderDetail($userId, $orderId)
    {
        return Order::with(['transactions', 'orderDetails.variant.product.categories', 'shippingAddress'])
            ->where('user_id', $userId)
            ->where('id', $orderId)
            ->firstOrFail();
    }

    // 4. HỦY ĐƠN
    public function cancelOrder($userId, $orderId, $reason = '')
    {
        $order = Order::where('user_id', $userId)->where('id', $orderId)->firstOrFail();

        if ($order->status !== 'pending') {
            throw new Exception("Chỉ có thể hủy đơn hàng đang chờ (pending).");
        }

        $order->status = 'cancelled';
        $order->notes = $reason ?: $order->notes; // Lưu lý do hủy vào notes
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
                                'variant.product.seller',   // Load seller info
                                'variant.product.categories' // Load categories cho filter
                            ]);
                }
            ]);

        // Filter by status if provided
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    // 7. CHẤP NHẬN ĐƠN HÀNG (pending -> shipping)
    public function acceptOrder($sellerId, $orderId)
    {
        $order = $this->getSellerOrderOrFail($sellerId, $orderId);

        if ($order->status !== 'pending') {
            throw new Exception("Chỉ có thể chấp nhận đơn hàng đang chờ xác nhận.");
        }

        $order->status = 'shipping'; // Chuyển thẳng sang vận chuyển
        $order->save();
        return $order;
    }

    // 8. GIAO CHO VẬN CHUYỂN (Legacy support or manual override)
    public function shipOrder($sellerId, $orderId)
    {
        $order = $this->getSellerOrderOrFail($sellerId, $orderId);

        if ($order->status === 'shipping')
            return $order; // Idempotent

        if ($order->status !== 'processing' && $order->status !== 'pending') {
            throw new Exception("Chỉ có thể giao vận chuyển đơn hàng đang xử lý hoặc chờ.");
        }

        $order->status = 'shipping';
        $order->save();
        return $order;
    }

    // 9. SELLER HỦY ĐƠN
    public function sellerCancelOrder($sellerId, $orderId, $reason = '')
    {
        $order = $this->getSellerOrderOrFail($sellerId, $orderId);

        if (!in_array($order->status, ['pending', 'processing'])) {
            throw new Exception("Không thể hủy đơn hàng đã giao cho vận chuyển.");
        }

        $order->status = 'cancelled';
        $order->notes = $reason ?: $order->notes; // Lưu lý do hủy vào notes
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

    // 11. HOÀN TẤT VẬN CHUYỂN (shipping -> completed)
    public function completeOrder($sellerId, $orderId)
    {
        $order = $this->getSellerOrderOrFail($sellerId, $orderId);

        if ($order->status !== 'shipping') {
            throw new Exception("Chỉ có thể hoàn tất đơn hàng đang vận chuyển.");
        }

        $order->status = 'completed';
        $order->save();
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

