<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Http\Requests\StoreVariantRequest;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    // 11. POST /api/products/{id}/variants
    public function store(StoreVariantRequest $request, $productId)
    {
        $product = Product::findOrFail($productId);
        
        if (auth()->id() !== $product->seller_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $variant = $product->variants()->create($request->validated());

        return response()->json(['message' => 'Variant created', 'data' => $variant], 201);
    }

    // 12. PUT /api/variants/{id}
    public function update(StoreVariantRequest $request, $id)
    {
        $variant = ProductVariant::findOrFail($id);
        
        if (auth()->id() !== $variant->product->seller_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $variant->update($request->validated());

        return response()->json(['message' => 'Variant updated', 'data' => $variant]);
    }

    // 13. DELETE /api/variants/{id}
    public function destroy($id)
    {
        $variant = ProductVariant::findOrFail($id);

        if (auth()->id() !== $variant->product->seller_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $variant->delete();

        return response()->json(['message' => 'Variant deleted']);
    }
}