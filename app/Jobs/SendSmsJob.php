<?php

namespace App\Jobs;

use App\Models\SmsLog;
use App\Services\SmsGatewayService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSmsJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * Maximum attempts before the job is marked as permanently failed.
     * Retry delays are defined in backoff() below.
     */
    public int $tries = 3;

    public function __construct(
        protected int $smsLogId
    ) {}

    /**
     * Exponential backoff delays between retries (in seconds):
     * 1st retry after 60 s, 2nd after 5 min, 3rd after 15 min.
     */
    public function backoff(): array
    {
        return [60, 300, 900];
    }

    /**
     * Prevent the same SmsLog from being processed by two simultaneous workers.
     */
    public function middleware(): array
    {
        return [new WithoutOverlapping('sms-' . $this->smsLogId)];
    }

    /**
     * Process the queued SMS send.
     * Throws RuntimeException on failure so Laravel retries with the backoff schedule.
     */
    public function handle(SmsGatewayService $gateway): void
    {
        $smsLog = SmsLog::find($this->smsLogId);

        if (!$smsLog) {
            Log::warning('SendSmsJob: SmsLog not found', ['id' => $this->smsLogId]);
            return;
        }

        // Already delivered by a previous attempt — nothing to do.
        if ($smsLog->status === 'sent') {
            return;
        }

        $result = $gateway->send($smsLog->phone, $smsLog->message);

        if ($result['success']) {
            $smsLog->markSent($result['response'], $result['cost']);
            Log::info('SMS sent successfully', [
                'sms_log_id' => $this->smsLogId,
                'phone'      => $smsLog->phone,
                'gateway'    => $smsLog->gateway_used,
            ]);
            return;
        }

        // Track every failed attempt.
        $smsLog->increment('attempt_count');
        $smsLog->update(['provider_response' => $result['response']]);

        Log::warning('SMS attempt failed', [
            'sms_log_id'   => $this->smsLogId,
            'attempt'      => $this->attempts(),
            'error'        => $result['response'],
        ]);

        // Throw so Laravel uses backoff() to schedule the next retry.
        throw new \RuntimeException('SMS gateway error: ' . $result['response']);
    }

    /**
     * Called once all retry attempts are exhausted.
     * Marks the SmsLog record as permanently failed.
     */
    public function failed(\Throwable $exception): void
    {
        $smsLog = SmsLog::find($this->smsLogId);

        if ($smsLog) {
            $smsLog->update(['status' => 'failed']);
        }

        Log::error('SMS permanently failed after all retries', [
            'sms_log_id' => $this->smsLogId,
            'error'      => $exception->getMessage(),
        ]);
    }
}
