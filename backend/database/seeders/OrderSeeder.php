<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $buyer = User::where('email', 'buyer@example.com')->first();
        $seller = User::where('email', 'seller@example.com')->first();

        if (!$buyer || !$seller)
            return;

        // Lấy địa chỉ mặc định của buyer
        $address = DB::table('shipping_addresses')
            ->where('user_id', $buyer->id)
            ->where('is_default', true)
            ->first();

        if (!$address)
            return;

        // Lấy một số variants để tạo đơn hàng
        $variants = ProductVariant::with('product')
            ->whereHas('product', fn($q) => $q->where('seller_id', $seller->id))
            ->take(3)
            ->get();

        if ($variants->isEmpty())
            return;

        // Tạo 2 đơn hàng mẫu với các trạng thái khác nhau
        $orders = [
            [
                'status' => 'delivered',
                'payment_method' => 'cash',
                'notes' => 'Giao nhanh giùm em',
                'delivery_date' => now()->subDays(25),
                'created_ago' => 30,
            ],
            [
                'status' => 'pending',
                'payment_method' => 'bank_transfer',
                'notes' => 'Gọi trước khi giao',
                'delivery_date' => null,
                'created_ago' => 2,
            ],
        ];

        foreach ($orders as $index => $orderData) {
            $variant = $variants[$index % count($variants)];

            // Kiểm tra đã tồn tại chưa dựa trên notes và user
            $exists = DB::table('orders')
                ->where('user_id', $buyer->id)
                ->where('notes', $orderData['notes'])
                ->exists();

            if ($exists)
                continue;

            $orderId = DB::table('orders')->insertGetId([
                'user_id' => $buyer->id,
                'address_id' => $address->id,
                'order_date' => now()->subDays($orderData['created_ago']),
                'delivery_date' => $orderData['delivery_date'],
                'shipping_fee' => 30000,
                'status' => $orderData['status'],
                'payment_method' => $orderData['payment_method'],
                'tracking_code' => 'VN' . strtoupper(Str::random(10)),
                'notes' => $orderData['notes'],
                'created_at' => now()->subDays($orderData['created_ago']),
                'updated_at' => now()->subDays($orderData['created_ago'] - 1),
            ]);

            // Tạo order detail (chú ý: dùng unit_price, không có updated_at)
            DB::table('order_details')->insert([
                'order_id' => $orderId,
                'variant_id' => $variant->id,
                'quantity' => 1,
                'unit_price' => $variant->price,
                'created_at' => now(),
            ]);
        }
    }
}
