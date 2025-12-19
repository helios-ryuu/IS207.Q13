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
        $customers = User::where('role', 'seller')->get();
        $variants = ProductVariant::with('product')->where('status', 'active')->get();

        if ($customers->isEmpty() || $variants->isEmpty())
            return;

        // Valid statuses: pending, confirmed, processing, shipped, delivered, cancelled, refunded
        $statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'delivered', 'delivered', 'cancelled'];
        $paymentMethods = ['cash', 'bank_transfer', 'wallet'];
        $notes = [
            'Giao nhanh giùm em',
            'Gọi trước khi giao',
            'Để ở bảo vệ nếu không có người nhận',
            'Ship giờ hành chính',
            'Giao cuối tuần',
            null,
            null,
        ];

        $orderCount = 0;
        $targetCount = 25;

        // Keep looping until we reach target or exhausted attempts
        $attempts = 0;
        $maxAttempts = 100;

        while ($orderCount < $targetCount && $attempts < $maxAttempts) {
            $attempts++;

            foreach ($customers as $customer) {
                if ($orderCount >= $targetCount)
                    break;

                $address = DB::table('shipping_addresses')
                    ->where('user_id', $customer->id)
                    ->inRandomOrder()
                    ->first();

                if (!$address)
                    continue;

                $status = $statuses[array_rand($statuses)];
                $createdAgo = rand(1, 60);
                $deliveryDate = null;

                if ($status === 'delivered') {
                    $deliveryDate = now()->subDays($createdAgo - rand(1, 5));
                }

                $orderId = DB::table('orders')->insertGetId([
                    'user_id' => $customer->id,
                    'address_id' => $address->id,
                    'order_date' => now()->subDays($createdAgo),
                    'delivery_date' => $deliveryDate,
                    'shipping_fee' => [0, 15000, 25000, 30000][array_rand([0, 15000, 25000, 30000])],
                    'status' => $status,
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'tracking_code' => 'VN' . strtoupper(Str::random(10)),
                    'notes' => $notes[array_rand($notes)],
                    'created_at' => now()->subDays($createdAgo),
                    'updated_at' => now()->subDays(max(0, $createdAgo - rand(1, 5))),
                ]);

                // Add 1-3 order details per order
                $numDetails = rand(1, 3);
                $selectedVariants = $variants->random(min($numDetails, $variants->count()));

                foreach ($selectedVariants as $variant) {
                    DB::table('order_details')->insert([
                        'order_id' => $orderId,
                        'variant_id' => $variant->id,
                        'quantity' => rand(1, 3),
                        'unit_price' => $variant->price,
                        'created_at' => now()->subDays($createdAgo),
                    ]);
                }

                $orderCount++;
            }
        }
    }
}
