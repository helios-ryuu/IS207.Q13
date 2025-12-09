<?php

namespace App\Services;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageService
{
    public function sendMessage(array $data)
    {
        // Lấy ID người đang đăng nhập
        $senderId = Auth::id();

        // Tạo tin nhắn mới
        return Message::create([
            'sender_id'   => $senderId,
            'receiver_id' => $data['receiver_id'],
            'content'     => $data['content'],
            'sent_date'   => now(), // Tự động lấy giờ hiện tại
            'status'      => 'sent', // Mặc định trạng thái là đã gửi
        ]);
    }

    public function getConversations()
    {
        $userId = Auth::id();

        // Lấy tin nhắn mới nhất của từng cặp user
        // Logic: Tìm tất cả tin nhắn liên quan đến user, sắp xếp giảm dần theo ngày
        $conversations = Message::where(function($q) use ($userId) {
                $q->where('sender_id', $userId)
                ->orWhere('receiver_id', $userId);
            })
            ->orderBy('sent_date', 'desc')
            ->get()
            ->unique(function ($message) use ($userId) {
                // Group theo ID người kia (nếu mình gửi thì lấy id người nhận, mình nhận thì lấy id người gửi)
                return $message->sender_id == $userId ? $message->receiver_id : $message->sender_id;
            });

        return $conversations;
    }

    // 1. Lấy chi tiết tin nhắn giữa mình và một user khác (Chat history)
    public function getMessagesWithUser($partnerId)
    {
        $myId = Auth::id();

        return Message::where(function($q) use ($myId, $partnerId) {
                // Tin mình gửi cho họ
                $q->where('sender_id', $myId)->where('receiver_id', $partnerId);
            })
            ->orWhere(function($q) use ($myId, $partnerId) {
                // Tin họ gửi cho mình
                $q->where('sender_id', $partnerId)->where('receiver_id', $myId);
            })
            ->orderBy('sent_date', 'asc') // Sắp xếp tin cũ trước, mới sau
            ->get();
    }

    // 2. Đánh dấu một tin nhắn là "đã đọc"
    public function markAsRead($messageId)
    {
        // Tìm tin nhắn mà người nhận là mình
        $message = Message::where('id', $messageId)
                          ->where('receiver_id', Auth::id())
                          ->firstOrFail(); // Nếu không tìm thấy hoặc không phải của mình thì báo lỗi 404
        
        $message->update(['status' => 'read']);
        return $message;
    }

    // 3. Đếm tổng số tin chưa đọc
    public function getUnreadCount()
    {
        return Message::where('receiver_id', Auth::id())
                      ->where('status', '!=', 'read') // Giả sử status khác 'read' là chưa đọc
                      ->count();
    }
}
