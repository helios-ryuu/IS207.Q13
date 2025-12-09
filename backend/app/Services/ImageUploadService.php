<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    public function upload(UploadedFile $file, string $folder = 'uploads'): string
    {
        // Tạo tên file unique
        $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        // Upload vào storage/app/public/{folder}
        $path = $file->storeAs($folder, $fileName, 'public');
        
        // Trả về đường dẫn public (cần chạy php artisan storage:link)
        return Storage::url($path);
    }

    public function delete(string $path): bool
    {
        // Chuyển đổi URL public thành đường dẫn relative trong storage
        $relativePath = str_replace('/storage/', '', $path);
        if (Storage::disk('public')->exists($relativePath)) {
            return Storage::disk('public')->delete($relativePath);
        }
        return false;
    }
}