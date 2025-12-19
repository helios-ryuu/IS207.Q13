<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'content',
        'read_at',
        'link',
        'expired_date',
    ];

    protected $appends = ['is_read'];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'expired_date' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Accessor: is_read
    public function getIsReadAttribute()
    {
        return !is_null($this->read_at);
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
