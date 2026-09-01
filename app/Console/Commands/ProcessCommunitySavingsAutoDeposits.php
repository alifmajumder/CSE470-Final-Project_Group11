<?php

namespace App\Console\Commands;

use App\Models\CommunitySavingsMember;
use App\Models\CommunitySavingsTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessCommunitySavingsAutoDeposits extends Command
{
    protected $signature = 'savings:process-auto-deposits';
    protected $description = 'Post due recurring Community Savings Pool contributions to the shared wallet';

    public function handle(): int
    {
        $processed = 0;

        CommunitySavingsMember::query()
            ->whereNotNull('auto_deposit_amount')
            ->whereNotNull('next_auto_deposit_at')
            ->whereDate('next_auto_deposit_at', '<=', today())
            ->with('pool')
            ->chunkById(100, function ($members) use (&$processed) {
                foreach ($members as $member) {
                    DB::transaction(function () use ($member, &$processed) {
                        $lockedMember = CommunitySavingsMember::whereKey($member->id)
                            ->lockForUpdate()
                            ->first();

                        if (!$lockedMember || !$lockedMember->auto_deposit_amount || !$lockedMember->next_auto_deposit_at) {
                            return;
                        }

                        $pool = $member->pool()->lockForUpdate()->first();

                        if (!$pool || $pool->status !== 'active') {
                            return;
                        }

                        $amount = (float) $lockedMember->auto_deposit_amount;
                        $pool->increment('balance', $amount);

                        CommunitySavingsTransaction::create([
                            'pool_id' => $pool->id,
                            'user_id' => $lockedMember->user_id,
                            'type' => 'deposit',
                            'amount' => $amount,
                            'purpose' => 'Scheduled auto-deposit',
                            'reference' => 'AD' . strtoupper(bin2hex(random_bytes(5))),
                            'transacted_at' => now(),
                        ]);

                        $next = $lockedMember->next_auto_deposit_at->copy();
                        do {
                            $next = $lockedMember->auto_deposit_frequency === 'weekly'
                                ? $next->addWeek()
                                : $next->addMonth();
                        } while ($next->lte(today()));

                        $lockedMember->update(['next_auto_deposit_at' => $next]);
                        $processed++;
                    });
                }
            });

        $this->info("Processed {$processed} auto-deposit(s).");

        return self::SUCCESS;
    }
}