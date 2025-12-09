<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'user_id',
        'order_id',
        'rating',
        'content',
        'response_time',
        'shipping_quality',
        'review_date',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'response_time' => 'integer',
            'shipping_quality' => 'integer',
            'review_date' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relationships
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
