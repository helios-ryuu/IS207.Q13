<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppliedPromotionSeeder extends Seeder
{
    public function run(): void
    {
        $promotions = DB::table('promotions')
            ->where('status', 'active')
            ->get();

        $orders = DB::table('orders')->get();

        if ($promotions->isEmpty() || $orders->isEmpty())
            return;

        $appliedCount = 0;
        $targetCount = 15;

        foreach ($orders as $order) {
            if ($appliedCount >= $targetCount)
                break;

            $promotion = $promotions->random();

            $exists = DB::table('applied_promotions')
                ->where('promotion_id', $promotion->id)
                ->where('order_id', $order->id)
                ->exists();

            if ($exists)
                continue;

            // Calculate discounted amount based on promotion type
            $orderTotal = DB::table('order_details')
                ->where('order_id', $order->id)
                ->sum(DB::raw('quantity * unit_price'));

            if ($orderTotal <= 0)
                continue;

            $discountedAmount = 0;
            if ($promotion->type === 'percentage') {
                $discountedAmount = $orderTotal * ($promotion->discount_amount / 100);
            } else {
                $discountedAmount = $promotion->discount_amount;
            }

            DB::table('applied_promotions')->insert([
                'promotion_id' => $promotion->id,
                'order_id' => $order->id,
                'discounted_amount' => min($discountedAmount, $orderTotal),
                'created_at' => $order->created_at,
            ]);

            $appliedCount++;
        }
    }
}
