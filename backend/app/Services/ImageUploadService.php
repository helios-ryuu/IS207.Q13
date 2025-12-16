<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    /**
     * Get the current storage disk from config
     */
    protected function getDisk(): string
    {
        return config('filesystems.default', 'public');
    }

    /**
     * Get GCS bucket name
     */
    protected function getBucket(): string
    {
        return config('filesystems.disks.gcs.bucket', 'vietmarket');
    }

    /**
     * Upload ảnh và trả về đường dẫn URL đầy đủ
     * 
     * @param UploadedFile $file File ảnh cần upload
     * @param string $folder Thư mục lưu trữ (avatars/users, covers/users, products)
     * @return string URL đầy đủ của ảnh
     */
    public function upload(UploadedFile $file, string $folder = 'uploads'): string
    {
        // 1. Tạo tên file ngẫu nhiên
        $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

        // 2. Lấy disk từ config (gcs hoặc public)
        $disk = $this->getDisk();

        // 3. Upload file lên storage
        $path = $file->storeAs($folder, $fileName, $disk);

        // 4. Trả về URL phù hợp với disk
        return $this->getUrl($path, $disk);
    }

    /**
     * Xóa ảnh từ storage
     * 
     * @param string $url URL đầy đủ hoặc path của ảnh
     * @return bool
     */
    public function delete(string $url): bool
    {
        $disk = $this->getDisk();

        // Chuyển URL thành path tương đối
        $relativePath = $this->extractPath($url, $disk);

        if (Storage::disk($disk)->exists($relativePath)) {
            return Storage::disk($disk)->delete($relativePath);
        }

        return false;
    }

    /**
     * Tạo URL đầy đủ từ path
     * 
     * @param string $path Path tương đối trong storage
     * @param string|null $disk Disk sử dụng (null = lấy từ config)
     * @return string URL đầy đủ
     */
    public function getUrl(string $path, ?string $disk = null): string
    {
        $disk = $disk ?? $this->getDisk();

        if ($disk === 'gcs') {
            $bucket = $this->getBucket();
            return "https://storage.googleapis.com/{$bucket}/{$path}";
        }

        // Local storage: /storage/folder/file.jpg
        return '/storage/' . $path;
    }

    /**
     * Trích xuất path tương đối từ URL
     * 
     * @param string $url URL đầy đủ hoặc path
     * @param string|null $disk Disk sử dụng
     * @return string Path tương đối
     */
    public function extractPath(string $url, ?string $disk = null): string
    {
        $disk = $disk ?? $this->getDisk();

        if ($disk === 'gcs') {
            // GCS URL: https://storage.googleapis.com/bucket/folder/file.jpg
            if (str_contains($url, 'storage.googleapis.com')) {
                $parts = parse_url($url);
                $path = ltrim($parts['path'] ?? '', '/');
                // Remove bucket name from path
                $segments = explode('/', $path, 2);
                return $segments[1] ?? $path;
            }
            // Already a relative path
            return $url;
        }

        // Local URL: /storage/folder/file.jpg -> folder/file.jpg
        return str_replace('/storage/', '', $url);
    }

    /**
     * Kiểm tra file có tồn tại không
     * 
     * @param string $path Path tương đối
     * @return bool
     */
    public function exists(string $path): bool
    {
        $disk = $this->getDisk();
        $relativePath = $this->extractPath($path, $disk);
        return Storage::disk($disk)->exists($relativePath);
    }
}