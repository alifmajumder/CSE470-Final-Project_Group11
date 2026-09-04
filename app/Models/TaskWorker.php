<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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
        'completion_photo_path',
        'completion_photo_uploaded_at',
        'contract_otp',
        'contract_confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
            'completed_at' => 'datetime',
            'completion_photo_uploaded_at' => 'datetime',
            'contract_confirmed_at' => 'datetime',
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

    public function hasCompletionPhoto(): bool
    {
        return ! empty($this->completion_photo_path);
    }

    public function completionPhotoUrl(): ?string
    {
        return $this->hasCompletionPhoto()
            ? Storage::disk('public')->url($this->completion_photo_path)
            : null;
    }

    public function isContractSigned(): bool
    {
        return ! is_null($this->contract_confirmed_at);
    }
}