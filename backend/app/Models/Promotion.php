<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'discount_amount',
        'conditions',
        'start_date',
        'end_date',
        'status',
        'max_usage_limit',
        'usage_count',
    ];

    protected function casts(): array
    {
        return [
            'discount_amount' => 'decimal:2',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'max_usage_limit' => 'integer',
            'usage_count' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // Relationships
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'applied_promotions')
            ->withPivot('discounted_amount')
            ->withTimestamps();
    }
}
