<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    protected $messageService;

    // Inject Service vào Controller
    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * Gửi tin nhắn mới
     * POST /api/messages
     */
    public function store(StoreMessageRequest $request): JsonResponse
    {
        // 1. Dữ liệu đã được validate ở StoreMessageRequest
        $validatedData = $request->validated();

        // 2. Gọi Service để xử lý logic lưu database
        $message = $this->messageService->sendMessage($validatedData);

        // 3. Trả về kết quả JSON thông qua Resource
        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data'    => new MessageResource($message),
        ], 201);
    }

    public function index()
    {
        $conversations = $this->messageService->getConversations();

        // Dùng ConversationResource để format dữ liệu trả về (mình sẽ tạo ở bước 3)
        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\ConversationResource::collection($conversations),
        ]);
    }

    // API: Xem chi tiết cuộc trò chuyện với 1 người
    // GET /api/messages/users/{userId}
    public function show($userId)
    {
        $messages = $this->messageService->getMessagesWithUser($userId);
        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\MessageResource::collection($messages),
        ]);
    }

    // API: Đánh dấu đã đọc
    // PUT /api/messages/{id}/read
    public function markAsRead($id)
    {
        $this->messageService->markAsRead($id);
        return response()->json([
            'success' => true,
            'message' => 'Message marked as read'
        ]);
    }

    // API: Đếm số tin chưa đọc
    // GET /api/messages/unread-count
    public function unreadCount()
    {
        $count = $this->messageService->getUnreadCount();
        return response()->json([
            'success' => true,
            'data' => ['unread_count' => $count]
        ]);
    }
}