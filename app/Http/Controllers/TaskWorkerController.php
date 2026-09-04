<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskWorker;
use App\Models\User;
use App\Models\SmsLog;
use App\Services\BadgeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Validation\ValidationException;
use App\Models\Notification;
use Illuminate\Support\Facades\Storage;

class TaskWorkerController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $this->authorizeOwner($task);

        $validated = $request->validate([
            'worker_identifier' => 'required|string|max:255',
        ]);

        $worker = User::query()
            ->where('phone', $validated['worker_identifier'])
            ->orWhere('email', $validated['worker_identifier'])
            ->first();

        if (! $worker) {
            throw ValidationException::withMessages([
                'worker_identifier' => 'No registered user found with that phone number or email.',
            ]);
        }

        if ($worker->id === $task->employer_id) {
            throw ValidationException::withMessages([
                'worker_identifier' => 'You cannot add yourself as a worker on your own task.',
            ]);
        }

        $activeCount = $task->taskWorkers()->where('status', '!=', 'cancelled')->count();

        if ($activeCount >= $task->required_workers) {
            throw ValidationException::withMessages([
                'worker_identifier' => 'This task already has its required number of workers.',
            ]);
        }

        if ($task->taskWorkers()->where('worker_id', $worker->id)->where('status', '!=', 'cancelled')->exists()) {
            throw ValidationException::withMessages([
                'worker_identifier' => 'This worker is already signed up for this task.',
            ]);
        }

        $task->taskWorkers()->create([
            'worker_id' => $worker->id,
            'status' => 'assigned',
            'joined_at' => Date::now(),
        ]);

        $task->increment('registered_workers');

        return back()->with('success', "{$worker->name} added to the task.");
    }

    public function take(Task $task)
    {
        $worker = Auth::user();

        if ($worker->id === $task->employer_id) {
            return back()->withErrors(['worker_identifier' => 'You cannot take your own task.']);
        }

        $activeCount = $task->taskWorkers()->whereIn('status', ['assigned', 'completed'])->count();

        if ($activeCount >= $task->required_workers) {
            return back()->withErrors(['worker_identifier' => 'This task has already filled its required number of approved workers.']);
        }

        if ($task->taskWorkers()->where('worker_id', $worker->id)->whereIn('status', ['assigned', 'completed', 'pending'])->exists()) {
            return back()->withErrors(['worker_identifier' => 'You have already applied or been assigned to this task.']);
        }

        $task->taskWorkers()->create([
            'worker_id' => $worker->id,
            'status' => 'pending',
            'joined_at' => Date::now(),
        ]);

        Notification::create([
            'user_id' => $task->employer_id,
            'message' => "{$worker->name} has applied for your task: {$task->title}."
        ]);

        return back()->with('success', 'You have successfully applied! Please wait for employer approval.');
    }

    public function approve(Task $task, TaskWorker $taskWorker)
    {
        $this->authorizeOwner($task);
        $this->authorizeBelongsToTask($task, $taskWorker);

        if ($taskWorker->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending applications can be approved.']);
        }

        $activeCount = $task->taskWorkers()->whereIn('status', ['assigned', 'completed'])->count();

        if ($activeCount >= $task->required_workers) {
            return back()->withErrors(['error' => 'You have already approved the maximum number of workers.']);
        }

        $taskWorker->update(['status' => 'assigned']);
        $task->increment('registered_workers');

        Notification::create([
            'user_id' => $taskWorker->worker_id,
            'message' => "✅ Approved! You were accepted for the task: {$task->title}."
        ]);

        $newActiveCount = $activeCount + 1;
        
        if ($newActiveCount >= $task->required_workers) {
            $pendingWorkers = $task->taskWorkers()->where('status', 'pending')->get();
            foreach ($pendingWorkers as $pending) {
                $pending->update(['status' => 'rejected']);
                Notification::create([
                    'user_id' => $pending->worker_id,
                    'message' => "❌ Sorry, the task '{$task->title}' has been filled by others."
                ]);
            }
        }

        return back()->with('success', "Worker {$taskWorker->worker->name} approved!");
    }

    public function reject(Task $task, TaskWorker $taskWorker)
    {
        $this->authorizeOwner($task);
        $this->authorizeBelongsToTask($task, $taskWorker);

        if ($taskWorker->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending applications can be rejected.']);
        }

        $taskWorker->update(['status' => 'rejected']);

        Notification::create([
            'user_id' => $taskWorker->worker_id,
            'message' => "❌ Your application for '{$task->title}' was declined by the employer."
        ]);

        return back()->with('success', "Worker application rejected.");
    }

    // FEATURE 8: Generate Contract OTP & Dispatch Simulated SMS
    public function generateContractOtp(Task $task, TaskWorker $taskWorker)
    {
        $this->authorizeOwner($task);
        $this->authorizeBelongsToTask($task, $taskWorker);

        if ($taskWorker->status !== 'assigned') {
            return back()->withErrors(['contract' => 'Worker must be officially assigned before creating a contract.']);
        }

        $otp = (string) rand(100000, 999999);
        $taskWorker->update(['contract_otp' => $otp]);

        // 1. Log SMS intended for the Worker (Containing the code)
        $workerPhone = $taskWorker->worker->phone ?? '+8801000000001';
        SmsLog::create([
            'phone' => $workerPhone,
            'message' => "Digital Contract OTP: {$otp}. Send this 6-digit code to the employer to lock in your wage (৳{$task->wage}) for '{$task->title}'.",
            'status' => 'sent',
            'gateway_used' => 'log',
            'attempt_count' => 1,
            'sent_at' => Date::now(),
        ]);

        // 2. Log SMS intended for the Employer (Notification only)
        $employerPhone = $task->employer->phone ?? '+8801000000002';
        SmsLog::create([
            'phone' => $employerPhone,
            'message' => "System: An OTP has been securely sent to your worker {$taskWorker->worker->name}. Check your SMS Dashboard when they forward it back to you.",
            'status' => 'sent',
            'gateway_used' => 'log',
            'attempt_count' => 1,
            'sent_at' => Date::now(),
        ]);

        return back()->with('success', 'Contract OTP generated! An SMS has been sent to the worker. Check the SMS Dashboard.');
    }

    // FEATURE 8: Worker Forwards OTP to Employer via SMS
    public function forwardContractOtp(Request $request, Task $task, TaskWorker $taskWorker)
    {
        abort_if($taskWorker->worker_id !== Auth::id(), 403, 'Only the worker can forward their OTP.');
        $this->authorizeBelongsToTask($task, $taskWorker);

        if (!$taskWorker->contract_otp) {
            return back()->withErrors(['otp' => 'There is no OTP available to forward.']);
        }

        $employerPhone = $task->employer->phone ?? '+8801000000002';

        SmsLog::create([
            'phone' => $employerPhone,
            'message' => "From Worker {$taskWorker->worker->name}: The OTP to lock our contract for '{$task->title}' is {$taskWorker->contract_otp}.",
            'status' => 'sent',
            'gateway_used' => 'log',
            'attempt_count' => 1,
            'sent_at' => Date::now(),
        ]);

        return back()->with('success', 'OTP successfully forwarded to the employer via SMS!');
    }

    // FEATURE 8: Confirm Contract
    public function confirmContract(Request $request, Task $task, TaskWorker $taskWorker)
    {
        $this->authorizeOwner($task);
        $this->authorizeBelongsToTask($task, $taskWorker);

        $request->validate(['otp' => 'required|string']);

        if ($taskWorker->contract_otp !== $request->otp) {
            return back()->withErrors(['otp' => 'Invalid OTP. Please check the SMS Dashboard for the code sent by the worker.']);
        }

        $taskWorker->update([
            'contract_confirmed_at' => Date::now(),
            'contract_otp' => null, 
        ]);

        Notification::create([
            'user_id' => $taskWorker->worker_id,
            'message' => "📝 Digital Contract Signed! The agreement and wage for '{$task->title}' is officially locked."
        ]);

        return back()->with('success', 'Digital Contract successfully signed! Neither party can change the terms now.');
    }

    public function uploadCompletionPhoto(Request $request, Task $task, TaskWorker $taskWorker)
    {
        $this->authorizeBelongsToTask($task, $taskWorker);

        abort_if($taskWorker->worker_id !== Auth::id(), 403, 'Only the assigned worker can upload proof of completion for this job.');

        if ($taskWorker->status !== 'assigned') {
            return back()->withErrors(['completion_photo' => 'You can only upload a completion photo while the job is assigned to you and not yet marked completed.']);
        }

        $validated = $request->validate([
            'completion_photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        if ($taskWorker->completion_photo_path) {
            Storage::disk('public')->delete($taskWorker->completion_photo_path);
        }

        $path = $request->file('completion_photo')->store("completion_photos/{$task->id}", 'public');

        $taskWorker->update([
            'completion_photo_path' => $path,
            'completion_photo_uploaded_at' => Date::now(),
        ]);

        Notification::create([
            'user_id' => $task->employer_id,
            'message' => "📸 {$taskWorker->worker->name} uploaded a completion photo for '{$task->title}'. Review it before marking the job complete.",
        ]);

        return back()->with('success', 'Completion photo uploaded. The employer will review it before marking the job complete.');
    }

    public function complete(Task $task, TaskWorker $taskWorker, BadgeService $badgeService)
    {
        $this->authorizeOwner($task);
        $this->authorizeBelongsToTask($task, $taskWorker);

        if ($taskWorker->status === 'completed') {
            return back()->with('success', 'Already marked completed.');
        }

        $taskWorker->update([
            'status' => 'completed',
            'completed_at' => Date::now(),
        ]);

        $badge = $badgeService->evaluateAndAward($taskWorker->worker, $task->category);

        $message = 'Marked as completed.';
        if ($badge) {
            $message .= " {$taskWorker->worker->name} just earned the \"{$badge->badge_label}\" badge!";
        }

        return back()->with('success', $message);
    }

    public function cancel(Task $task, TaskWorker $taskWorker)
    {
        $this->authorizeOwner($task);
        $this->authorizeBelongsToTask($task, $taskWorker);

        if ($taskWorker->status === 'completed') {
            return back()->withErrors(['worker' => 'Cannot remove a worker whose job is already marked completed.']);
        }

        $hasPayments = $task->payments()->where('worker_id', $taskWorker->worker_id)->exists();
        if ($hasPayments) {
            return back()->withErrors(['worker' => 'Cannot remove a worker who already has payment records.']);
        }

        $taskWorker->update(['status' => 'cancelled']);
        $task->decrement('registered_workers');

        return back()->with('success', 'Worker removed from task.');
    }

    public function rateWorker(Request $request, Task $task, TaskWorker $taskWorker)
    {
        $this->authorizeOwner($task);
        $this->authorizeBelongsToTask($task, $taskWorker);

        if ($taskWorker->status !== 'completed') {
            return back()->withErrors(['rating' => 'You can only rate a worker after their job is marked completed.']);
        }

        if ($taskWorker->employer_rating !== null) {
            return back()->withErrors(['rating' => 'You have already rated this worker for this task.']);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500',
        ]);

        $taskWorker->update([
            'employer_rating' => $validated['rating'],
            'employer_review' => $validated['review'] ?? null, 
        ]);

        return back()->with('success', "Trust score submitted! You rated {$taskWorker->worker->name} {$validated['rating']} stars.");
    }

    private function authorizeOwner(Task $task): void
    {
        abort_if($task->employer_id !== Auth::id(), 403, 'Only the employer who posted this task can manage its workers.');
    }

    private function authorizeBelongsToTask(Task $task, TaskWorker $taskWorker): void
    {
        abort_if($taskWorker->task_id !== $task->id, 404);
    }
}