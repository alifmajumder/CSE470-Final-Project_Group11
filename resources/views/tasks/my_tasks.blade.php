<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tasks Dashboard</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f7f6; padding: 40px; color: #2c3e50; }
        .page { max-width: 1000px; margin: 0 auto; }
        .nav-button { display: inline-block; background: #27ae60; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; margin-bottom: 20px; }
        .nav-button:hover { background: #219150; }
        h2 { color: #27ae60; margin-top: 0; margin-bottom: 20px; }
        h3 { color: #2c3e50; margin-top: 40px; margin-bottom: 15px; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; }
        .alert { padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; }
        .alert-success { background: #d1fae5; border-left: 4px solid #10b981; color: #065f46; }
        .alert-error { background: #fee2e2; border-left: 4px solid #ef4444; color: #b91c1c; }
        .alert ul { margin: 0; padding-left: 20px; }

        .task-card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .task-head { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 10px; margin-bottom: 10px; }
        .task-title { font-size: 18px; font-weight: bold; margin: 0; }
        .category-badge { display: inline-block; background: #ebf8ff; color: #2b6cb0; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; margin-left: 8px; vertical-align: middle; }
        .task-meta { color: #718096; font-size: 14px; margin-bottom: 15px; }
        .progress-text { font-weight: bold; color: #3182ce; }
        .status-badge { background: #e2e8f0; padding: 4px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; text-transform: uppercase; }

        .add-worker-form { display: flex; gap: 8px; margin: 15px 0; flex-wrap: wrap; }
        .add-worker-form input { flex: 1; min-width: 220px; padding: 9px 12px; border: 1px solid #cbd5e0; border-radius: 5px; font-size: 14px; }
        .btn { border: none; padding: 9px 16px; border-radius: 5px; cursor: pointer; font-size: 13px; font-weight: 600; text-decoration: none; display: inline-block; }
        .btn-primary { background: #2c3e50; color: white; }
        .btn-primary:hover { background: #1a252f; }
        .btn-success { background: #27ae60; color: white; }
        .btn-success:hover { background: #219150; }
        .btn-outline { background: white; color: #4a5568; border: 1px solid #cbd5e0; }
        .btn-outline:hover { background: #f7fafc; }
        .btn-danger { background: white; color: #e53e3e; border: 1px solid #feb2b2; }
        .btn-danger:hover { background: #fff5f5; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }

        table.workers-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.workers-table th, table.workers-table td { text-align: left; padding: 10px; border-bottom: 1px solid #edf2f7; font-size: 14px; }
        table.workers-table th { color: #a0aec0; font-size: 11px; text-transform: uppercase; }
        .worker-status { padding: 3px 9px; border-radius: 10px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        
        /* NEW STATUS COLORS */
        .worker-status.assigned { background: #fefcbf; color: #975a16; }
        .worker-status.completed { background: #c6f6d5; color: #276749; }
        .worker-status.pending { background: #e2e8f0; color: #4a5568; }
        .worker-status.rejected { background: #fed7d7; color: #c53030; }

        .actions-cell { display: flex; gap: 6px; flex-wrap: wrap; }
        .no-workers { color: #a0aec0; font-size: 14px; font-style: italic; padding: 10px 0; }
        .payment-line { font-size: 13px; margin-bottom: 3px; }
        .payment-line a { color: #3182ce; text-decoration: none; }
        .payment-line a:hover { text-decoration: underline; }
        .confirmed-tag { color: #276749; font-weight: 600; }
        .unconfirmed-tag { color: #a0aec0; }
    </style>
</head>
<body>
    <div class="page">
        <a href="/" class="nav-button">&larr; Back to Home</a>
        <h2>My Task Dashboard</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- SEGMENT 1: JOBS I POSTED --}}
        <h3>📋 Jobs I Posted</h3>
        @forelse($tasks as $task)
            <div class="task-card">
                <div class="task-head">
                    <div>
                        <span class="task-title">{{ $task->title }}</span>
                        <span class="category-badge">{{ $task->categoryLabel() }}</span>
                    </div>
                    <span class="status-badge">{{ $task->status }}</span>
                </div>
                <div class="task-meta">
                    {{ $task->location }}, {{ $task->district }} &middot; ৳{{ $task->wage }} &middot;
                    <span class="progress-text">{{ $task->registered_workers }} / {{ $task->required_workers }}</span> workers signed up
                </div>

                {{-- Add a worker by phone or email --}}
                <form action="{{ route('tasks.workers.store', $task) }}" method="POST" class="add-worker-form">
                    @csrf
                    <input type="text" name="worker_identifier" placeholder="Worker's phone or email" required>
                    <button type="submit" class="btn btn-primary btn-sm">Add Worker</button>
                </form>

                @if($task->taskWorkers->isEmpty())
                    <p class="no-workers">No workers signed up yet.</p>
                @else
                    <table class="workers-table">
                        <thead>
                            <tr>
                                <th>Worker</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Payments</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($task->taskWorkers as $tw)
                                @php $workerPayments = $payments->get("{$task->id}-{$tw->worker_id}", collect()); @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $tw->worker->name }}</strong><br>
                                        
                                        <!-- NEW: Displaying the Worker's Trust Score -->
                                        @php $workerScore = $tw->worker->averageTrustScore(); @endphp
                                        @if($workerScore > 0)
                                            <span style="font-size: 12px; color: #d97706; font-weight: bold; display: inline-block; margin: 2px 0;">
                                                ⭐ {{ number_format($workerScore, 1) }}/5
                                            </span><br>
                                        @else
                                            <span style="font-size: 12px; color: #a0aec0; font-style: italic; display: inline-block; margin: 2px 0;">
                                                No ratings yet
                                            </span><br>
                                        @endif

                                        <a href="{{ route('badges.profile', $tw->worker) }}" style="font-size:12px; color:#3182ce; text-decoration:none;">View badges &rarr;</a>
                                    </td>
                                    <td>{{ $tw->worker->phone ?? 'N/A' }}</td>
                                    <td><span class="worker-status {{ $tw->status }}">{{ $tw->status }}</span></td>
                                    <td>
                                        @forelse($workerPayments as $p)
                                            <div class="payment-line">
                                                <a href="{{ route('payments.receipt', $p) }}">৳{{ number_format($p->amount, 2) }} ({{ $p->methodLabel() }})</a>
                                                &mdash;
                                                @if($p->isConfirmedByWorker())
                                                    <span class="confirmed-tag">confirmed</span>
                                                @else
                                                    <span class="unconfirmed-tag">unconfirmed</span>
                                                @endif
                                            </div>
                                        @empty
                                            <span class="no-workers" style="padding:0;">No payments yet</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        <div class="actions-cell">
                                            @if($tw->status === 'pending')
                                                <form action="{{ route('tasks.workers.approve', [$task, $tw]) }}" method="POST" style="margin: 0;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                                </form>
                                                <form action="{{ route('tasks.workers.reject', [$task, $tw]) }}" method="POST" style="margin: 0;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                                </form>
                                            @elseif($tw->status === 'assigned')
                                                <form action="{{ route('tasks.workers.complete', [$task, $tw]) }}" method="POST" style="margin: 0;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">Mark Completed</button>
                                                </form>
                                                
                                                <a href="{{ route('payments.create', [$task, $tw]) }}" class="btn btn-outline btn-sm">Record Payment</a>
                                                
                                                @if($workerPayments->isEmpty())
                                                    <form action="{{ route('tasks.workers.cancel', [$task, $tw]) }}" method="POST" onsubmit="return confirm('Remove this worker from the task?');" style="margin: 0;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                                    </form>
                                                @endif
                                            @endif

                                            <!-- FEATURE 11: Trust Score Rating UI -->
                                            @if($tw->status === 'completed' && !$tw->employer_rating)
                                                <form action="{{ route('tasks.workers.rate', [$task, $tw]) }}" method="POST" style="display:flex; gap:5px; align-items:center; margin-top: 5px;">
                                                    @csrf
                                                    <select name="rating" required class="btn-outline btn-sm" style="padding: 4px; border-radius: 4px;">
                                                        <option value="">Rate Worker...</option>
                                                        <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                                                        <option value="4">⭐⭐⭐⭐ (4)</option>
                                                        <option value="3">⭐⭐⭐ (3)</option>
                                                        <option value="2">⭐⭐ (2)</option>
                                                        <option value="1">⭐ (1)</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-primary btn-sm">Submit Score</button>
                                                </form>
                                            @elseif($tw->employer_rating)
                                                <div style="margin-top: 5px; font-size:13px; color:#d97706; font-weight:bold;">
                                                    Rated: {{ str_repeat('⭐', $tw->employer_rating) }} ({{ $tw->employer_rating }}/5)
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @empty
            <div class="task-card">
                <p class="no-workers">You haven't posted any tasks yet.</p>
            </div>
        @endforelse

        {{-- SEGMENT 2: JOBS I'VE TAKEN --}}
        <h3>🛠️ Jobs I've Taken</h3>
        @forelse($takenTaskWorkers ?? [] as $tw)
            <div class="task-card" style="border-left: 5px solid #27ae60;">
                <div class="task-head">
                    <div>
                        <span class="task-title">{{ $tw->task->title }}</span>
                        <span class="category-badge">{{ $tw->task->categoryLabel() }}</span>
                    </div>
                    <span class="worker-status {{ $tw->status }}">{{ $tw->status }}</span>
                </div>
                <div class="task-meta">
                    📍 {{ $tw->task->location }}, {{ $tw->task->district }} &middot; 
                    💰 Wage: ৳{{ $tw->task->wage }} &middot; 
                    👤 Employer: <strong>{{ $tw->task->employer->name ?? 'N/A' }}</strong> (📞 {{ $tw->task->employer->phone ?? 'N/A' }})
                </div>
                <p style="margin: 0; color: #4a5568; font-size: 14px;">{{ $tw->task->description }}</p>
                
                <!-- FEATURE 11: Worker sees the rating they received! -->
                @if($tw->employer_rating)
                    <div style="margin-top: 15px; padding-top: 12px; border-top: 1px dashed #cbd5e0; font-size: 14px;">
                        <strong style="color: #d97706;">Employer Rating:</strong> 
                        {{ str_repeat('⭐', $tw->employer_rating) }} 
                        <span style="color: #718096; font-weight: bold;">({{ $tw->employer_rating }}/5)</span>
                        
                        @if($tw->employer_review)
                            <div style="margin-top: 5px; color: #4a5568; font-style: italic;">
                                "{{ $tw->employer_review }}"
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <div class="task-card">
                <p class="no-workers">You haven't taken any jobs yet.</p>
            </div>
        @endforelse
    </div>
</body>
</html>