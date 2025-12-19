<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty())
            return;

        $notificationTemplates = [
            // Customer notifications
            ['role' => 'seller', 'type' => 'order', 'title' => 'Đơn hàng đã được giao', 'content' => 'Đơn hàng của bạn đã được giao thành công. Hãy đánh giá sản phẩm nhé!', 'link' => '/orders'],
            ['role' => 'seller', 'type' => 'order', 'title' => 'Đơn hàng đang giao', 'content' => 'Đơn hàng của bạn đang trên đường giao. Vui lòng giữ điện thoại để nhận hàng.', 'link' => '/orders'],
            ['role' => 'seller', 'type' => 'promotion', 'title' => 'Mã giảm giá mới', 'content' => 'Bạn nhận được mã giảm 10% cho đơn hàng tiếp theo!', 'link' => '/promotions'],
            ['role' => 'seller', 'type' => 'promotion', 'title' => 'Flash Sale 50%', 'content' => 'Khuyến mãi khủng hôm nay - giảm đến 50% tất cả sản phẩm điện tử!', 'link' => '/flash-sale'],
            ['role' => 'seller', 'type' => 'message', 'title' => 'Tin nhắn mới từ Shop', 'content' => 'Bạn có tin nhắn mới từ người bán.', 'link' => '/chat'],
            ['role' => 'seller', 'type' => 'system', 'title' => 'Cập nhật tài khoản', 'content' => 'Thông tin tài khoản của bạn đã được cập nhật thành công.', 'link' => '/profile'],

            // Seller notifications
            ['role' => 'seller', 'type' => 'order', 'title' => 'Đơn hàng mới', 'content' => 'Bạn có đơn hàng mới đang chờ xác nhận.', 'link' => '/seller/orders'],
            ['role' => 'seller', 'type' => 'order', 'title' => 'Đơn hàng đã thanh toán', 'content' => 'Khách hàng đã thanh toán đơn hàng. Vui lòng chuẩn bị hàng.', 'link' => '/seller/orders'],
            ['role' => 'seller', 'type' => 'review', 'title' => 'Đánh giá mới', 'content' => 'Sản phẩm của bạn vừa nhận được đánh giá 5 sao.', 'link' => '/seller/reviews'],
            ['role' => 'seller', 'type' => 'review', 'title' => 'Đánh giá mới', 'content' => 'Khách hàng để lại phản hồi cho sản phẩm của bạn.', 'link' => '/seller/reviews'],
            ['role' => 'seller', 'type' => 'message', 'title' => 'Tin nhắn mới từ khách', 'content' => 'Khách hàng gửi tin nhắn hỏi về sản phẩm.', 'link' => '/seller/chat'],
            ['role' => 'seller', 'type' => 'system', 'title' => 'Doanh thu tháng này', 'content' => 'Xem báo cáo doanh thu tháng này của bạn.', 'link' => '/seller/revenue'],

            // Admin notifications
            ['role' => 'admin', 'type' => 'system', 'title' => 'Yêu cầu duyệt tin mới', 'content' => 'Có tin đăng mới đang chờ duyệt.', 'link' => '/admin/posts'],
            ['role' => 'admin', 'type' => 'system', 'title' => 'Báo cáo vi phạm', 'content' => 'Có báo cáo vi phạm mới cần xử lý.', 'link' => '/admin/reports'],
            ['role' => 'admin', 'type' => 'system', 'title' => 'Đăng ký seller mới', 'content' => 'Có yêu cầu đăng ký bán hàng mới.', 'link' => '/admin/sellers'],
        ];

        $notifCount = 0;
        $targetCount = 30;

        foreach ($users as $user) {
            // Filter templates by role
            $userTemplates = array_filter($notificationTemplates, fn($t) => $t['role'] === $user->role);
            if (empty($userTemplates))
                continue;

            // Each user gets 2-4 notifications
            $numNotifs = rand(2, 4);

            for ($i = 0; $i < $numNotifs && $notifCount < $targetCount; $i++) {
                $template = $userTemplates[array_rand($userTemplates)];
                $createdDays = rand(0, 14);
                $isRead = rand(0, 1);

                $exists = DB::table('notifications')
                    ->where('user_id', $user->id)
                    ->where('title', $template['title'])
                    ->where('content', $template['content'])
                    ->exists();

                if ($exists)
                    continue;

                DB::table('notifications')->insert([
                    'user_id' => $user->id,
                    'type' => $template['type'],
                    'title' => $template['title'],
                    'content' => $template['content'],
                    'read_at' => $isRead ? now()->subDays(max(0, $createdDays - rand(0, 2))) : null,
                    'link' => $template['link'],
                    'expired_date' => null,
                    'created_at' => now()->subDays($createdDays),
                    'updated_at' => now()->subDays($createdDays),
                ]);

                $notifCount++;
            }
        }
    }
}
