<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    /**
     * Upload ảnh vào thư mục Public và trả về đường dẫn
     */
    public function upload(UploadedFile $file, string $folder = 'uploads'): string
    {
        // 1. Tạo tên file ngẫu nhiên
        $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

        // 2. Luôn sử dụng disk 'public' cho môi trường Local
        // (Tránh dùng env() trực tiếp để ngăn lỗi cache)
        $path = $file->storeAs($folder, $fileName, 'public');

        // 3. Trả về đường dẫn URL tương đối
        // Frontend sẽ ghép với domain: http://localhost:8000/storage/...
        return '/storage/' . $path;
    }

    /**
     * Xóa ảnh
     */
    public function delete(string $path): bool
    {
        // Chuyển URL '/storage/products/abc.jpg' -> path 'products/abc.jpg'
        $relativePath = str_replace('/storage/', '', $path);

        if (Storage::disk('public')->exists($relativePath)) {
            return Storage::disk('public')->delete($relativePath);
        }

        return false;
    }
}