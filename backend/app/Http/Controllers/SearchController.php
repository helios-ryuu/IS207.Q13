<?php

namespace App\Http\Controllers;

use App\Services\ProductSearchService;
use App\Models\Product;
use App\Http\Resources\ProductCollection;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected $searchService;

    public function __construct(ProductSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    // 17. GET /api/search
    public function search(Request $request)
    {
        $products = $this->searchService->search($request);
        return new ProductCollection($products);
    }

    // 18. GET /api/products/trending
    public function trending()
    {
        // Giả sử trending là nhiều variants (nhiều stock) hoặc random
        // Đúng ra cần bảng views hoặc orders count
        $products = Product::with(['variants.images'])
            ->inRandomOrder()
            ->take(10)
            ->get();
        return new ProductCollection($products);
    }

    // 19. GET /api/products/newest
    public function newest()
    {
        $products = Product::with(['variants.images'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        return new ProductCollection($products);
    }

    // 20. GET /api/products/recommended
    public function recommended()
    {
        // Logic gợi ý cơ bản
        $products = Product::with(['variants.images'])
            ->where('status', 'active')
            ->inRandomOrder()
            ->take(10)
            ->get();
        return new ProductCollection($products);
    }
}