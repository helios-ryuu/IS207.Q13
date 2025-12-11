<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $sellers = User::where('role', 'seller')->get();

        if ($customers->isEmpty() || $sellers->isEmpty())
            return;

        $conversationTemplates = [
            [
                ['from' => 'customer', 'content' => 'Chào shop, sản phẩm còn hàng không ạ?', 'status' => 'read'],
                ['from' => 'seller', 'content' => 'Chào bạn, sản phẩm vẫn còn hàng nhé. Bạn cần mình hỗ trợ thêm gì không?', 'status' => 'read'],
                ['from' => 'customer', 'content' => 'Giá có giảm thêm được không shop?', 'status' => 'read'],
                ['from' => 'seller', 'content' => 'Shop có thể giảm thêm 5% nếu bạn mua từ 2 sản phẩm trở lên nhé.', 'status' => 'sent'],
            ],
            [
                ['from' => 'customer', 'content' => 'Shop ơi, máy còn bảo hành không?', 'status' => 'read'],
                ['from' => 'seller', 'content' => 'Dạ máy còn bảo hành chính hãng 12 tháng ạ.', 'status' => 'read'],
                ['from' => 'customer', 'content' => 'Ok shop, mình đặt đơn nhé. Thanks!', 'status' => 'sent'],
            ],
            [
                ['from' => 'customer', 'content' => 'Sản phẩm ship về Hà Nội mất mấy ngày?', 'status' => 'read'],
                ['from' => 'seller', 'content' => 'Từ 2-3 ngày làm việc bạn nhé. Shop giao hàng nhanh J&T Express.', 'status' => 'sent'],
            ],
            [
                ['from' => 'customer', 'content' => 'Size L còn không shop?', 'status' => 'read'],
                ['from' => 'seller', 'content' => 'Có bạn nhé, size L đang còn 5 cái.', 'status' => 'read'],
                ['from' => 'customer', 'content' => 'Vải có co giãn không?', 'status' => 'read'],
                ['from' => 'seller', 'content' => 'Vải cotton pha spandex nên co giãn tốt lắm bạn.', 'status' => 'read'],
                ['from' => 'customer', 'content' => 'Mình đặt 2 cái màu đen và trắng nhé.', 'status' => 'sent'],
            ],
            [
                ['from' => 'customer', 'content' => 'Có ship COD không shop?', 'status' => 'read'],
                ['from' => 'seller', 'content' => 'Có COD bạn nhé, phí ship 30k.', 'status' => 'sent'],
            ],
            [
                ['from' => 'seller', 'content' => 'Chào bạn, shop đang có chương trình sale 20% tất cả sản phẩm. Mời bạn ghé xem nhé!', 'status' => 'read'],
                ['from' => 'customer', 'content' => 'Cảm ơn shop, để mình xem.', 'status' => 'sent'],
            ],
        ];

        $messageCount = 0;
        $targetCount = 40;

        foreach ($customers as $customer) {
            // Each customer chats with 1-2 sellers
            $numConversations = rand(1, 2);
            $selectedSellers = $sellers->random(min($numConversations, $sellers->count()));

            foreach ($selectedSellers as $seller) {
                if ($messageCount >= $targetCount)
                    break 2;

                $template = $conversationTemplates[array_rand($conversationTemplates)];
                $baseMinutes = rand(60, 720);

                foreach ($template as $index => $msg) {
                    if ($messageCount >= $targetCount)
                        break 2;

                    $senderId = $msg['from'] === 'customer' ? $customer->id : $seller->id;
                    $receiverId = $msg['from'] === 'customer' ? $seller->id : $customer->id;
                    $minutesAgo = $baseMinutes - ($index * rand(3, 10));

                    $exists = DB::table('messages')
                        ->where('sender_id', $senderId)
                        ->where('receiver_id', $receiverId)
                        ->where('content', $msg['content'])
                        ->exists();

                    if ($exists)
                        continue;

                    DB::table('messages')->insert([
                        'sender_id' => $senderId,
                        'receiver_id' => $receiverId,
                        'content' => $msg['content'],
                        'sent_date' => now()->subMinutes($minutesAgo),
                        'status' => $msg['status'],
                        'created_at' => now()->subMinutes($minutesAgo),
                        'updated_at' => now()->subMinutes($minutesAgo),
                    ]);

                    $messageCount++;
                }
            }
        }
    }
}
