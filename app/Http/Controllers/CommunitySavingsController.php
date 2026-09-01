<?php

namespace App\Http\Controllers;

use App\Models\CommunitySavingsMember;
use App\Models\CommunitySavingsPool;
use App\Models\CommunitySavingsTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CommunitySavingsController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $pools = CommunitySavingsPool::whereHas('members', fn ($q) => $q->where('user_id', $userId))
            ->with(['owner', 'members.user'])
            ->where('status', 'active')
            ->latest()
            ->get();

        $memberships = CommunitySavingsMember::where('user_id', $userId)
            ->whereIn('pool_id', $pools->pluck('id'))
            ->get()
            ->keyBy('pool_id');

        return view('savings.index', compact('pools', 'memberships'));
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'purpose' => 'nullable|string|max:1000',
            'target_amount' => 'nullable|numeric|min:0.01|max:9999999999.99',
            'initial_contribution' => 'nullable|numeric|min:0|max:9999999999.99',
        ]);

        $pool = DB::transaction(function () use ($validated) {
            $pool = CommunitySavingsPool::create([
                'owner_id' => Auth::id(),
                'name' => $validated['name'],
                'purpose' => $validated['purpose'] ?? null,
                'target_amount' => $validated['target_amount'] ?? null,
            ]);

            CommunitySavingsMember::create([
                'pool_id' => $pool->id,
                'user_id' => Auth::id(),
                'joined_at' => now(),
            ]);

            $initial = (float) ($validated['initial_contribution'] ?? 0);
            if ($initial > 0) {
                $this->recordDeposit($pool->id, Auth::id(), $initial, 'Opening contribution');
            }

            return $pool;
        });

        return redirect()->route('savings.index')->with('success', "Savings pool '{$pool->name}' created.");
    }

    public function addMember(Request $request, CommunitySavingsPool $pool)
    {
        $this->ensureOwner($pool);

        $validated = $request->validate([
            'member_identifier' => 'required|string|max:255',
        ]);

        $user = User::where('email', $validated['member_identifier'])
            ->orWhere('phone', $validated['member_identifier'])
            ->first();

        if (!$user) {
            return back()->withErrors(['member_identifier' => 'No RuralConnect user was found with that email or phone.']);
        }

        if ($user->id === Auth::id()) {
            return back()->withErrors(['member_identifier' => 'You are already the pool owner/member.']);
        }

        if ($user->role !== 'worker') {
            return back()->withErrors(['member_identifier' => 'Only worker accounts can join a Community Savings Pool.']);
        }

        if (!$user->is_verified) {
            return back()->withErrors(['member_identifier' => 'This worker must be verified by an admin before joining a savings pool.']);
        }

        if ($pool->isMember($user->id)) {
            return back()->withErrors(['member_identifier' => 'That worker is already a member of this pool.']);
        }

        CommunitySavingsMember::create([
            'pool_id' => $pool->id,
            'user_id' => $user->id,
            'joined_at' => now(),
        ]);

        return back()->with('success', "{$user->name} was added to {$pool->name}.");
    }

    public function removeMember(CommunitySavingsPool $pool, CommunitySavingsMember $member)
    {
        $this->ensureOwner($pool);

        if ($member->pool_id !== $pool->id) {
            abort(404);
        }

        if ($member->user_id === $pool->owner_id) {
            return back()->withErrors(['member' => 'The pool owner cannot be removed.']);
        }

        $member->delete();

        return back()->with('success', 'Member removed from the savings pool.');
    }

    public function deposit(Request $request, CommunitySavingsPool $pool)
    {
        $this->ensureMember($pool);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:9999999999.99',
            'purpose' => 'nullable|string|max:160',
        ]);

        $this->recordDeposit($pool->id, Auth::id(), (float) $validated['amount'], $validated['purpose'] ?? 'Member contribution');

        return back()->with('success', 'Contribution added to the shared wallet.');
    }

    public function setAutoDeposit(Request $request, CommunitySavingsPool $pool)
    {
        $this->ensureMember($pool);

        $validated = $request->validate([
            'auto_deposit_amount' => 'required|numeric|min:1|max:9999999999.99',
            'auto_deposit_frequency' => ['required', Rule::in(['weekly', 'monthly'])],
        ]);

        $member = CommunitySavingsMember::where('pool_id', $pool->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $member->update([
            'auto_deposit_amount' => $validated['auto_deposit_amount'],
            'auto_deposit_frequency' => $validated['auto_deposit_frequency'],
            'next_auto_deposit_at' => $this->nextDueDate($validated['auto_deposit_frequency']),
        ]);

        return back()->with('success', 'Auto-deposit schedule saved.');
    }

    public function disableAutoDeposit(CommunitySavingsPool $pool)
    {
        $this->ensureMember($pool);

        CommunitySavingsMember::where('pool_id', $pool->id)
            ->where('user_id', Auth::id())
            ->update([
                'auto_deposit_amount' => null,
                'auto_deposit_frequency' => null,
                'next_auto_deposit_at' => null,
            ]);

        return back()->with('success', 'Auto-deposit disabled.');
    }

    public function withdraw(Request $request, CommunitySavingsPool $pool)
    {
        $this->ensureOwner($pool);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:9999999999.99',
            'purpose' => 'required|string|max:160',
        ]);

        DB::transaction(function () use ($pool, $validated) {
            $lockedPool = CommunitySavingsPool::whereKey($pool->id)->lockForUpdate()->firstOrFail();
            $amount = (float) $validated['amount'];

            if ((float) $lockedPool->balance < $amount) {
                abort(422, 'Insufficient savings pool balance.');
            }

            $lockedPool->decrement('balance', $amount);

            CommunitySavingsTransaction::create([
                'pool_id' => $lockedPool->id,
                'user_id' => Auth::id(),
                'type' => 'withdrawal',
                'amount' => $amount,
                'purpose' => $validated['purpose'],
                'reference' => $this->reference('W'),
                'transacted_at' => now(),
            ]);
        });

        return back()->with('success', 'Withdrawal recorded from the shared wallet.');
    }

    private function recordDeposit(int $poolId, int $userId, float $amount, string $purpose): void
    {
        DB::transaction(function () use ($poolId, $userId, $amount, $purpose) {
            $pool = CommunitySavingsPool::whereKey($poolId)->lockForUpdate()->firstOrFail();

            if ($pool->status !== 'active') {
                abort(422, 'This savings pool is closed.');
            }

            $pool->increment('balance', $amount);

            CommunitySavingsTransaction::create([
                'pool_id' => $pool->id,
                'user_id' => $userId,
                'type' => 'deposit',
                'amount' => $amount,
                'purpose' => $purpose,
                'reference' => $this->reference('D'),
                'transacted_at' => now(),
            ]);
        });
    }

    private function nextDueDate(string $frequency): Carbon
    {
        return $frequency === 'weekly'
            ? now()->addWeek()->startOfDay()
            : now()->addMonth()->startOfDay();
    }

    private function reference(string $prefix): string
    {
        return $prefix . strtoupper(bin2hex(random_bytes(6)));
    }

    private function ensureMember(CommunitySavingsPool $pool): void
    {
        abort_unless($pool->isMember(Auth::id()), 403);
    }

    private function ensureOwner(CommunitySavingsPool $pool): void
    {
        abort_unless($pool->owner_id === Auth::id(), 403);
    }
}