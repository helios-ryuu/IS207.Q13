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
}