<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'employer_id',
        'title',
        'category',
        'description',
        'wage',
        'district',
        'location',
        'required_workers',
        'registered_workers',
        'status',
    ];

    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    /**
     * Workers who have signed up for (or completed) this task.
     * Supports the Payment Record and Skill Badge features.
     */
    public function taskWorkers(): HasMany
    {
        return $this->hasMany(TaskWorker::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function categoryLabel(): string
    {
        return config("skills.categories.{$this->category}", ucfirst($this->category ?? 'other'));
    }
}
