<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Tạo thông báo mới cho user
     * 
     * @param int $userId ID người nhận
     * @param string $title Tiêu đề
     * @param string $content Nội dung
     * @param string $type Loại thông báo (system, order, promotion, account)
     * @param string|null $link Đường dẫn liên quan (optional)
     * @return Notification
     */
    public function create($userId, $title, $content, $type = 'system', $link = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'content' => $content,
            'type' => $type,
            'link' => $link,
        ]);
    }

    /**
     * Thông báo cho nhiều user (ví dụ Admin gửi toàn hệ thống - chưa dùng ngay nhưng keep for future)
     */
    public function broadcast($title, $content, $type = 'system', $link = null)
    {
        // Logic gửi cho all users hoặc group user
    }
}
