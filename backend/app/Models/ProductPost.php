<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'posted_date',
        'status',
        'product_id',
        'admin_id',
        'seller_id',
    ];

    protected function casts(): array
    {
        return [
            'posted_date' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }


}
