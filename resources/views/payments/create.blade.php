<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Payment</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f7f6; color: #2c3e50; padding: 40px; }
        .container { max-width: 520px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        h2 { color: #27ae60; margin-top: 0; }
        .subtitle { color: #718096; font-size: 14px; margin-top: -10px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; display: flex; flex-direction: column; }
        label { font-weight: 600; margin-bottom: 5px; font-size: 14px; }
        input, textarea, select { padding: 10px; border: 1px solid #cbd5e0; border-radius: 5px; font-size: 14px; font-family: 'Segoe UI', sans-serif; }
        input:focus, textarea:focus, select:focus { outline: none; border-color: #27ae60; }
        .method-options { display: flex; gap: 10px; }
        .method-options label { display: flex; align-items: center; gap: 6px; font-weight: normal; border: 1px solid #cbd5e0; padding: 10px; border-radius: 6px; flex: 1; cursor: pointer; }
        .btn-submit { background: #27ae60; color: white; border: none; padding: 12px; font-weight: bold; border-radius: 5px; cursor: pointer; margin-top: 10px; width: 100%; }
        .btn-submit:hover { background: #219150; }
        .error-box { background-color: #fee2e2; border-left: 4px solid #ef4444; padding: 12px; margin-bottom: 20px; border-radius: 4px; }
        .error-box ul { margin: 0; padding-left: 20px; color: #b91c1c; font-size: 14px; }
        .back-link { display: inline-block; margin-bottom: 15px; color: #4a5568; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="/my-tasks" class="back-link">&larr; Back to My Tasks</a>
        <h2>Record a Payment</h2>
        <p class="subtitle">
            For <strong>{{ $taskWorker->worker->name }}</strong> on
            "<strong>{{ $task->title }}</strong>" (task wage: ৳{{ $task->wage }})
        </p>

        @if ($errors->any())
            <div class="error-box">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('payments.store', [$task, $taskWorker]) }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Amount Paid (BDT)</label>
                <input type="number" name="amount" step="0.01" min="0.01" required value="{{ old('amount') }}">
            </div>

            <div class="form-group">
                <label>Payment Method</label>
                <div class="method-options">
                    @foreach($methods as $value => $label)
                        <label>
                            <input type="radio" name="method" value="{{ $value }}" {{ old('method', 'cash') === $value ? 'checked' : '' }}>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label>Transaction ID <span style="font-weight:normal; color:#a0aec0;">(required for bKash/Nagad)</span></label>
                <input type="text" name="transaction_reference" placeholder="e.g. 8N7K2P1QRS" value="{{ old('transaction_reference') }}">
            </div>

            <div class="form-group">
                <label>Note <span style="font-weight:normal; color:#a0aec0;">(optional)</span></label>
                <textarea name="note" rows="2" placeholder="e.g. Full payment for 2 days of fishing work">{{ old('note') }}</textarea>
            </div>

            <button type="submit" class="btn-submit">Save &amp; Generate Receipt</button>
        </form>
    </div>
</body>
</html>
