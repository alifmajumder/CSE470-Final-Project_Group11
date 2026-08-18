<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt {{ $payment->receipt_number }}</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f7f6; color: #2c3e50; padding: 40px; }
        .page { max-width: 560px; margin: 0 auto; }
        .toolbar { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .back-link { color: #4a5568; text-decoration: none; font-size: 14px; }
        .btn-print { background: #2c3e50; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer; font-size: 13px; font-weight: 600; }
        .alert-success { background: #d1fae5; border-left: 4px solid #10b981; color: #065f46; padding: 12px 16px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; }

        .receipt { background: white; padding: 35px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-top: 6px solid #27ae60; }
        .receipt-header { text-align: center; margin-bottom: 20px; }
        .receipt-header .brand { color: #27ae60; font-weight: bold; font-size: 20px; }
        .receipt-header .receipt-no { color: #a0aec0; font-size: 13px; margin-top: 4px; letter-spacing: 0.5px; }
        .status-pill { display: inline-block; margin-top: 10px; padding: 4px 14px; border-radius: 14px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .status-pill.confirmed { background: #c6f6d5; color: #276749; }
        .status-pill.pending { background: #fefcbf; color: #975a16; }

        .amount-block { text-align: center; margin: 25px 0; }
        .amount-block .amount { font-size: 36px; font-weight: bold; color: #2c3e50; }
        .amount-block .method { color: #718096; font-size: 14px; margin-top: 4px; }

        .divider { border: none; border-top: 1px dashed #cbd5e0; margin: 20px 0; }

        .detail-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; }
        .detail-row .label { color: #718096; }
        .detail-row .value { font-weight: 600; text-align: right; }

        .note-block { margin-top: 15px; font-size: 13px; color: #4a5568; background: #f7fafc; padding: 10px 12px; border-radius: 6px; }

        .confirm-box { margin-top: 25px; text-align: center; }
        .btn-confirm { background: #27ae60; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: 600; }
        .btn-confirm:hover { background: #219150; }

        @media print {
            body { background: white; padding: 0; }
            .toolbar, .confirm-box { display: none; }
            .receipt { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="toolbar">
            <a href="javascript:history.back()" class="back-link">&larr; Back</a>
            <button class="btn-print" onclick="window.print()">Print / Save as PDF</button>
        </div>

        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <div class="receipt">
            <div class="receipt-header">
                <div class="brand">RuralConnect</div>
                <div class="receipt-no">Receipt No. {{ $payment->receipt_number }}</div>
                @if($payment->isConfirmedByWorker())
                    <span class="status-pill confirmed">Confirmed by worker</span>
                @else
                    <span class="status-pill pending">Awaiting worker confirmation</span>
                @endif
            </div>

            <div class="amount-block">
                <div class="amount">৳{{ number_format($payment->amount, 2) }}</div>
                <div class="method">Paid via {{ $payment->methodLabel() }}</div>
            </div>

            <hr class="divider">

            <div class="detail-row">
                <span class="label">Task</span>
                <span class="value">{{ $payment->task->title ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Paid to (worker)</span>
                <span class="value">{{ $payment->worker->name ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Paid by (employer)</span>
                <span class="value">{{ $payment->employer->name ?? 'N/A' }}</span>
            </div>
            @if($payment->transaction_reference)
                <div class="detail-row">
                    <span class="label">Transaction ID</span>
                    <span class="value">{{ $payment->transaction_reference }}</span>
                </div>
            @endif
            <div class="detail-row">
                <span class="label">Date &amp; Time</span>
                <span class="value">{{ $payment->paid_at->format('d M Y, h:i A') }}</span>
            </div>
            @if($payment->isConfirmedByWorker())
                <div class="detail-row">
                    <span class="label">Worker confirmed</span>
                    <span class="value">{{ $payment->worker_confirmed_at->format('d M Y, h:i A') }}</span>
                </div>
            @endif

            @if($payment->note)
                <div class="note-block">{{ $payment->note }}</div>
            @endif
        </div>

        @auth
            @if(Auth::id() === $payment->worker_id && ! $payment->isConfirmedByWorker())
                <div class="confirm-box">
                    <form action="{{ route('payments.confirm', $payment) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-confirm">I confirm I received this payment</button>
                    </form>
                </div>
            @endif
        @endauth
    </div>
</body>
</html>
