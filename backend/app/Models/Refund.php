<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'refund_amount',
        'reason',
        'status',
        'request_date',
        'approval_date',
        'notes',
        'order_id',
        'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'refund_amount' => 'decimal:2',
            'request_date' => 'datetime',
            'approval_date' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function refundDetails()
    {
        return $this->hasMany(RefundDetail::class);
    }
}
