<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class FavoriteController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Lấy danh sách sản phẩm yêu thích của user
     * GET /api/user/favorites
     */
    public function index()
    {
        $userId = Auth::id();

        $favorites = Favorite::with(['product.variants.images', 'product.categories', 'product.seller'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Trả về danh sách products đã được format
        $products = $favorites->map(function ($favorite) {
            return [
                'id' => $favorite->product->id,
                'title' => $favorite->product->name,
                'price' => $favorite->product->variants->first()?->price ?? 0,
                'image' => $favorite->product->variants->first()?->images->first()?->image_url ?? null,
                'location' => $favorite->product->seller?->address ?? 'Chưa xác định',
                'category' => $favorite->product->categories->first()?->name ?? 'Chưa phân loại',
                'condition' => $favorite->product->variants->first()?->condition ?? 'Đã sử dụng',
                'seller' => $favorite->product->seller?->full_name ?? 'Người bán',
                'saved_date' => $favorite->created_at->format('Y-m-d'),
                'posted_date' => $favorite->product->created_at->format('Y-m-d'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $products,
            'total' => $favorites->count(),
        ]);
    }

    /**
     * Thêm sản phẩm vào danh sách yêu thích
     * POST /api/user/favorites/{productId}
     */
    public function store($productId)
    {
        $userId = Auth::id();

        // Kiểm tra sản phẩm tồn tại
        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại',
            ], 404);
        }

        // Kiểm tra đã yêu thích chưa
        $exists = Favorite::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm đã có trong danh sách yêu thích',
            ], 422);
        }

        // Thêm vào favorites
        $favorite = Favorite::create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        $this->notificationService->create($userId, 'Yêu thích', "Bạn đã thêm '{$product->name}' vào danh sách yêu thích.", 'system');

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào danh sách yêu thích',
            'data' => [
                'id' => $favorite->id,
                'product_id' => $productId,
                'created_at' => $favorite->created_at,
            ],
        ], 201);
    }

    /**
     * Xóa sản phẩm khỏi danh sách yêu thích
     * DELETE /api/user/favorites/{productId}
     */
    public function destroy($productId)
    {
        $userId = Auth::id();

        $favorite = Favorite::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if (!$favorite) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không có trong danh sách yêu thích',
            ], 404);
        }

        $favorite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa khỏi danh sách yêu thích',
        ]);
    }

    /**
     * Toggle yêu thích (thêm nếu chưa có, xóa nếu đã có)
     * POST /api/user/favorites/{productId}/toggle
     */
    public function toggle($productId)
    {
        $userId = Auth::id();

        // Kiểm tra sản phẩm tồn tại
        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại',
            ], 404);
        }

        $favorite = Favorite::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($favorite) {
            // Đã có -> Xóa
            $favorite->delete();
            return response()->json([
                'success' => true,
                'is_favorited' => false,
                'message' => 'Đã xóa khỏi danh sách yêu thích',
            ]);
        } else {
            // Chưa có -> Thêm
            Favorite::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);

            $this->notificationService->create($userId, 'Yêu thích', "Bạn đã thêm '{$product->name}' vào danh sách yêu thích.", 'system');

            return response()->json([
                'success' => true,
                'is_favorited' => true,
                'message' => 'Đã thêm vào danh sách yêu thích',
            ]);
        }
    }

    /**
     * Kiểm tra sản phẩm đã được yêu thích chưa
     * GET /api/user/favorites/{productId}/check
     */
    public function check($productId)
    {
        $userId = Auth::id();

        $exists = Favorite::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();

        return response()->json([
            'success' => true,
            'is_favorited' => $exists,
        ]);
    }

    /**
     * Đếm số lượng sản phẩm yêu thích
     * GET /api/user/favorites/count
     */
    public function count()
    {
        $userId = Auth::id();

        $count = Favorite::where('user_id', $userId)->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }
}
