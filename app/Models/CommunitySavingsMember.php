<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunitySavingsMember extends Model
{
    protected $fillable = [
        'pool_id',
        'user_id',
        'auto_deposit_amount',
        'auto_deposit_frequency',
        'next_auto_deposit_at',
        'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'auto_deposit_amount' => 'decimal:2',
            'next_auto_deposit_at' => 'date',
            'joined_at' => 'datetime',
        ];
    }

    public function pool(): BelongsTo
    {
        return $this->belongsTo(CommunitySavingsPool::class, 'pool_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}