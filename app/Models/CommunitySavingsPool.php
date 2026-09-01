<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunitySavingsPool extends Model
{
    protected $fillable = [
        'owner_id',
        'name',
        'purpose',
        'balance',
        'target_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'target_amount' => 'decimal:2',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(CommunitySavingsMember::class, 'pool_id')->with('user');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CommunitySavingsTransaction::class, 'pool_id')->latest('transacted_at');
    }

    public function isMember(int $userId): bool
    {
        return $this->members()->where('user_id', $userId)->exists();
    }
}