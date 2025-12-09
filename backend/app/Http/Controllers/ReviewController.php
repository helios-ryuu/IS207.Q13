<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    // 1. Xem review của sản phẩm
    public function index($productId)
    {
        $reviews = Review::where('product_id', $productId)
                         ->with('user:id,full_name,avatar') // Chỉ lấy tên và ảnh
                         ->orderBy('created_at', 'desc')
                         ->get();

        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    // 2. Viết review
    // POST /api/products/{id}/reviews
    public function store(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string|min:10',
        ]);

        $userId = Auth::id();

        // 1. KIỂM TRA ĐÃ MUA HÀNG CHƯA 
        // Tìm các đơn hàng của user -> đã hoàn thành (status = completed)
        // -> và có chứa variant thuộc về product_id này
        $hasPurchased = \App\Models\Order::where('user_id', $userId)
            ->where('status', 'delivered') 
            ->whereHas('orderDetails', function ($query) use ($productId) {
                // Query vào bảng order_details, tiếp tục check quan hệ 'variant'
                $query->whereHas('variant', function ($q) use ($productId) {
                    // Check xem variant đó có thuộc product_id đang review không
                    $q->where('product_id', $productId);
                });
            })
            ->exists();

      //  KÍCH HOẠT CHẶN NGƯỜI CHƯA MUA
        if (!$hasPurchased) {
             return response()->json(['message' => 'Bạn cần mua và nhận hàng thành công sản phẩm này để đánh giá.'], 403);
        }
      

        // 2. Kiểm tra đã đánh giá chưa
        $exists = Review::where('user_id', $userId)->where('product_id', $productId)->exists();
        if ($exists) {
            return response()->json(['message' => 'Bạn đã đánh giá sản phẩm này rồi.'], 422);
        }

        // 3. Tạo review
        $review = Review::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'rating' => $request->rating,
            'content' => $request->content,
            'review_date' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đánh giá sản phẩm thành công!',
            'data' => new \App\Http\Resources\ReviewResource($review)
        ], 201);
    }
    // 3. Thống kê sao
    public function stats($productId)
    {
        $stats = Review::where('product_id', $productId)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating');

        $avg = Review::where('product_id', $productId)->avg('rating');
        
        return response()->json([
            'success' => true,
            'average' => round($avg, 1),
            'details' => $stats
        ]);
    }

    // 4. Sửa review
    // PUT /api/reviews/{id}
    public function update(Request $request, $id)
    {
        $review = Review::where('id', $id)
                        ->where('user_id', Auth::id()) // Chỉ chủ sở hữu mới được sửa
                        ->first();

        if (!$review) {
            return response()->json(['message' => 'Không tìm thấy đánh giá hoặc bạn không có quyền sửa.'], 404);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string|min:10',
        ]);

        $review->update([
            'rating' => $request->rating,
            'content' => $request->content,
            'updated_at' => now() // Cập nhật thời gian sửa
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật đánh giá thành công',
            'data' => new \App\Http\Resources\ReviewResource($review)
        ]);
    }

    // 5. Xóa review
    // DELETE /api/reviews/{id}
    public function destroy($id)
    {
        $review = Review::where('id', $id)
                        ->where('user_id', Auth::id()) // Chỉ chủ sở hữu mới được xóa
                        ->first();

        if (!$review) {
            return response()->json(['message' => 'Không tìm thấy đánh giá hoặc bạn không có quyền xóa.'], 404);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa đánh giá thành công'
        ]);
    }
}