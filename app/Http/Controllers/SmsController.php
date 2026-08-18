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

class SmsController extends Controller
{
    // -------------------------------------------------------------------------
    // SMS Dashboard (visual page)
    // -------------------------------------------------------------------------

    public function dashboard(): \Illuminate\View\View
    {
        return view('sms.dashboard', [
            'missedCalls'     => MissedCall::with('worker')->latest()->get(),
            'smsLogs'         => SmsLog::latest()->get(),
            'totalMissedCalls' => MissedCall::count(),
            'totalSms'        => SmsLog::count(),
            'smsSent'         => SmsLog::where('status', 'sent')->count(),
            'smsFailed'       => SmsLog::where('status', 'failed')->count(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Core send helpers (used internally and by MissedCallController)
    // -------------------------------------------------------------------------

    /**
     * Queue a single job-alert SMS to one phone number.
     *
     * @param string $phone       Recipient phone — any BD format accepted
     * @param array  $jobDetails  Keys: title, wage, location, reply_code
     */
    public function sendJobAlert(string $phone, array $jobDetails): SmsLog
    {
        $normalized = $this->normalizePhone($phone);
        $message    = $this->formatJobMessage($jobDetails);

        $smsLog = SmsLog::create([
            'phone'        => $normalized,
            'message'      => $message,
            'status'       => 'pending',
            'gateway_used' => config('services.sms.driver', 'ssl'),
            'attempt_count' => 0,
        ]);

        SendSmsJob::dispatch($smsLog->id);

        Log::info('SMS alert queued', [
            'sms_log_id' => $smsLog->id,
            'phone'      => $normalized,
        ]);

        return $smsLog;
    }

    /**
     * Queue job-alert SMS to multiple workers (bulk).
     *
     * @param  string[] $phones
     * @param  array    $jobDetails
     * @return SmsLog[]
     */
    public function sendBulkAlert(array $phones, array $jobDetails): array
    {
        $logs = [];
        foreach ($phones as $phone) {
            $logs[] = $this->sendJobAlert($phone, $jobDetails);
        }

        Log::info('Bulk SMS queued', [
            'count'     => count($logs),
            'job_title' => $jobDetails['title'] ?? 'N/A',
        ]);

        return $logs;
    }

    // -------------------------------------------------------------------------
    // HTTP endpoint (admin-only): POST /sms/send-alert
    // -------------------------------------------------------------------------

    /**
     * Admin trigger: broadcast job alert for a given task.
     * Optionally scoped to workers in a specific district.
     */
    public function sendAlertViaHttp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'task_id'  => 'required|integer|exists:tasks,id',
            'district' => 'nullable|string|max:100',
        ]);

        $task = Task::findOrFail($validated['task_id']);

        $query = User::whereNotNull('phone');
        if (!empty($validated['district'])) {
            $query->where('district', $validated['district']);
        }

        $phones = $query->pluck('phone')->toArray();

        if (empty($phones)) {
            return response()->json([
                'message' => 'No registered workers with phone numbers found.',
            ], 404);
        }

        $jobDetails = [
            'title'      => $task->title    ?? 'কাজ পাওয়া গেছে',
            'wage'       => $task->wage     ?? 'আলোচনাযোগ্য',
            'location'   => $task->district ?? $task->location ?? 'অজানা',
            'reply_code' => 'RC' . $task->id,
        ];

        $logs = $this->sendBulkAlert($phones, $jobDetails);

        return response()->json([
            'message'      => 'SMS alerts queued successfully.',
            'queued_count' => count($logs),
        ]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Build a Bangla SMS body.
     * Unicode SMS segments = 70 chars each — keep it concise.
     */
    public function formatJobMessage(array $jobDetails): string
    {
        $title    = $jobDetails['title']      ?? 'কাজ';
        $wage     = $jobDetails['wage']       ?? 'আলোচনাযোগ্য';
        $location = $jobDetails['location']   ?? '';
        $code     = $jobDetails['reply_code'] ?? '';

        $msg  = "RuralConnect:\n";
        $msg .= "কাজ: {$title}\n";
        $msg .= "মজুরি: {$wage} টাকা\n";

        if ($location !== '') {
            $msg .= "স্থান: {$location}\n";
        }
        if ($code !== '') {
            $msg .= "কোড: {$code}\n";
        }

        $msg .= 'যোগাযোগ: ' . config('services.sms.helpline', '16XXX');

        return $msg;
    }

    /**
     * Normalize any common BD phone format to E.164 (+880XXXXXXXXX).
     */
    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        // Already full E.164 without '+'
        if (str_starts_with($digits, '880') && strlen($digits) === 13) {
            return '+' . $digits;
        }
        // Local format: 01XXXXXXXXX (11 digits)
        if (str_starts_with($digits, '0') && strlen($digits) === 11) {
            return '+880' . substr($digits, 1);
        }
        // 10-digit without leading zero
        if (strlen($digits) === 10) {
            return '+880' . $digits;
        }

        // Return as-is prefixed with '+'
        return '+' . $digits;
    }
}
