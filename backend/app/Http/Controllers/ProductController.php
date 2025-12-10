<?php

namespace App\Http\Controllers;

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
    public function index(\Illuminate\Http\Request $request)
    {
        $products = $this->searchService->search($request);
        return new ProductCollection($products);
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
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $product->update($request->validated());

        if ($request->has('category_ids')) {
            $product->categories()->sync($request->category_ids);
        }

        return new ProductResource($product);
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