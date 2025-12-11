<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    protected string $disk;

    public function __construct()
    {
        // Xác định disk dựa trên environment
        $this->disk = env('FILESYSTEM_DRIVER', 'public') === 'gcs' ? 'gcs' : 'public';
    }

    public function upload(UploadedFile $file, string $folder = 'uploads'): string
    {
        // Tạo tên file unique
        $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

        // Upload vào disk tương ứng
        $path = $file->storeAs($folder, $fileName, $this->disk);

        // Trả về URL
        if ($this->disk === 'gcs') {
            return Storage::disk('gcs')->url($path);
        }

        return Storage::url($path);
    }

    public function delete(string $path): bool
    {
        if ($this->disk === 'gcs') {
            // Xử lý URL GCS
            $bucketUrl = config('filesystems.disks.gcs.storage_api_uri', 'https://storage.googleapis.com');
            $bucket = config('filesystems.disks.gcs.bucket');
            $prefix = $bucketUrl . '/' . $bucket . '/';

            if (str_starts_with($path, $prefix)) {
                $relativePath = str_replace($prefix, '', $path);
                if (Storage::disk('gcs')->exists($relativePath)) {
                    return Storage::disk('gcs')->delete($relativePath);
                }
            }
            return false;
        }

        // Xử lý local storage
        $relativePath = str_replace('/storage/', '', $path);
        if (Storage::disk('public')->exists($relativePath)) {
            return Storage::disk('public')->delete($relativePath);
        }
        return false;
    }
}