<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.sms.page_title') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Hind Siliguri', 'Segoe UI', sans-serif; background: #f4f7f6; color: #2c3e50; }

        /* ── Navbar ── */
        .navbar { display: flex; justify-content: space-between; align-items: center;
                  padding: 18px 40px; background: white; box-shadow: 0 2px 4px rgba(0,0,0,.07); }
        .navbar-brand { font-size: 22px; font-weight: 700; color: #27ae60; text-decoration: none; }
        .navbar-brand span { color: #2c3e50; font-weight: 400; font-size: 16px; margin-left: 8px; }
        .nav-links a { text-decoration: none; color: #4a5568; font-weight: 600;
                       margin-left: 24px; transition: color .2s; }
        .nav-links a:hover { color: #27ae60; }

        /* ── Layout ── */
        .page { max-width: 1100px; margin: 36px auto; padding: 0 20px; }
        h2 { font-size: 22px; color: #27ae60; margin-bottom: 18px; }

        /* ── Stat cards ── */
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 36px; }
        .card { background: white; border-radius: 10px; padding: 22px 24px;
                box-shadow: 0 2px 8px rgba(0,0,0,.06); text-align: center; }
        .card .num  { font-size: 38px; font-weight: 700; color: #27ae60; }
        .card .lbl  { font-size: 13px; color: #718096; margin-top: 4px; }
        .card.red   .num { color: #e53e3e; }
        .card.amber .num { color: #d97706; }
        .card.blue  .num { color: #3182ce; }

        /* ── Simulate panel ── */
        .panel { background: white; border-radius: 10px; padding: 28px 30px;
                 box-shadow: 0 2px 8px rgba(0,0,0,.06); margin-bottom: 36px; }
        .panel h2 { margin-bottom: 6px; }
        .panel p  { font-size: 14px; color: #718096; margin-bottom: 20px; }
        .form-row { display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; }
        .form-group { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 200px; }
        .form-group label { font-size: 13px; font-weight: 600; color: #4a5568; }
        .form-group input { border: 1px solid #cbd5e0; border-radius: 7px;
                            padding: 10px 13px; font-size: 14px; outline: none; transition: border .2s; }
        .form-group input:focus { border-color: #27ae60; }
        .btn { background: #27ae60; color: white; border: none; border-radius: 7px;
               padding: 11px 26px; font-size: 14px; font-weight: 600;
               cursor: pointer; transition: background .2s; white-space: nowrap; }
        .btn:hover { background: #219150; }
        .result-box { margin-top: 18px; padding: 14px 16px; border-radius: 7px;
                      font-size: 14px; display: none; }
        .result-box.ok  { background: #f0fff4; border: 1px solid #9ae6b4; color: #276749; }
        .result-box.err { background: #fff5f5; border: 1px solid #feb2b2; color: #9b2c2c; }

        /* ── Tables ── */
        .section { background: white; border-radius: 10px;
                   box-shadow: 0 2px 8px rgba(0,0,0,.06); margin-bottom: 36px; overflow: hidden; }
        .section-header { padding: 20px 24px 0; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #f7fafc; text-align: left; padding: 11px 16px;
                   font-size: 12px; font-weight: 700; color: #718096;
                   text-transform: uppercase; letter-spacing: .5px;
                   border-bottom: 1px solid #e2e8f0; }
        tbody td { padding: 12px 16px; font-size: 14px; border-bottom: 1px solid #f0f0f0; vertical-align: top; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: #f9fffe; }

        /* ── Status badges ── */
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px;
                 font-size: 12px; font-weight: 600; }
        .badge.sent       { background: #f0fff4; color: #276749; }
        .badge.pending    { background: #fffbeb; color: #92400e; }
        .badge.failed     { background: #fff5f5; color: #9b2c2c; }
        .badge.jobs_sent  { background: #ebf8ff; color: #2c5282; }
        .badge.welcome_sent { background: #faf5ff; color: #553c9a; }
        .badge.processing { background: #fffbeb; color: #92400e; }
        .badge.no_jobs    { background: #f7fafc; color: #718096; }

        .sms-msg { font-family: monospace; font-size: 12px; white-space: pre-wrap;
                   background: #f7fafc; padding: 8px 10px; border-radius: 6px;
                   max-width: 340px; line-height: 1.6; }
        .empty { text-align: center; padding: 40px; color: #a0aec0; font-size: 15px; }

        @media(max-width:700px){
            .stats { grid-template-columns: repeat(2,1fr); }
            .form-row { flex-direction: column; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="/" class="navbar-brand">RuralConnect <span>/ {{ __('messages.sms.heading') }}</span></a>
    <div class="nav-links">
        <a href="/">{{ __('messages.nav.home') }}</a>
        <a href="/sms/dashboard">{{ __('messages.sms.refresh') }}</a>
        @php $switchTo = app()->getLocale() === 'en' ? 'bn' : 'en'; @endphp
        <a href="{{ route('lang.switch', $switchTo) }}"
           style="background:#27ae60;color:white;border-radius:20px;padding:6px 16px;font-size:14px;font-weight:600;text-decoration:none;transition:background .2s;"
           onmouseover="this.style.background='#219150'" onmouseout="this.style.background='#27ae60'">
            {{ __('messages.nav.switch_lang') }}
        </a>
    </div>
</nav>

<div class="page">

    {{-- ── Stat cards ── --}}
    <div class="stats">
        <div class="card">
            <div class="num">{{ $totalMissedCalls }}</div>
            <div class="lbl">{{ __('messages.sms.stat_missed') }}</div>
        </div>
        <div class="card blue">
            <div class="num">{{ $totalSms }}</div>
            <div class="lbl">{{ __('messages.sms.stat_queued') }}</div>
        </div>
        <div class="card">
            <div class="num">{{ $smsSent }}</div>
            <div class="lbl">{{ __('messages.sms.stat_delivered') }}</div>
        </div>
        <div class="card red">
            <div class="num">{{ $smsFailed }}</div>
            <div class="lbl">{{ __('messages.sms.stat_failed') }}</div>
        </div>
    </div>

    {{-- ── Simulate missed call ── --}}
    <div class="panel">
        <h2>{{ __('messages.sms.sim_heading') }}</h2>
        <p>{{ __('messages.sms.sim_desc') }}</p>
        <div class="form-row">
            <div class="form-group">
                <label>{{ __('messages.sms.caller_label') }}</label>
                <input type="text" id="callerInput" placeholder="e.g. 01811111111" value="01811111111">
            </div>
            <div class="form-group">
                <label>{{ __('messages.sms.called_label') }}</label>
                <input type="text" id="calledInput" placeholder="e.g. 01900000000" value="01900000000">
            </div>
            <button class="btn" onclick="simulateCall()">{{ __('messages.sms.send_btn') }}</button>
        </div>
        <div class="result-box" id="resultBox"></div>
    </div>

    <div class="section">
        <div class="section-header"><h2>{{ __('messages.sms.missed_log') }}</h2></div>
        @if($missedCalls->isEmpty())
            <div class="empty">{{ __('messages.sms.no_missed') }}</div>
        @else
        <table>
            <thead>
                <tr>
                    <th>{{ __('messages.sms.col_id') }}</th>
                    <th>{{ __('messages.sms.col_caller') }}</th>
                    <th>{{ __('messages.sms.col_called') }}</th>
                    <th>{{ __('messages.sms.col_worker') }}</th>
                    <th>{{ __('messages.sms.col_district') }}</th>
                    <th>{{ __('messages.sms.col_jobs_sent') }}</th>
                    <th>{{ __('messages.sms.col_status') }}</th>
                    <th>{{ __('messages.sms.col_time') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($missedCalls as $mc)
                <tr>
                    <td>{{ $mc->id }}</td>
                    <td><strong>{{ $mc->caller_number }}</strong></td>
                    <td>{{ $mc->called_number ?? '—' }}</td>
                    <td>{{ $mc->worker?->name ?? '<span style="color:#a0aec0">' . __('messages.sms.unregistered') . '</span>' }}</td>
                    <td>{{ $mc->district ?? '—' }}</td>
                    <td style="text-align:center">{{ $mc->jobs_sent }}</td>
                    <td><span class="badge {{ $mc->status }}">{{ $mc->status }}</span></td>
                    <td style="color:#718096;font-size:12px">{{ $mc->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- ── SMS Logs Table ── --}}
    <div class="section">
        <div class="section-header"><h2>{{ __('messages.sms.sms_log') }}</h2></div>
        @if($smsLogs->isEmpty())
            <div class="empty">{{ __('messages.sms.no_sms') }}</div>
        @else
        <table>
            <thead>
                <tr>
                    <th>{{ __('messages.sms.col_id') }}</th>
                    <th>{{ __('messages.sms.col_to') }}</th>
                    <th>{{ __('messages.sms.col_message') }}</th>
                    <th>{{ __('messages.sms.col_gateway') }}</th>
                    <th>{{ __('messages.sms.col_status') }}</th>
                    <th>{{ __('messages.sms.col_attempts') }}</th>
                    <th>{{ __('messages.sms.col_sent_at') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($smsLogs as $sms)
                <tr>
                    <td>{{ $sms->id }}</td>
                    <td><strong>{{ $sms->phone }}</strong></td>
                    <td><div class="sms-msg">{{ $sms->message }}</div></td>
                    <td><span style="font-size:12px;font-weight:600;color:#4a5568">{{ strtoupper($sms->gateway_used) }}</span></td>
                    <td><span class="badge {{ $sms->status }}">{{ $sms->status }}</span></td>
                    <td style="text-align:center">{{ $sms->attempt_count }}</td>
                    <td style="color:#718096;font-size:12px">
                        {{ $sms->sent_at ? $sms->sent_at->diffForHumans() : '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

</div>

<script>
async function simulateCall() {
    const caller = document.getElementById('callerInput').value.trim();
    const called = document.getElementById('calledInput').value.trim();
    const box    = document.getElementById('resultBox');

    if (!caller) { alert('Please enter a caller number.'); return; }

    box.style.display = 'none';

    try {
        const res = await fetch('/webhook/missed-call', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ caller_number: caller, called_number: called })
        });
        const data = await res.json();

        box.className = 'result-box ' + (res.ok ? 'ok' : 'err');
        box.innerHTML = res.ok
            ? `✓ Missed call processed! ID: <strong>${data.missed_call_id}</strong> — <a href="/sms/dashboard">Refresh page</a> to see the SMS logs.`
            : `✗ Error: ${JSON.stringify(data)}`;
        box.style.display = 'block';
    } catch(e) {
        box.className = 'result-box err';
        box.innerHTML = '✗ Request failed: ' + e.message;
        box.style.display = 'block';
    }
}
</script>

</body>
</html>
