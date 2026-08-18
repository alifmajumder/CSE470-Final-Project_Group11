<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function home(Request $request)
    {
        // If the user is not logged in, do not show any tasks on the public home feed
        if (!Auth::check()) {
            $tasks = collect(); // Empty collection
            return view('home', compact('tasks'));
        }

        $query = Task::query();

        // Hide tasks that the user posted themselves
        $query->where('employer_id', '!=', Auth::id());

        // Hide tasks that the user has already taken as a worker
        $userTakenTaskIds = \App\Models\TaskWorker::where('worker_id', Auth::id())
            ->where('status', '!=', 'cancelled')
            ->pluck('task_id');
        
        $query->whereNotIn('id', $userTakenTaskIds);

        if ($request->filled('keyword')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->keyword . '%')
                  ->orWhere('description', 'like', '%' . $request->keyword . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        $tasks = $query->latest()->get();

        return view('home', compact('tasks'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|in:'.implode(',', array_keys(config('skills.categories'))),
            'description' => 'required|string',
            'wage' => 'required|string',
            'district' => 'required|string',
            'location' => 'required|string',
            'required_workers' => 'required|integer|min:1'
        ]);

        $validated['employer_id'] = Auth::id();

        Task::create($validated);

        return redirect('/')->with('success', 'Group task posted successfully!');
    }

    public function myTasks()
    {
        // 1. Jobs posted by the user (Employer view)
        $tasks = Task::where('employer_id', Auth::id())
            ->with(['taskWorkers.worker'])
            ->latest()
            ->get();

        $payments = Payment::whereIn('task_id', $tasks->pluck('id'))
            ->latest('paid_at')
            ->get()
            ->groupBy(fn ($payment) => "{$payment->task_id}-{$payment->worker_id}");

        // 2. Jobs taken by the user as a worker (Worker view)
        $takenTaskWorkers = \App\Models\TaskWorker::where('worker_id', Auth::id())
            ->where('status', '!=', 'cancelled')
            ->with(['task.employer'])
            ->latest()
            ->get();

        return view('tasks.my_tasks', compact('tasks', 'payments', 'takenTaskWorkers'));
    }
}