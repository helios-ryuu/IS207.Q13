<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ProductSearchService
{
    public function search(Request $request)
    {
        $query = Product::query()
            ->with(['variants', 'categories', 'seller', 'variants.images'])
            ->where('status', 'active');

        // Tìm theo từ khóa (tên hoặc mô tả)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // Lọc theo Category
        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        // Lọc theo khoảng giá (Dựa trên variants)
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->whereHas('variants', function (Builder $q) use ($request) {
                if ($request->filled('min_price')) {
                    $q->where('price', '>=', $request->min_price);
                }
                if ($request->filled('max_price')) {
                    $q->where('price', '<=', $request->max_price);
                }
            });
        }

        // Sắp xếp
        if ($request->filled('sort_by')) {
            switch ($request->sort_by) {
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'price_asc':
                    // Sử dụng subquery hoặc min() để sort clean hơn
                    $query->select('products.*')
                        ->selectSub(function ($q) {
                            $q->from('product_variants')
                                ->whereColumn('product_variants.product_id', 'products.id')
                                ->selectRaw('min(price)');
                        }, 'min_price')
                        ->orderBy('min_price', 'asc');
                    break;

                case 'price_desc':
                    $query->select('products.*')
                        ->selectSub(function ($q) {
                            $q->from('product_variants')
                                ->whereColumn('product_variants.product_id', 'products.id')
                                ->selectRaw('max(price)');
                        }, 'max_price')
                        ->orderBy('max_price', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($request->get('per_page', 12));
    }
}