<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // 14. GET /api/categories
    public function index()
    {
        $categories = Category::all();
        return CategoryResource::collection($categories);
    }

    // 15. GET /api/categories/{id}
    public function show($id)
    {
        $category = Category::with(['products.variants'])->findOrFail($id);
        return new CategoryResource($category);
    }

    // 16. GET /api/categories/{id}/subcategories
    public function subcategories($id)
    {
        // Database của bạn không có cấu trúc parent_id cho subcategories
        // Nên tôi trả về rỗng hoặc logic tùy chỉnh của bạn sau này.
        return response()->json(['message' => 'This feature requires parent_id column in categories table'], 501);
    }
}