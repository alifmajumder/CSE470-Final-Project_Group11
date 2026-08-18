<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'district',
        'role',
        'is_verified',
        'upazila',
        'skills',
        'nid',
        'sms_opt_in',
        'locale',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'skills' => 'array',
            'sms_opt_in' => 'boolean',
        ];
    }

    /** Tasks this user has posted as an employer. */
    public function postedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'employer_id');
    }

    /** Task sign-ups/completions where this user is the worker. */
    public function taskAssignments(): HasMany
    {
        return $this->hasMany(TaskWorker::class, 'worker_id');
    }

    /** Payments received by this user as a worker. */
    public function paymentsReceived(): HasMany
    {
        return $this->hasMany(Payment::class, 'worker_id');
    }

    /** Payments made by this user as an employer. */
    public function paymentsMade(): HasMany
    {
        return $this->hasMany(Payment::class, 'employer_id');
    }

    /** Skill badges this user has earned. */
    public function badges(): HasMany
    {
        return $this->hasMany(UserBadge::class);
    }
    /** Calculate the average 5-star trust score for this worker. */
    public function averageTrustScore(): float
    {
        return (float) $this->taskAssignments()
            ->whereNotNull('employer_rating')
            ->avg('employer_rating');
    }
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->latest();
    }
}