<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'service_id',
        'status',
        'purchase_date',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relationships
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
