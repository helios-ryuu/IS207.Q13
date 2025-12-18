<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_url',
        'variant_id',
    ];

    protected $appends = ['full_image_url'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get full image URL - handles both GCS and local storage
     * Returns the raw image_url if it's already a full URL (GCS/external)
     * Or converts local path to full URL
     */
    public function getFullImageUrlAttribute(): ?string
    {
        $url = $this->attributes['image_url'] ?? null;

        if (!$url) {
            return null;
        }

        // Already a full URL (GCS or external)
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        // Local storage path: /storage/... or products/...
        $disk = config('filesystems.default');

        if ($disk === 'gcs') {
            $bucket = config('filesystems.disks.gcs.bucket');
            $path = ltrim(str_replace('/storage/', '', $url), '/');
            return "https://storage.googleapis.com/{$bucket}/{$path}";
        }

        // Local: return ABSOLUTE URL with APP_URL
        $baseUrl = config('app.url', 'http://localhost:8000');

        if (str_starts_with($url, '/storage/')) {
            return $baseUrl . $url;
        }

        return $baseUrl . '/storage/' . ltrim($url, '/');
    }

    // Relationships
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

