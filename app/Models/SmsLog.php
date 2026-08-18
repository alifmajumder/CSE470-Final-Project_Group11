<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * SmsLog — persistent record of every SMS send attempt.
 *
 * @property int         $id
 * @property string      $phone
 * @property string      $message
 * @property string      $status            pending|sent|failed
 * @property string|null $provider_response Raw response body from the gateway
 * @property string      $gateway_used      ssl|gp|twilio|log
 * @property float       $cost
 * @property int         $attempt_count
 * @property \Carbon\Carbon|null $sent_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class SmsLog extends Model
{
    protected $fillable = [
        'phone',
        'message',
        'status',
        'provider_response',
        'gateway_used',
        'cost',
        'attempt_count',
        'sent_at',
    ];

    protected $casts = [
        'sent_at'       => 'datetime',
        'cost'          => 'decimal:4',
        'attempt_count' => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Convenience helpers
    // -------------------------------------------------------------------------

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    /**
     * Mark the record as successfully delivered.
     */
    public function markSent(string $providerResponse, float $cost = 0.0): void
    {
        $this->update([
            'status'            => 'sent',
            'provider_response' => $providerResponse,
            'cost'              => $cost,
            'sent_at'           => now(),
        ]);
    }

    /**
     * Increment attempt count and store the error reason.
     * The status is left as 'failed' only by the Job's failed() hook
     * so intermediate retries stay visible as 'pending'.
     */
    public function markFailed(string $reason): void
    {
        $this->update([
            'status'            => 'failed',
            'provider_response' => $reason,
            'attempt_count'     => $this->attempt_count + 1,
        ]);
    }
}
