<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work History & Earnings Tracker</title>
    <!-- Include Chart.js for the Earnings Graph -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f7f6; padding: 40px; color: #2c3e50; margin: 0; }
        .container { max-width: 1000px; margin: 0 auto; }
        .nav-button { display: inline-block; background: #27ae60; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; margin-bottom: 20px; transition: 0.2s; }
        .nav-button:hover { background: #219150; }
        
        .header-box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        h2 { color: #27ae60; margin-top: 0; margin-bottom: 5px; font-size: 28px; }
        .subtitle { color: #718096; font-size: 15px; margin-bottom: 0; }
        
        /* Dashboard Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px; }
        .stat-card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-left: 5px solid #27ae60; display: flex; flex-direction: column; }
        .stat-card.blue { border-left-color: #3182ce; }
        .stat-card.amber { border-left-color: #d97706; }
        .stat-title { font-size: 14px; font-weight: bold; color: #718096; text-transform: uppercase; margin-bottom: 5px; }
        .stat-value { font-size: 32px; font-weight: bold; color: #2c3e50; }
        
        /* Chart Section */
        .chart-section { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .chart-section h3 { margin-top: 0; color: #2c3e50; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px; }
        
        /* Table Section */
        .table-section { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .table-section h3 { margin-top: 0; color: #2c3e50; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px; }
        
        .alert-success { background: #d1fae5; border-left: 4px solid #10b981; color: #065f46; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 14px; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
        th { background-color: #f7fafc; color: #4a5568; font-size: 12px; text-transform: uppercase; font-weight: 700; }
        .amount { font-weight: bold; color: #27ae60; }
        .status-pill { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .status-pill.confirmed { background: #c6f6d5; color: #276749; }
        .status-pill.pending { background: #fefcbf; color: #975a16; }
        .btn-sm { border: none; padding: 6px 12px; border-radius: 5px; cursor: pointer; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-block; margin-right: 6px; transition: 0.2s; }
        .btn-outline { background: white; color: #4a5568; border: 1px solid #cbd5e0; }
        .btn-outline:hover { background: #f7fafc; }
        .btn-confirm { background: #27ae60; color: white; }
        .btn-confirm:hover { background: #219150; }
        .empty-state { color: #a0aec0; font-style: italic; padding: 20px 0; text-align: center; }
        
        @media(max-width: 768px) {
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="nav-button">&larr; Back to Home</a>
        
        <div class="header-box">
            <h2>📈 Work History & Earnings Tracker</h2>
            <p class="subtitle">Monitor your financial progress, view your income trends, and manage your payment receipts.</p>
        </div>

        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <!-- FEATURE 14: Analytics Dashboard -->
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-title">Lifetime Earnings</span>
                <span class="stat-value">৳{{ number_format($totalEarned, 2) }}</span>
            </div>
            <div class="stat-card blue">
                <span class="stat-title">Earned This Month</span>
                <span class="stat-value">৳{{ number_format($earnedThisMonth, 2) }}</span>
            </div>
            <div class="stat-card amber">
                <span class="stat-title">Earned This Week</span>
                <span class="stat-value">৳{{ number_format($earnedThisWeek, 2) }}</span>
            </div>
        </div>

        <!-- FEATURE 14: 6-Month Income Trend Chart -->
        <div class="chart-section">
            <h3>📊 6-Month Income Trend</h3>
            @if($totalEarned > 0)
                <canvas id="earningsChart" height="80"></canvas>
            @else
                <p class="empty-state">Complete jobs and receive payments to see your earnings trend here.</p>
            @endif
        </div>

        <!-- Existing Payments Table -->
        <div class="table-section">
            <h3>🗂️ Recent Payment Records</h3>
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
                                <td><strong>{{ $payment->receipt_number }}</strong></td>
                                <td>{{ $payment->task->title ?? 'N/A' }}</td>
                                <td>{{ $payment->employer->name ?? 'N/A' }}</td>
                                <td class="amount">৳{{ number_format($payment->amount, 2) }}</td>
                                <td>{{ $payment->methodLabel() }}</td>
                                <td style="color: #718096;">{{ $payment->paid_at->format('d M Y') }}</td>
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
    </div>

    <!-- Chart.js Initialization Script -->
    @if($totalEarned > 0)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('earningsChart').getContext('2d');
            
            // Pass PHP variables cleanly to JavaScript
            const labels = {!! json_encode($trendLabels) !!};
            const dataPoints = {!! json_encode($trendData) !!};

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Income (৳)',
                        data: dataPoints,
                        backgroundColor: '#27ae60',
                        borderRadius: 4,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '৳' + value;
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ' ৳' + context.parsed.y;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endif
</body>
</html>