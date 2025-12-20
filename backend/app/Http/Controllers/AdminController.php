<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductPost;
use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        try {
            // Total users
            $totalUsers = User::count();
            $usersLastMonth = User::where('created_at', '>=', now()->subMonth())->count();
            $usersGrowth = 0;
            if ($totalUsers > 0 && $usersLastMonth > 0) {
                $usersGrowth = round((($usersLastMonth / $totalUsers) * 100), 1);
            }

            // Pending posts (pending status in products table)
            $postsPending = Product::where('status', 'pending')->count();
            $postsLastWeek = Product::where('status', 'pending')
                ->where('created_at', '>=', now()->subWeek())
                ->count();
            $postsGrowth = 0;
            if ($postsPending > 0) {
                $postsGrowth = round((($postsLastWeek / $postsPending) * 100) - 100, 1);
            }

            // Sales this month (calculated from order_details)
            $salesThisMonth = DB::table('orders')
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->where('orders.created_at', '>=', now()->startOfMonth())
                ->whereIn('orders.status', ['completed', 'delivered'])
                ->sum(DB::raw('order_details.quantity * order_details.unit_price'));

            $salesLastMonth = DB::table('orders')
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->whereBetween('orders.created_at', [
                    now()->subMonth()->startOfMonth(),
                    now()->subMonth()->endOfMonth()
                ])
                ->whereIn('orders.status', ['completed', 'delivered'])
                ->sum(DB::raw('order_details.quantity * order_details.unit_price'));

            $salesGrowth = 0;
            if ($salesLastMonth > 0) {
                $salesGrowth = round((($salesThisMonth - $salesLastMonth) / $salesLastMonth) * 100, 1);
            }

            // Active users today
            $activeUsersToday = User::where('updated_at', '>=', now()->startOfDay())
                ->where('status', 'active')
                ->count();
            $activeUsersYesterday = User::whereBetween('updated_at', [
                now()->subDay()->startOfDay(),
                now()->subDay()->endOfDay()
            ])
                ->where('status', 'active')
                ->count();
            $activeGrowth = 0;
            if ($activeUsersYesterday > 0) {
                $activeGrowth = round((($activeUsersToday - $activeUsersYesterday) / $activeUsersYesterday) * 100, 1);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'users' => $totalUsers,
                    'usersGrowth' => ($usersGrowth >= 0 ? '+' : '') . $usersGrowth . '%',
                    'postsPending' => $postsPending,
                    'postsGrowth' => ($postsGrowth >= 0 ? '+' : '') . $postsGrowth . '%',
                    'sales' => '₫' . number_format($salesThisMonth, 0, ',', ','),
                    'salesGrowth' => ($salesGrowth >= 0 ? '+' : '') . $salesGrowth . '%',
                    'activeUsers' => $activeUsersToday,
                    'activeGrowth' => ($activeGrowth >= 0 ? '+' : '') . $activeGrowth . '%',
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard stats error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải thống kê: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities()
    {
        $activities = [];

        // Recent user registrations
        $recentUsers = User::orderBy('created_at', 'desc')
            ->take(3)
            ->get(['full_name', 'created_at']);

        foreach ($recentUsers as $user) {
            $activities[] = [
                'action' => 'Người dùng mới đăng ký',
                'user' => $user->full_name,
                'time' => $user->created_at->diffForHumans(),
            ];
        }

        // Recent approved products
        $approvedProducts = Product::where('status', 'active')
            ->orderBy('updated_at', 'desc')
            ->with('seller')
            ->take(2)
            ->get();

        foreach ($approvedProducts as $product) {
            $activities[] = [
                'action' => 'Sản phẩm được phê duyệt',
                'user' => $product->seller ? $product->seller->full_name : 'Unknown',
                'time' => $product->updated_at->diffForHumans(),
            ];
        }

        // Recent completed orders
        $completedOrders = Order::where('status', 'completed')
            ->orderBy('updated_at', 'desc')
            ->with('user')
            ->take(2)
            ->get();

        foreach ($completedOrders as $order) {
            $activities[] = [
                'action' => 'Giao dịch thành công',
                'user' => $order->user ? $order->user->full_name : 'Unknown',
                'time' => $order->updated_at->diffForHumans(),
            ];
        }

        // Sort by time and take latest 5
        usort($activities, function ($a, $b) {
            return strcmp($b['time'], $a['time']);
        });

        return response()->json([
            'success' => true,
            'data' => array_slice($activities, 0, 5),
        ]);
    }

    /**
     * Get pending products for approval
     */
    public function getPendingProducts()
    {
        $products = Product::where('status', 'pending')
            ->with(['seller', 'variants.images', 'categories'])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $products->map(function ($product) {
            $variant = $product->variants->first();
            return [
                'id' => $product->id,
                'title' => $product->name,
                'author' => $product->seller ? $product->seller->full_name : 'Unknown',
                'date' => $product->created_at->format('Y-m-d'),
                'category' => $product->categories->first()?->name ?? 'Chưa phân loại',
                'price' => $variant ? number_format((float) ($variant->price ?? 0), 0, ',', '.') . '₫' : 'N/A',
                'image' => $variant?->images->first()?->full_image_url ?? null,
                'location' => $product->location ?? 'Toàn quốc',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Approve a product
     */
    public function approveProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if ($product->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể phê duyệt sản phẩm đang chờ duyệt.',
            ], 400);
        }

        $product->status = 'active';
        $product->save();

        // Gửi thông báo cho seller
        $this->notificationService->create(
            $product->seller_id,
            'Bài đăng đã được duyệt',
            "Bài đăng '{$product->name}' đã được phê duyệt và đang hiển thị trên VietMarket.",
            'system',
            "/product/{$product->id}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Sản phẩm đã được phê duyệt thành công.',
            'data' => $product,
        ]);
    }

    /**
     * Reject a product
     */
    public function rejectProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if ($product->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể từ chối sản phẩm đang chờ duyệt.',
            ], 400);
        }

        $product->status = 'rejected';
        $product->save();

        // Gửi thông báo cho seller
        $this->notificationService->create(
            $product->seller_id,
            'Bài đăng bị từ chối',
            "Bài đăng '{$product->name}' đã bị từ chối. Vui lòng kiểm tra và chỉnh sửa lại nội dung.",
            'system',
            "/manage-posts"
        );

        return response()->json([
            'success' => true,
            'message' => 'Sản phẩm đã bị từ chối.',
            'data' => $product,
        ]);
    }
}

