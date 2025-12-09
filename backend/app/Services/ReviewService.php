<?php

namespace App\Services;

use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    // Lấy review của 1 sản phẩm
    public function getProductReviews($productId)
    {
        return Review::where('product_id', $productId)
                     ->with('user') // Load sẵn thông tin user để đỡ query nhiều
                     ->orderBy('review_date', 'desc')
                     ->get();
    }

    // Tạo review mới
    public function createReview(array $data)
    {
        // TODO: Ở đây nên thêm logic kiểm tra "Đã mua hàng chưa" (Order check)
        // Tạm thời cho phép review thoải mái để test
        
        return Review::create([
            'user_id' => Auth::id(),
            'product_id' => $data['product_id'],
            'rating' => $data['rating'],
            'content' => $data['content'],
            'review_date' => now(),
        ]);
    }

    // Thống kê sao (Ví dụ: 5 sao có 10 người, 4 sao có 2 người...)
    public function getReviewStats($productId)
    {
        $stats = Review::where('product_id', $productId)
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->pluck('count', 'rating') // Trả về dạng [5 => 10, 4 => 2]
            ->toArray();

        $average = Review::where('product_id', $productId)->avg('rating');
        $total = Review::where('product_id', $productId)->count();

        return [
            'average_rating' => round($average, 1), // Làm tròn 1 số lẻ (4.5)
            'total_reviews' => $total,
            'breakdown' => $stats, // Chi tiết từng sao
        ];
    }
    
    // Xóa review (Chỉ cho phép xóa của chính mình)
    public function deleteReview($reviewId)
    {
        $review = Review::where('id', $reviewId)->where('user_id', Auth::id())->firstOrFail();
        return $review->delete();
    }
}