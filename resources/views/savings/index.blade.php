<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Savings Pool - RuralConnect</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f4f7f6; color: #2c3e50; }
        .page { max-width: 1100px; margin: 0 auto; padding: 30px 20px 60px; }
        .nav { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
        a { color:#3182ce; text-decoration:none; }
        .back { background:#2c3e50; color:white; padding:9px 14px; border-radius:6px; }
        h1 { color:#27ae60; margin-bottom:6px; }
        .subtitle { color:#718096; margin-top:0; }
        .card { background:white; border-radius:12px; padding:22px; margin:18px 0; box-shadow:0 4px 15px rgba(0,0,0,.05); }
        .grid { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
        .wallet { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:18px; }
        .balance { font-size:32px; font-weight:700; color:#166534; }
        .muted { color:#718096; font-size:13px; }
        label { display:block; font-weight:600; font-size:13px; margin:10px 0 5px; }
        input, textarea, select { width:100%; box-sizing:border-box; padding:9px 10px; border:1px solid #cbd5e0; border-radius:6px; font:inherit; }
        textarea { min-height:70px; resize:vertical; }
        button { border:0; border-radius:6px; padding:9px 14px; background:#27ae60; color:white; font-weight:700; cursor:pointer; margin-top:10px; }
        button.secondary { background:#2c3e50; }
        button.danger { background:#e53e3e; }
        .alert { padding:11px 14px; border-radius:6px; margin:12px 0; }
        .success { background:#d1fae5; color:#065f46; }
        .error { background:#fed7d7; color:#9b2c2c; }
        ul.errors { margin:0; padding-left:20px; }
        .members { width:100%; border-collapse:collapse; margin-top:12px; }
        .members th, .members td { padding:10px; border-bottom:1px solid #edf2f7; text-align:left; font-size:13px; }
        .members th { color:#718096; font-size:11px; text-transform:uppercase; }
        .transaction { display:flex; justify-content:space-between; gap:15px; padding:9px 0; border-bottom:1px solid #edf2f7; font-size:13px; }
        .deposit { color:#166534; font-weight:700; }
        .withdrawal { color:#b91c1c; font-weight:700; }
        .owner-tag { background:#e2e8f0; border-radius:12px; padding:3px 8px; font-size:11px; }
        @media(max-width:800px){ .grid{grid-template-columns:1fr;} .page{padding:20px 12px;} }
    </style>
</head>
<body>
<div class="page">
    <div class="nav">
        <a href="/" class="back">&larr; Back to Home</a>
        <a href="{{ route('savings.index') }}">Refresh</a>
    </div>

    <h1>🤝 Community Savings Pool</h1>
    <p class="subtitle">A digital Somiti where trusted workers save together for medical emergencies or shared equipment.</p>

    @if(session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert error">
            <ul class="errors">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <h2>Create a new Somiti</h2>
        <form action="{{ route('savings.create') }}" method="POST">
            @csrf
            <div class="grid">
                <div>
                    <label>Pool name</label>
                    <input name="name" placeholder="e.g. Village Harvest Somiti" required maxlength="120">
                </div>
                <div>
                    <label>Target amount (optional)</label>
                    <input type="number" name="target_amount" min="0.01" step="0.01" placeholder="৳50000">
                </div>
            </div>
            <label>Purpose</label>
            <textarea name="purpose" maxlength="1000" placeholder="Medical emergencies, shared irrigation pump, farming equipment..."></textarea>
            <label>Opening contribution (optional)</label>
            <input type="number" name="initial_contribution" min="0" step="0.01" placeholder="৳500">
            <button type="submit">Create Savings Pool</button>
        </form>
    </div>

    @forelse($pools as $pool)
        @php
            $myMembership = $memberships->get($pool->id);
            $transactions = $pool->transactions()->with('user')->limit(10)->get();
        @endphp

        <div class="card">
            <div style="display:flex;justify-content:space-between;gap:15px;align-items:flex-start;">
                <div>
                    <h2 style="margin:0 0 5px;">{{ $pool->name }}</h2>
                    <div class="muted">Owner: {{ $pool->owner->name }} @if($pool->owner_id === Auth::id()) <span class="owner-tag">You</span> @endif</div>
                    @if($pool->purpose)<p>{{ $pool->purpose }}</p>@endif
                </div>
                @if($pool->target_amount)
                    <div class="muted">Target: ৳{{ number_format($pool->target_amount, 2) }}</div>
                @endif
            </div>

            <div class="wallet">
                <div class="muted">Shared digital wallet balance</div>
                <div class="balance">৳{{ number_format($pool->balance, 2) }}</div>
            </div>

            <div class="grid">
                <div>
                    <h3>Make a contribution</h3>
                    <form action="{{ route('savings.deposit', $pool) }}" method="POST">
                        @csrf
                        <label>Amount</label>
                        <input type="number" name="amount" min="1" step="0.01" required>
                        <label>Purpose / note</label>
                        <input name="purpose" maxlength="160" placeholder="Regular savings">
                        <button type="submit">Deposit to Wallet</button>
                    </form>
                </div>

                <div>
                    <h3>My auto-deposit</h3>
                    @if($myMembership->auto_deposit_amount)
                        <p><strong>৳{{ number_format($myMembership->auto_deposit_amount, 2) }}</strong>
                            {{ $myMembership->auto_deposit_frequency }} · next due
                            {{ $myMembership->next_auto_deposit_at?->format('d M Y') }}</p>
                        <form action="{{ route('savings.auto.disable', $pool) }}" method="POST">
                            @csrf
                            <button class="secondary" type="submit">Disable Auto-Deposit</button>
                        </form>
                    @endif
                    <form action="{{ route('savings.auto.set', $pool) }}" method="POST">
                        @csrf
                        <label>Recurring amount</label>
                        <input type="number" name="auto_deposit_amount" min="1" step="0.01" required>
                        <label>Frequency</label>
                        <select name="auto_deposit_frequency" required>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                        <button type="submit">Save Auto-Deposit</button>
                    </form>
                    <p class="muted">The scheduled command records the contribution automatically when it becomes due. Connect this ledger to a real payment gateway before using real money.</p>
                </div>
            </div>

            <h3>Members ({{ $pool->members->count() }})</h3>
            <table class="members">
                <thead><tr><th>Worker</th><th>Phone</th><th>Auto-deposit</th><th>Action</th></tr></thead>
                <tbody>
                @foreach($pool->members as $member)
                    <tr>
                        <td><strong>{{ $member->user->name }}</strong> @if($member->user_id === $pool->owner_id)<span class="owner-tag">Owner</span>@endif</td>
                        <td>{{ $member->user->phone ?? 'N/A' }}</td>
                        <td>
                            @if($member->auto_deposit_amount)
                                ৳{{ number_format($member->auto_deposit_amount, 2) }} / {{ $member->auto_deposit_frequency }}
                            @else
                                <span class="muted">Not set</span>
                            @endif
                        </td>
                        <td>
                            @if($pool->owner_id === Auth::id() && $member->user_id !== $pool->owner_id)
                                <form action="{{ route('savings.members.remove', [$pool, $member]) }}" method="POST" onsubmit="return confirm('Remove this member?');">
                                    @csrf @method('DELETE')
                                    <button class="danger" type="submit">Remove</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            @if($pool->owner_id === Auth::id())
                <div style="margin-top:18px;padding-top:15px;border-top:1px solid #edf2f7;">
                    <h3>Add trusted worker</h3>
                    <p class="muted">Only verified worker accounts can be added.</p>
                    <form action="{{ route('savings.members.add', $pool) }}" method="POST" style="display:flex;gap:8px;flex-wrap:wrap;">
                        @csrf
                        <input style="flex:1;min-width:220px;" name="member_identifier" placeholder="Worker email or phone" required>
                        <button type="submit">Add Member</button>
                    </form>
                </div>

                <div class="grid" style="margin-top:18px;padding-top:15px;border-top:1px solid #edf2f7;">
                    <div>
                        <h3>Use pool funds</h3>
                        <p class="muted">Owner-only withdrawal for an approved emergency or shared-equipment purpose.</p>
                        <form action="{{ route('savings.withdraw', $pool) }}" method="POST">
                            @csrf
                            <label>Amount</label>
                            <input type="number" name="amount" min="1" step="0.01" max="{{ $pool->balance }}" required>
                            <label>Purpose</label>
                            <input name="purpose" maxlength="160" placeholder="Medical emergency / shared equipment" required>
                            <button class="secondary" type="submit">Record Withdrawal</button>
                        </form>
                    </div>
                </div>
            @endif

            <h3>Recent wallet activity</h3>
            @forelse($transactions as $transaction)
                <div class="transaction">
                    <div>
                        <strong>{{ $transaction->user->name ?? 'Former member' }}</strong>
                        <div class="muted">{{ $transaction->purpose ?? ucfirst($transaction->type) }} · {{ $transaction->transacted_at->format('d M Y, h:i A') }}</div>
                    </div>
                    <div class="{{ $transaction->type === 'deposit' ? 'deposit' : 'withdrawal' }}">
                        {{ $transaction->type === 'deposit' ? '+' : '-' }}৳{{ number_format($transaction->amount, 2) }}
                    </div>
                </div>
            @empty
                <p class="muted">No wallet activity yet.</p>
            @endforelse
        </div>
    @empty
        <div class="card"><p>No savings pools yet. Create one above and add trusted workers.</p></div>
    @endforelse
</div>
</body>
</html>