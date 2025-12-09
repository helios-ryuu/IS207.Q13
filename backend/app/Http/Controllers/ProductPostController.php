<?php

namespace App\Http\Controllers;

use App\Models\ProductPost;
use Illuminate\Http\Request;

class ProductPostController extends Controller
{
    /**
     * Get product posts list (with filtering by status)
     */
    public function index(Request $request)
    {
        $query = ProductPost::with(['product', 'seller']);

        // Filter by status (map frontend values to DB values)
        if ($request->has('status')) {
            $statusMap = [
                'pending' => 'draft',
                'approved' => 'published',
                'rejected' => 'rejected',
            ];
            $dbStatus = $statusMap[$request->status] ?? $request->status;
            $query->where('status', $dbStatus);
        }

        $posts = $query->orderBy('created_at', 'desc')->get();

        $data = $posts->map(function ($post) {
            return [
                'id' => 'P' . str_pad($post->id, 4, '0', STR_PAD_LEFT),
                'title' => $post->title,
                'author' => $post->seller ? $post->seller->full_name : 'Unknown',
                'date' => $post->posted_date ? $post->posted_date->format('Y-m-d') : $post->created_at->format('Y-m-d'),
                'category' => $post->product && $post->product->categories->first() 
                    ? $post->product->categories->first()->name 
                    : 'Chưa phân loại',
                'price' => $post->product 
                    ? number_format($post->product->price, 0, ',', '.') . '₫' 
                    : 'N/A',
                'status' => $post->status,
                'raw_id' => $post->id,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Show single post detail
     */
    public function show($id)
    {
        $post = ProductPost::with(['product', 'seller', 'admin'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $post->id,
                'title' => $post->title,
                'description' => $post->description,
                'status' => $post->status,
                'seller' => $post->seller ? [
                    'id' => $post->seller->id,
                    'name' => $post->seller->full_name,
                    'email' => $post->seller->email,
                ] : null,
                'product' => $post->product ? [
                    'id' => $post->product->id,
                    'name' => $post->product->name,
                    'price' => $post->product->price,
                ] : null,
                'admin' => $post->admin ? [
                    'id' => $post->admin->id,
                    'name' => $post->admin->full_name,
                ] : null,
                'posted_date' => $post->posted_date,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
            ],
        ]);
    }

    /**
     * Approve a product post
     */
    public function approve(Request $request, $id)
    {
        $post = ProductPost::findOrFail($id);

        if ($post->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể phê duyệt bài đăng đang chờ duyệt.',
            ], 400);
        }

        $post->status = 'published';
        $post->admin_id = $request->user()->id;
        $post->save();

        return response()->json([
            'success' => true,
            'message' => 'Bài đăng đã được phê duyệt thành công.',
            'data' => $post,
        ]);
    }

    /**
     * Reject a product post
     */
    public function reject(Request $request, $id)
    {
        $post = ProductPost::findOrFail($id);

        if ($post->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể từ chối bài đăng đang chờ duyệt.',
            ], 400);
        }

        $post->status = 'rejected';
        $post->admin_id = $request->user()->id;
        $post->save();

        return response()->json([
            'success' => true,
            'message' => 'Bài đăng đã bị từ chối.',
            'data' => $post,
        ]);
    }
}
