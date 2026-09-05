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
        if (!Auth::check()) {
            $tasks = collect();
            return view('home', compact('tasks'));
        }

        $query = Task::query();

        $query->where('employer_id', '!=', Auth::id());

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
        $tasks = Task::where('employer_id', Auth::id())
            ->with(['taskWorkers.worker'])
            ->latest()
            ->get();

        $payments = Payment::whereIn('task_id', $tasks->pluck('id'))
            ->latest('paid_at')
            ->get()
            ->groupBy(fn ($payment) => "{$payment->task_id}-{$payment->worker_id}");

        $takenTaskWorkers = \App\Models\TaskWorker::where('worker_id', Auth::id())
            ->where('status', '!=', 'cancelled')
            ->with(['task.employer'])
            ->latest()
            ->get();

        return view('tasks.my_tasks', compact('tasks', 'payments', 'takenTaskWorkers'));
    }

    public function generateFlyer(Task $task)
    {
        abort_if($task->employer_id !== Auth::id(), 403, 'Only the employer can generate the flyer.');
        
        $task->load('employer');
        
        return view('tasks.flyer', compact('task'));
    }
}