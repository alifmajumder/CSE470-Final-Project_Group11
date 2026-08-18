<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Payments</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f7f6; padding: 40px; color: #2c3e50; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); max-width: 1000px; margin: 0 auto; }
        h2 { color: #27ae60; margin-top: 0; margin-bottom: 5px; }
        .subtitle { color: #718096; font-size: 14px; margin-bottom: 20px; }
        .nav-button { display: inline-block; background: #27ae60; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; margin-bottom: 20px; }
        .nav-button:hover { background: #219150; }
        .alert-success { background: #d1fae5; border-left: 4px solid #10b981; color: #065f46; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 14px; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
        th { background-color: #f7fafc; color: #4a5568; font-size: 12px; text-transform: uppercase; }
        .amount { font-weight: bold; }
        .status-pill { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .status-pill.confirmed { background: #c6f6d5; color: #276749; }
        .status-pill.pending { background: #fefcbf; color: #975a16; }
        .btn-sm { border: none; padding: 6px 12px; border-radius: 5px; cursor: pointer; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-block; margin-right: 6px; }
        .btn-outline { background: white; color: #4a5568; border: 1px solid #cbd5e0; }
        .btn-outline:hover { background: #f7fafc; }
        .btn-confirm { background: #27ae60; color: white; }
        .btn-confirm:hover { background: #219150; }
        .empty-state { color: #a0aec0; font-style: italic; padding: 20px 0; }
        .total-earned { font-size: 14px; color: #4a5568; margin-bottom: 20px; }
        .total-earned strong { color: #27ae60; font-size: 18px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="nav-button">&larr; Back to Home</a>
        <h2>My Payments</h2>
        <p class="subtitle">Every payment recorded against your name, cash or mobile banking &mdash; your permanent record.</p>

        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @if($payments->isNotEmpty())
            <p class="total-earned">Total received: <strong>৳{{ number_format($payments->sum('amount'), 2) }}</strong> across {{ $payments->count() }} payment(s)</p>
        @endif

        @if($payments->isEmpty())
            <p class="empty-state">No payments recorded yet.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Receipt #</th>
                        <th>Task</th>
                        <th>Employer</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                        <tr>
                            <td>{{ $payment->receipt_number }}</td>
                            <td>{{ $payment->task->title ?? 'N/A' }}</td>
                            <td>{{ $payment->employer->name ?? 'N/A' }}</td>
                            <td class="amount">৳{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->methodLabel() }}</td>
                            <td>{{ $payment->paid_at->format('d M Y') }}</td>
                            <td>
                                @if($payment->isConfirmedByWorker())
                                    <span class="status-pill confirmed">Confirmed</span>
                                @else
                                    <span class="status-pill pending">Pending</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('payments.receipt', $payment) }}" class="btn-sm btn-outline">View Receipt</a>
                                @if(! $payment->isConfirmedByWorker())
                                    <form action="{{ route('payments.confirm', $payment) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn-sm btn-confirm">Confirm</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>
