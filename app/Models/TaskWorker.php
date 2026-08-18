<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskWorker extends Model
{
    protected $fillable = [
        'task_id',
        'worker_id',
        'status',
        'joined_at',
        'completed_at',
        'employer_rating',
        'employer_review',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
