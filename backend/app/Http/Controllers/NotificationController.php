<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // 1. Lấy danh sách thông báo
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10); // Phân trang 10 cái 1 lần

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    // 2. Đánh dấu 1 cái đã đọc
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $notification->update(['read_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Đã đánh dấu là đã đọc']);
    }

    // 3. Đánh dấu TẤT CẢ đã đọc
    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Đã đánh dấu tất cả là đã đọc']);
    }

    // 4. Đếm số lượng chưa đọc
    public function unreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['success' => true, 'data' => ['unread_count' => $count]]);
    }
}