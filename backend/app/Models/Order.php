<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_date',
        'delivery_date',
        'shipping_fee',
        'status',
        'notes',
        'payment_method',
        'tracking_code',
        'user_id',
        'address_id',
        'total_amount', // [FIX] Cho phép mass assignment
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'datetime',
            'delivery_date' => 'datetime',
            'shipping_fee' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class, 'address_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }



    public function sellerReviews()
    {
        return $this->hasMany(SellerReview::class);
    }
}
