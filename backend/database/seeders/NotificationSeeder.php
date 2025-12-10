<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $buyer = User::where('email', 'buyer@example.com')->first();
        $seller = User::where('email', 'seller@example.com')->first();
        $admin = User::where('email', 'admin@example.com')->first();

        $notifications = [
            // Buyer notifications
            [
                'user_id' => $buyer?->id,
                'type' => 'order',
                'title' => 'Đơn hàng đã được giao',
                'content' => 'Đơn hàng #1001 của bạn đã được giao thành công. Hãy đánh giá sản phẩm nhé!',
                'read_at' => now(),
                'link' => '/orders/1',
                'created_ago' => 30,
            ],
            [
                'user_id' => $buyer?->id,
                'type' => 'promotion',
                'title' => 'Mã giảm giá mới',
                'content' => 'Bạn nhận được mã giảm 10% cho đơn hàng tiếp theo!',
                'read_at' => null,
                'link' => '/promotions',
                'created_ago' => 7,
            ],
            [
                'user_id' => $buyer?->id,
                'type' => 'message',
                'title' => 'Tin nhắn mới từ Shop VietMarket',
                'content' => 'Shop VietMarket đã gửi tin nhắn cho bạn.',
                'read_at' => null,
                'link' => '/chat',
                'created_ago' => 1,
            ],
            // Seller notifications
            [
                'user_id' => $seller?->id,
                'type' => 'order',
                'title' => 'Đơn hàng mới',
                'content' => 'Bạn có đơn hàng mới #1002 đang chờ xác nhận.',
                'read_at' => null,
                'link' => '/seller/orders',
                'created_ago' => 2,
            ],
            [
                'user_id' => $seller?->id,
                'type' => 'review',
                'title' => 'Đánh giá mới',
                'content' => 'Sản phẩm iPhone 15 Pro Max vừa nhận được đánh giá 5 sao.',
                'read_at' => now(),
                'link' => '/seller/reviews',
                'created_ago' => 5,
            ],
            // Admin notifications
            [
                'user_id' => $admin?->id,
                'type' => 'system',
                'title' => 'Yêu cầu duyệt tin mới',
                'content' => 'Có 3 tin đăng mới đang chờ duyệt.',
                'read_at' => null,
                'link' => '/admin/posts',
                'created_ago' => 1,
            ],
        ];

        foreach ($notifications as $notif) {
            if (!$notif['user_id'])
                continue;

            $exists = DB::table('notifications')
                ->where('user_id', $notif['user_id'])
                ->where('title', $notif['title'])
                ->exists();

            if ($exists)
                continue;

            DB::table('notifications')->insert([
                'user_id' => $notif['user_id'],
                'type' => $notif['type'],
                'title' => $notif['title'],
                'content' => $notif['content'],
                'read_at' => $notif['read_at'],
                'link' => $notif['link'],
                'expired_date' => null,
                'created_at' => now()->subDays($notif['created_ago']),
                'updated_at' => now()->subDays($notif['created_ago']),
            ]);
        }
    }
}
