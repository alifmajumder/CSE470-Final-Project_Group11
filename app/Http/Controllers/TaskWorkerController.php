<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskWorker;
use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Validation\ValidationException;
use App\Models\Notification;

class TaskWorkerController extends Controller
{
    /**
     * Employer signs a worker up for their task, by phone or email.
     * This is what lets us later record a payment for -- and credit a
     * completed job to -- a specific worker.
     */
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

    /**
     * Worker applies for an open task, putting them in the 'pending' pool.
     */
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

    /**
     * Employer reviews a pending worker and approves them for the job.
     */
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

    /**
     * Employer rejects a pending worker application.
     */
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

    /**
     * Employer marks a worker's job as completed. This is what feeds the
     * Skill Badge System -- completed jobs are what count toward a badge.
     */
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

    /**
     * Employer removes a worker who was signed up by mistake. Only allowed
     * before the job is marked completed or any payment has been recorded,
     * so it can't be used to quietly erase a real work/payment history.
     */
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

    /**
     * Employer submits a 1 to 5 star rating and review for a completed worker.
     * This powers Feature 11 (Worker Rating & Trust Score).
     */
    /**
     * Employer submits a 1 to 5 star rating and review for a completed worker.
     * This powers Feature 11 (Worker Rating & Trust Score).
     */
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
            // THE FIX: Add "?? null" so PHP doesn't crash if the text review is missing!
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
