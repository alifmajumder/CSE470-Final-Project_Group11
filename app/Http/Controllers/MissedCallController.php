<?php

namespace App\Http\Controllers;

use App\Jobs\SendSmsJob;
use App\Models\MissedCall;
use App\Models\SmsLog;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MissedCallController extends Controller
{
    public function __construct(
        protected SmsController $sms
    ) {}

    // -------------------------------------------------------------------------
    // Webhook: POST /webhook/missed-call
    // -------------------------------------------------------------------------

    /**
     * Called by the telephony provider (SSL Wireless IVR, Twilio, Banglalink USSD)
     * when a missed call arrives on the system DID number.
     *
     * Expected payload (provider-agnostic — we check common field names):
     *   caller_number | callerNumber | Caller | from
     *   called_number | calledNumber | Called | to
     *   timestamp     (optional)
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        // ------------------------------------------------------------------
        // Optional webhook token validation (set WEBHOOK_SECRET in .env).
        // ------------------------------------------------------------------
        $secret = config('services.sms.webhook_secret');
        if ($secret) {
            $provided = $request->header('X-Webhook-Token')
                ?? $request->input('token');

            if (!hash_equals($secret, (string) $provided)) {
                Log::warning('Missed call webhook: invalid token', [
                    'ip' => $request->ip(),
                ]);
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }

        // ------------------------------------------------------------------
        // Extract fields — handle multiple provider naming conventions.
        // ------------------------------------------------------------------
        $callerNumber = $request->input('caller_number')
            ?? $request->input('callerNumber')
            ?? $request->input('Caller')
            ?? $request->input('from');

        $calledNumber = $request->input('called_number')
            ?? $request->input('calledNumber')
            ?? $request->input('Called')
            ?? $request->input('to');

        if (!$callerNumber) {
            Log::warning('Missed call webhook: missing caller_number', $request->all());
            return response()->json(['error' => 'caller_number is required'], 422);
        }

        $callerNumber = $this->sms->normalizePhone((string) $callerNumber);

        Log::info('Missed call received', [
            'caller' => $callerNumber,
            'called' => $calledNumber,
            'ip'     => $request->ip(),
        ]);

        // ------------------------------------------------------------------
        // Look up the caller in the users table.
        // ------------------------------------------------------------------
        $worker = User::where('phone', $callerNumber)->first();

        $missedCall = MissedCall::create([
            'caller_number' => $callerNumber,
            'called_number' => $calledNumber,
            'worker_id'     => $worker?->id,
            'district'      => $worker?->district,
            'jobs_sent'     => 0,
            'status'        => 'processing',
        ]);

        if ($worker) {
            $this->sendJobsToWorker($worker, $missedCall);
        } else {
            $this->sendWelcomeSms($callerNumber, $missedCall);
        }

        return response()->json([
            'status'         => 'ok',
            'missed_call_id' => $missedCall->id,
        ]);
    }

    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    /**
     * Fetch the nearest 3 active jobs for the worker's district and SMS them.
     */
    protected function sendJobsToWorker(User $worker, MissedCall $missedCall): void
    {
        $district = $worker->district;

        // Prefer jobs in the worker's own district; fall back to any active jobs.
        $jobs = $this->fetchNearbyJobs($district, 3);

        if ($jobs->isEmpty()) {
            $this->sms->sendJobAlert($worker->phone, [
                'title'      => 'এখন কোনো কাজ নেই',
                'wage'       => '',
                'location'   => $district ?? '',
                'reply_code' => 'NOJOB',
            ]);

            $missedCall->update(['status' => 'no_jobs', 'jobs_sent' => 0]);
            return;
        }

        $sent = 0;
        foreach ($jobs as $job) {
            $this->sms->sendJobAlert($worker->phone, [
                'title'      => $job->title    ?? 'কাজ পাওয়া গেছে',
                'wage'       => $job->wage     ?? 'আলোচনাযোগ্য',
                'location'   => $job->district ?? $job->location ?? $district ?? '',
                'reply_code' => 'RC' . $job->id,
            ]);
            $sent++;
        }

        $missedCall->update(['status' => 'jobs_sent', 'jobs_sent' => $sent]);

        Log::info('Job alerts sent via SMS', [
            'worker_id'  => $worker->id,
            'phone'      => $worker->phone,
            'jobs_count' => $sent,
        ]);
    }

    /**
     * Fetch active tasks, preferring the given district.
     */
    protected function fetchNearbyJobs(?string $district, int $limit)
    {
        $base = Task::where('status', 'active')->latest();

        if ($district) {
            $local = (clone $base)->where('district', $district)->take($limit)->get();
            if ($local->count() > 0) {
                return $local;
            }
        }

        return $base->take($limit)->get();
    }

    /**
     * Send a registration welcome SMS to an unrecognized caller.
     */
    protected function sendWelcomeSms(string $phone, MissedCall $missedCall): void
    {
        $appUrl  = rtrim(config('app.url'), '/');
        $message = "RuralConnect-এ স্বাগতম!\n"
            . "কাজ পেতে নিবন্ধন করুন:\n"
            . "{$appUrl}/register\n"
            . 'সাহায্য: ' . config('services.sms.helpline', '16XXX');

        $smsLog = SmsLog::create([
            'phone'         => $phone,
            'message'       => $message,
            'status'        => 'pending',
            'gateway_used'  => config('services.sms.driver', 'ssl'),
            'attempt_count' => 0,
        ]);

        SendSmsJob::dispatch($smsLog->id);

        $missedCall->update(['status' => 'welcome_sent', 'jobs_sent' => 0]);

        Log::info('Welcome SMS queued for unregistered caller', ['phone' => $phone]);
    }
}
