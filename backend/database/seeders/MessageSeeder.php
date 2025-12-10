<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $buyer = User::where('email', 'buyer@example.com')->first();
        $seller = User::where('email', 'seller@example.com')->first();

        if (!$buyer || !$seller)
            return;

        $conversations = [
            [
                'sender_id' => $buyer->id,
                'receiver_id' => $seller->id,
                'content' => 'Chào shop, sản phẩm iPhone 15 còn hàng không ạ?',
                'status' => 'read',
                'created_ago_minutes' => 60,
            ],
            [
                'sender_id' => $seller->id,
                'receiver_id' => $buyer->id,
                'content' => 'Chào bạn, sản phẩm vẫn còn hàng nhé. Bạn cần mình hỗ trợ thêm gì không?',
                'status' => 'read',
                'created_ago_minutes' => 55,
            ],
            [
                'sender_id' => $buyer->id,
                'receiver_id' => $seller->id,
                'content' => 'Máy còn bảo hành không shop? Pin còn bao nhiêu phần trăm?',
                'status' => 'read',
                'created_ago_minutes' => 50,
            ],
            [
                'sender_id' => $seller->id,
                'receiver_id' => $buyer->id,
                'content' => 'Máy còn bảo hành Apple Care đến tháng 6/2025 nhé bạn. Pin 100% ạ.',
                'status' => 'sent',
                'created_ago_minutes' => 45,
            ],
            [
                'sender_id' => $buyer->id,
                'receiver_id' => $seller->id,
                'content' => 'Ok shop, mình đặt đơn nhé. Thanks!',
                'status' => 'sent',
                'created_ago_minutes' => 40,
            ],
        ];

        foreach ($conversations as $msg) {
            $exists = DB::table('messages')
                ->where('sender_id', $msg['sender_id'])
                ->where('receiver_id', $msg['receiver_id'])
                ->where('content', $msg['content'])
                ->exists();

            if ($exists)
                continue;

            DB::table('messages')->insert([
                'sender_id' => $msg['sender_id'],
                'receiver_id' => $msg['receiver_id'],
                'content' => $msg['content'],
                'sent_date' => now()->subMinutes($msg['created_ago_minutes']),
                'status' => $msg['status'],
                'created_at' => now()->subMinutes($msg['created_ago_minutes']),
                'updated_at' => now()->subMinutes($msg['created_ago_minutes']),
            ]);
        }
    }
}
