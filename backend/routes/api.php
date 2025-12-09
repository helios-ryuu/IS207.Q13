<?php
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShippingAddressController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ReviewController ;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SellerReviewController;
// --- PUBLIC ROUTES (Ai cũng xem được) ---
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Xem sản phẩm & Search
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/search', [SearchController::class, 'search']);
// ... (Giữ nguyên các route public khác) ...

// Xem Review (Chỉ có GET là Public)
Route::get('/products/{id}/reviews', [ReviewController::class, 'index']);
Route::get('/products/{id}/reviews/stats', [ReviewController::class, 'stats']);
Route::get('/sellers/{id}/reviews', [SellerReviewController::class, 'index']);


// --- PROTECTED ROUTES (Phải đăng nhập) ---
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // MESSAGING
    Route::get('/messages', [MessageController::class, 'index']);
    Route::post('/messages', [MessageController::class, 'store']);
    Route::get('/messages/users/{userId}', [MessageController::class, 'show']);
    Route::put('/messages/{id}/read', [MessageController::class, 'markAsRead']);
    Route::get('/messages/unread-count', [MessageController::class, 'unreadCount']);

    // PRODUCT REVIEWS (Viết/Sửa/Xóa phải ở đây)
    Route::post('/products/{id}/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);

    // SELLER REVIEWS
    Route::post('/sellers/{id}/reviews', [SellerReviewController::class, 'store']);
    Route::put('/seller-reviews/{id}', [SellerReviewController::class, 'update']);

    // USER PROFILE
    Route::get('/user/profile', [UserProfileController::class, 'show']);
    Route::put('/user/profile', [UserProfileController::class, 'update']);
    Route::post('/user/change-password', [UserProfileController::class, 'changePassword']);
    Route::post('/user/change-avatar', [UserProfileController::class, 'changeAvatar']);

    // NOTIFICATION
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllRead']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    // --- PRODUCT REVIEWS (Auth) ---
    Route::post('/products/{id}/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [\App\Http\Controllers\ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);

    // --- SELLER REVIEWS (Auth) ---
    Route::post('/sellers/{id}/reviews', [SellerReviewController::class, 'store']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Product Management
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    
    // Variant Management
    Route::post('/products/{id}/variants', [ProductVariantController::class, 'store']);
    Route::put('/variants/{id}', [ProductVariantController::class, 'update']);
    Route::delete('/variants/{id}', [ProductVariantController::class, 'destroy']);

    // Image Management
    Route::post('/products/{id}/images', [ProductImageController::class, 'storeProductImage']);
    Route::delete('/images/{id}', [ProductImageController::class, 'destroy']);
    Route::post('/upload/image', [ProductImageController::class, 'uploadGeneral']);
    Route::delete('/upload/image/delete', [ProductImageController::class, 'deleteGeneral']); // Sửa path chút để dễ handle

    // --- CART API ---
    Route::get('/cart', [CartController::class, 'index']);             // Xem giỏ
    Route::post('/cart/items', [CartController::class, 'store']);      // Thêm
    Route::put('/cart/items/{id}', [CartController::class, 'update']); // Sửa (id là variant_id)
    Route::delete('/cart/items/{id}', [CartController::class, 'destroy']); // Xóa (id là variant_id)
    Route::delete('/cart', [CartController::class, 'clear']);          // Xóa hết
    // --- SHIPPING ADDRESS API ---
    Route::get('/addresses', [ShippingAddressController::class, 'index']);           // Xem list
    Route::post('/addresses', [ShippingAddressController::class, 'store']);          // Thêm
    Route::put('/addresses/{id}', [ShippingAddressController::class, 'update']);     // Sửa
    Route::delete('/addresses/{id}', [ShippingAddressController::class, 'destroy']); // Xóa
    Route::put('/addresses/{id}/set-default', [ShippingAddressController::class, 'setDefault']); // Đặt mặc định
    // --- ORDER API ---
    Route::post('/orders', [OrderController::class, 'store']);       // 1. Tạo đơn
    Route::get('/orders', [OrderController::class, 'index']);        // 2. Xem list
    Route::get('/orders/{id}', [OrderController::class, 'show']);    // 3. Xem chi tiết
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']); // 4. Hủy đơn
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']); // 5. Cập nhật trạng thái
    // --- WALLET API ---
    Route::get('/wallet', [WalletController::class, 'index']);           // Xem số dư
    Route::post('/wallet/deposit', [WalletController::class, 'deposit']); // Nạp tiền
    Route::post('/wallet/withdraw', [WalletController::class, 'withdraw']); // Rút tiền
    Route::get('/wallet/transactions', [WalletController::class, 'history']); // Lịch sử
    // --- TRANSACTION API ---
    Route::get('/transactions', [TransactionController::class, 'index']);      // Xem danh sách
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);  // Xem chi tiết
    Route::post('/transactions/verify', [TransactionController::class, 'verify']); // Xác thực
});