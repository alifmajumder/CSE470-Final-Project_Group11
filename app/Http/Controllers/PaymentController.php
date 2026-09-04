<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Task;
use App\Models\TaskWorker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

class PaymentController extends Controller
{
    /**
     * Show the "record a payment" form for a specific worker on a task.
     */
    public function create(Task $task, TaskWorker $taskWorker)
    {
        $this->authorizeOwner($task);
        $this->authorizeBelongsToTask($task, $taskWorker);
        $this->authorizeCompletionPhotoExists($taskWorker);

        $taskWorker->load('worker');

        return view('payments.create', [
            'task' => $task,
            'taskWorker' => $taskWorker,
            'methods' => config('skills.payment_methods'),
        ]);
    }

    /**
     * Save the payment and generate its receipt.
     */
    public function store(Request $request, Task $task, TaskWorker $taskWorker)
    {
        $this->authorizeOwner($task);
        $this->authorizeBelongsToTask($task, $taskWorker);
        $this->authorizeCompletionPhotoExists($taskWorker);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:9999999.99',
            'method' => 'required|in:cash,bkash,nagad',
            'transaction_reference' => 'required_if:method,bkash,nagad|nullable|string|max:100',
            'note' => 'nullable|string|max:500',
        ]);

        $payment = Payment::create([
            'task_id' => $task->id,
            'employer_id' => Auth::id(),
            'worker_id' => $taskWorker->worker_id,
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'transaction_reference' => $validated['method'] === 'cash' ? null : ($validated['transaction_reference'] ?? null),
            'note' => $validated['note'] ?? null,
            'paid_at' => Date::now(),
        ]);

        return redirect()
            ->route('payments.receipt', $payment)
            ->with('success', 'Payment recorded. Receipt '.$payment->receipt_number.' created.');
    }

    /**
     * Printable receipt. Viewable by the employer who logged it, the worker
     * who was paid, or an admin -- nobody else.
     */
    public function receipt(Payment $payment)
    {
        $userId = Auth::id();
        $isAdmin = Auth::user()->role === 'admin';

        abort_unless(
            $isAdmin || $payment->employer_id === $userId || $payment->worker_id === $userId,
            403
        );

        $payment->load(['task', 'employer', 'worker']);

        return view('payments.receipt', ['payment' => $payment]);
    }

    /**
     * Worker confirms they actually received a payment -- a second,
     * independent signature on the same record.
     */
    public function confirm(Payment $payment)
    {
        abort_unless($payment->worker_id === Auth::id(), 403);

        if (! $payment->worker_confirmed_at) {
            $payment->update(['worker_confirmed_at' => Date::now()]);
        }

        return back()->with('success', 'Payment confirmed as received.');
    }

    /**
     * FEATURE 14: Work History & Earnings Tracker
     * Transforms the basic payments list into a full financial analytics dashboard.
     */
    public function myPayments()
    {
        $payments = Payment::where('worker_id', Auth::id())
            ->with(['task', 'employer'])
            ->latest('paid_at')
            ->get();

        // 1. Calculate Summary Statistics
        $totalEarned = $payments->sum('amount');
        
        $earnedThisMonth = $payments->filter(function($payment) {
            return $payment->paid_at >= Date::now()->startOfMonth();
        })->sum('amount');

        $earnedThisWeek = $payments->filter(function($payment) {
            return $payment->paid_at >= Date::now()->startOfWeek();
        })->sum('amount');

        // 2. Calculate 6-Month Earnings Trend for the Chart
        $trendLabels = [];
        $trendData = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Date::now()->subMonths($i)->startOfMonth();
            $monthEnd = Date::now()->subMonths($i)->endOfMonth();
            
            $monthSum = $payments->filter(function($payment) use ($monthStart, $monthEnd) {
                return $payment->paid_at >= $monthStart && $payment->paid_at <= $monthEnd;
            })->sum('amount');

            $trendLabels[] = $monthStart->format('M Y');
            $trendData[] = $monthSum;
        }

        return view('payments.my_payments', compact(
            'payments', 
            'totalEarned', 
            'earnedThisMonth', 
            'earnedThisWeek', 
            'trendLabels', 
            'trendData'
        ));
    }

    private function authorizeOwner(Task $task): void
    {
        abort_if($task->employer_id !== Auth::id(), 403, 'Only the employer who posted this task can record payments for it.');
    }

    private function authorizeBelongsToTask(Task $task, TaskWorker $taskWorker): void
    {
        abort_if($taskWorker->task_id !== $task->id, 404);
    }

    private function authorizeCompletionPhotoExists(TaskWorker $taskWorker): void
    {
        if (! $taskWorker->hasCompletionPhoto()) {
            abort(422, 'The worker must upload a completion photo before payment can be recorded for this job.');
        }
    }
}