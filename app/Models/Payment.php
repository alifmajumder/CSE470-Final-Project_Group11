<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

class Payment extends Model
{
    protected $fillable = [
        'receipt_number',
        'task_id',
        'employer_id',
        'worker_id',
        'amount',
        'method',
        'transaction_reference',
        'note',
        'paid_at',
        'worker_confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'worker_confirmed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            if (empty($payment->receipt_number)) {
                $payment->receipt_number = static::generateReceiptNumber();
            }
        });
    }

    /**
     * Generate a unique, human-readable receipt number like RC-20260722-4F9K2A.
     */
    public static function generateReceiptNumber(): string
    {
        do {
            $candidate = 'RC-'.Date::now()->format('Ymd').'-'.strtoupper(Str::random(6));
        } while (static::where('receipt_number', $candidate)->exists());

        return $candidate;
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    public function isConfirmedByWorker(): bool
    {
        return $this->worker_confirmed_at !== null;
    }

    public function methodLabel(): string
    {
        return config("skills.payment_methods.{$this->method}", ucfirst($this->method));
    }
}
