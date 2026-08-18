<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBadge extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'badge_label',
        'jobs_completed_at_award',
        'earned_at',
    ];

    protected function casts(): array
    {
        return [
            'earned_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function icon(): string
    {
        return config("skills.badges.{$this->category}.icon", '🏅');
    }
}
