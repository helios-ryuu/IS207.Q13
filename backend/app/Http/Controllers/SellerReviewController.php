<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SellerReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;

class SellerReviewController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    // Xem review của người bán
    public function index($sellerId)
    {
        $reviews = SellerReview::where('seller_id', $sellerId)
            ->with('user:id,full_name,avatar')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\ReviewResource::collection($reviews) // Dùng chung Resource với Product cũng được
        ]);
    }

    // Đánh giá người bán
    // POST /api/sellers/{id}/reviews
    public function store(Request $request, $sellerId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string|min:5',
        ]);

        $userId = Auth::id();

        if ($userId == $sellerId) {
            return response()->json(['message' => 'Không thể tự đánh giá chính mình'], 422);
        }

        // 1. TÌM ĐƠN HÀNG GẦN NHẤT ĐÃ MUA CỦA SHOP NÀY 
        $order = \App\Models\Order::where('user_id', $userId)
            ->where('status', 'delivered') // Đã giao hàng
            ->whereHas('orderDetails.variant.product', function ($query) use ($sellerId) {
                $query->where('seller_id', $sellerId);
            })
            ->latest('order_date') // Lấy đơn mới nhất
            ->first(); // Lấy ra object Order

        // Nếu không tìm thấy đơn nào -> Chặn
        if (!$order) {
            return response()->json(['message' => 'Bạn chưa có giao dịch hoàn thành nào với shop này.'], 403);
        }

        // 2. Check duplicate (Giữ nguyên)
        $exists = \App\Models\SellerReview::where('user_id', $userId)->where('seller_id', $sellerId)->exists();
        if ($exists) {
            return response()->json(['message' => 'Bạn đã đánh giá shop này rồi.'], 422);
        }

        // 3. Lưu Review (KÈM THEO ORDER ID VỪA TÌM ĐƯỢC)
        $review = \App\Models\SellerReview::create([
            'user_id' => $userId,
            'seller_id' => $sellerId,
            'order_id' => $order->id, // 👈 FIX LỖI 1364 Ở ĐÂY
            'rating' => $request->input('rating'),
            'content' => $request->input('content'),
            'review_date' => now()
        ]);

        $this->notificationService->create(
            $sellerId,
            'Đánh giá mới',
            "Bạn nhận được đánh giá {$request->input('rating')} sao từ khách hàng.",
            'review'
        );

        return response()->json([
            'success' => true,
            'message' => 'Đánh giá Shop thành công',
            'data' => $review
        ], 201);
    }

    // Sửa seller review
    // PUT /api/seller-reviews/{id}
    public function update(Request $request, $id)
    {
        // Lưu ý: Dùng Model SellerReview
        $review = SellerReview::where('id', $id)
            ->where('user_id', Auth::id()) // Chỉ chủ sở hữu được sửa
            ->first();

        if (!$review) {
            return response()->json(['message' => 'Không tìm thấy đánh giá hoặc bạn không có quyền sửa.'], 404);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string|min:5',
        ]);

        $review->update([
            'rating' => $request->input('rating'),
            'content' => $request->input('content')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật đánh giá Shop thành công',
            'data' => $review
        ]);
    }
}