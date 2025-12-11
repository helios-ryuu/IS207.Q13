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

        // Upload vào disk tương ứng
        $path = $file->storeAs($folder, $fileName, $this->disk);

        // Trả về URL
        if ($this->disk === 'gcs') {
            // Manually construct GCS public URL
            $bucket = config('filesystems.disks.gcs.bucket');
            $storageUri = config('filesystems.disks.gcs.storage_api_uri', 'https://storage.googleapis.com');
            return $storageUri . '/' . $bucket . '/' . $path;
        }

        // Return relative URL for local storage (works with any port)
        return '/storage/' . $path;
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