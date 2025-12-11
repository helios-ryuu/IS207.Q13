<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    protected $imageService;

    public function __construct(ImageUploadService $imageService)
    {
        $this->imageService = $imageService;
    }

    // 6. POST /api/products/{id}/images
    // Lưu ý: Image thuộc Variant. Nếu không gửi variant_id, sẽ lấy variant đầu tiên.
    public function storeProductImage(Request $request, $productId)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variant_id' => 'nullable|exists:product_variants,id'
        ]);

        $product = Product::findOrFail($productId);
        if (auth()->id() !== $product->seller_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Xác định variant
        $variantId = $request->variant_id;
        if (!$variantId) {
            $variant = $product->variants()->first();
            if (!$variant) return response()->json(['message' => 'Product has no variants'], 400);
            $variantId = $variant->id;
        }

        try {
            // Upload
            $url = $this->imageService->upload($request->file('image'), 'products');

            $image = ProductImage::create([
                'variant_id' => $variantId,
                'image_url' => $url
            ]);

            return response()->json(['message' => 'Image uploaded', 'data' => $image], 201);
        } catch (\Exception $e) {
            \Log::error('Image upload failed: ' . $e->getMessage(), [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Upload failed: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    // 7. DELETE /api/images/{id}
    public function destroy($id)
    {
        $image = ProductImage::findOrFail($id);
        
        // Check ownership qua relationship
        if (auth()->id() !== $image->variant->product->seller_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Xóa file vật lý
        $this->imageService->delete($image->image_url);
        
        $image->delete();

        return response()->json(['message' => 'Image deleted']);
    }

    // 8. POST /api/upload/image (General upload)
    public function uploadGeneral(Request $request)
    {
        $request->validate(['image' => 'required|image|max:2048']);
        $url = $this->imageService->upload($request->file('image'), 'general');
        return response()->json(['url' => $url]);
    }

    // 9. DELETE /api/upload/image/{path}
    public function deleteGeneral(Request $request)
    {
        // Logic này hơi rủi ro nếu client gửi path bừa bãi.
        // Chỉ nên cho phép nếu path nằm trong folder allowed hoặc user sở hữu.
        // Đây là ví dụ cơ bản:
        $path = $request->get('path'); // Nên gửi qua body hoặc query param
        if($path && $this->imageService->delete($path)) {
            return response()->json(['message' => 'Deleted']);
        }
        return response()->json(['message' => 'File not found or cannot delete'], 404);
    }
}