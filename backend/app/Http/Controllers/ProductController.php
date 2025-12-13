<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // <--- QUAN TRỌNG: THÊM DÒNG NÀY VÀO

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;
use App\Services\ProductSearchService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Models\ProductVariant; // <--- CÓ THỂ THIẾU DÒNG NÀY
use App\Models\Category;       // <--- CÓ THỂ THIẾU DÒNG NÀY
use App\Models\ProductImage;   // <--- CÓ THỂ THIẾU DÒNG NÀY

class ProductController extends Controller
{
    protected $searchService;

    public function __construct(ProductSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    // 1. GET /api/products
    public function index(Request $request)
    {
        $query = Product::with(['seller', 'variants.images', 'categories']);

        // 1. Lọc theo Danh mục (Category)
        if ($request->filled('category')) {
            $categoryName = $request->category;
            $query->whereHas('categories', function ($q) use ($categoryName) {
                $q->where('name', 'like', '%' . $categoryName . '%');
            });
        }

        // 2. Lọc theo Danh mục con (Subcategory) - QUAN TRỌNG
        if ($request->filled('subcategory')) {
            $subName = $request->subcategory;
            $query->whereHas('categories', function ($q) use ($subName) {
                $q->where('name', 'like', '%' . $subName . '%');
            });
        }

        // 3. Lọc theo Từ khóa (Keyword)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                ->orWhere('description', 'like', '%' . $keyword . '%');
            });
        }

        // 4. Lọc theo Khu vực (Location) - QUAN TRỌNG
        // (Tìm chuỗi địa chỉ trong mô tả sản phẩm)
        if ($request->filled('location') && $request->location != 'Toàn quốc' && $request->location != 'Gần tôi') {
            $location = $request->location;
            $query->where('description', 'like', '%' . $location . '%');
        }

        // 5. Lọc theo Giá
        if ($request->filled('price_min')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('price', '>=', $request->price_min);
            });
        }
        if ($request->filled('price_max')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('price', '<=', $request->price_max);
            });
        }

        // 6. Sắp xếp
        if ($request->has('sort')) {
            if ($request->sort == 'oldest') $query->oldest();
            elseif ($request->sort == 'price_asc') {
                // Sắp xếp đơn giản theo variant đầu tiên (hoặc join bảng nếu cần chính xác)
                $query->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                    ->orderBy('product_variants.price', 'asc')
                    ->select('products.*')
                    ->distinct();
            } 
            elseif ($request->sort == 'price_desc') {
                $query->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                    ->orderBy('product_variants.price', 'desc')
                    ->select('products.*')
                    ->distinct();
            } 
            else $query->latest();
        } else {
            $query->latest();
        }

        $products = $query->paginate($request->get('per_page', 20));
        return ProductResource::collection($products);
    }

    // 2. GET /api/products/{id}
    public function show($id)
    {
        $product = Product::with(['variants.images', 'categories', 'seller'])->findOrFail($id);
        return new ProductResource($product);
    }

    // 3. POST /api/products (Transaction để tạo Product + Variant + Category)
    public function store(StoreProductRequest $request)
    {
        // Check policy
        if (!Gate::allows('create', Product::class)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'status' => 'active',
                'seller_id' => auth()->id(),
            ]);

            // Attach Categories
            $product->categories()->attach($request->category_ids);

            // Create Variants
            foreach ($request->variants as $variantData) {
                $product->variants()->create($variantData);
            }

            DB::commit();

            return new ProductResource($product->load('variants'));

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error creating product: ' . $e->getMessage()], 500);
        }
    }

    // 4. PUT /api/products/{id}
    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);

        if (auth()->id() !== $product->seller_id) {
            return response()->json(['message' => 'Bạn không có quyền sửa tin này'], 403);
        }

        // 3. Validate dữ liệu
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'nullable|numeric', // Giá có thể gửi lên hoặc không
            'status' => 'nullable|in:active,hidden,sold',
        ]);

        // 4. Cập nhật bảng Product (Tên, Mô tả, Trạng thái)
        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'status' => $validated['status'] ?? $product->status,
        ]);

        // 5. Cập nhật bảng Variant (Giá) - QUAN TRỌNG
        // Lấy variant đầu tiên để cập nhật giá (giả định tin thường chỉ có 1 variant)
        if ($request->has('price')) {
            $variant = $product->variants()->first();
            if ($variant) {
                $variant->update(['price' => $request->price]);
            }
        }

        if ($request->has('category_ids')) {
            $product->categories()->sync($request->category_ids);
        }

        return new ProductResource($product->load('variants'));
    }

    // 5. DELETE /api/products/{id} (Soft Delete)
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if (auth()->id() !== $product->seller_id && auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $product->delete(); // Soft delete do Model có trait SoftDeletes

        return response()->json(['message' => 'Product deleted successfully']);
    }

    // 10. GET /api/products/seller/{sellerId}
    public function getSellerProducts($sellerId)
    {
        $products = Product::where('seller_id', $sellerId)
            ->with(['variants.images'])
            ->paginate(10);

        return new ProductCollection($products);
    }

    // 11. GET /api/products/{id}/similar - Lấy sản phẩm tương tự
    public function similar($id)
    {
        $product = Product::with('categories')->findOrFail($id);

        // Lấy category IDs của sản phẩm hiện tại
        $categoryIds = $product->categories->pluck('id')->toArray();

        // Tìm sản phẩm cùng category, không bao gồm sản phẩm hiện tại
        $similarProducts = Product::with(['variants.images', 'categories', 'seller'])
            ->where('id', '!=', $id)
            ->where('status', 'active')
            ->where(function ($query) use ($categoryIds, $product) {
                // Cùng category
                $query->whereHas('categories', function ($q) use ($categoryIds) {
                    $q->whereIn('categories.id', $categoryIds);
                })
                    // Hoặc cùng seller
                    ->orWhere('seller_id', $product->seller_id);
            })
            ->inRandomOrder()
            ->take(8)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $similarProducts->map(function ($p) {
                $variant = $p->variants->first();
                return [
                    'id' => $p->id,
                    'title' => $p->name,
                    'price' => $variant?->price ?? 0,
                    'image' => $variant?->images->first()?->image_url ?? null,
                    'location' => $p->seller?->address ?? 'Chưa xác định',
                    'seller' => $p->seller?->full_name ?? 'Người bán',
                    'category' => $p->categories->first()?->name ?? 'Chưa phân loại',
                ];
            }),
            'total' => $similarProducts->count(),
        ]);
    }

    // 12. GET /api/user/listings - Lấy tin đăng của user hiện tại
    public function getUserListings(\Illuminate\Http\Request $request)
    {
        $userId = auth()->id();

        $query = Product::with(['variants.images', 'categories'])
            ->where('seller_id', $userId);

        // Filter theo status nếu có
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search theo tên
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $listings = $query->orderBy('created_at', 'desc')->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $listings->map(function ($p) {
                $variant = $p->variants->first();
                return [
                    'id' => $p->id,
                    'title' => $p->name,
                    'description' => $p->description,
                    'price' => $variant?->price ?? 0,
                    'image' => $variant?->images->first()?->image_url ?? null,
                    'status' => $p->status,
                    'category' => $p->categories->first()?->name ?? 'Chưa phân loại',
                    'views' => 0, // TODO: implement view tracking
                    'created_at' => $p->created_at->format('Y-m-d'),
                    'updated_at' => $p->updated_at->format('Y-m-d'),
                ];
            }),
            'pagination' => [
                'current_page' => $listings->currentPage(),
                'last_page' => $listings->lastPage(),
                'per_page' => $listings->perPage(),
                'total' => $listings->total(),
            ],
        ]);
    }
}