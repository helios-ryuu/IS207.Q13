<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    use HasFactory;

    protected $table = 'inventory_histories';

    protected $fillable = [
        'variant_id',
        'change_type',
        'quantity_change',
        'old_quantity',
        'new_quantity',
        'reason',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity_change' => 'integer',
            'old_quantity' => 'integer',
            'new_quantity' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relationships
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
