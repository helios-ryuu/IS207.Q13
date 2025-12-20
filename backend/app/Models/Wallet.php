<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'balance',
        'status',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id', 'user_id');
    }

    /**
     * Tính số dư ví từ tổng các giao dịch
     * Balance = SUM(transactions.amount) với status = 'completed'
     */
    public function getCalculatedBalanceAttribute(): float
    {
        return (float) Transaction::where('user_id', $this->user_id)
            ->where('status', 'completed')
            ->sum('amount');
    }
}
