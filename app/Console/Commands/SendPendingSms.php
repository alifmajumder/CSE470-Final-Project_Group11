<?php

namespace App\Console\Commands;

use App\Jobs\SendSmsJob;
use App\Models\SmsLog;
use Illuminate\Console\Command;

class SendPendingSms extends Command
{
    protected $signature = 'sms:send-pending
                            {--limit=50 : Maximum SMS records to process per run}
                            {--retry-failed : Also requeue failed SMS (under max 3 attempts)}';

    protected $description = 'Dispatch queued jobs for all pending (and optionally failed) SMS records';

    public function handle(): int
    {
        $limit        = (int) $this->option('limit');
        $retryFailed  = (bool) $this->option('retry-failed');

        // ------------------------------------------------------------------
        // 1. Build the query
        // ------------------------------------------------------------------
        $statuses = ['pending'];
        if ($retryFailed) {
            $statuses[] = 'failed';
        }

        $records = SmsLog::whereIn('status', $statuses)
            ->where('attempt_count', '<', 3)
            ->latest()
            ->take($limit)
            ->get();

        if ($records->isEmpty()) {
            $this->info('No SMS records to process.');
            $this->printStats();
            return Command::SUCCESS;
        }

        $this->info("Processing {$records->count()} SMS record(s) ...");

        // ------------------------------------------------------------------
        // 2. Dispatch jobs with a progress bar
        // ------------------------------------------------------------------
        $bar = $this->output->createProgressBar($records->count());
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %message%');
        $bar->start();

        $dispatched = 0;
        foreach ($records as $smsLog) {
            $bar->setMessage("Queueing ID {$smsLog->id} → {$smsLog->phone}");
            SendSmsJob::dispatch($smsLog->id);
            $dispatched++;
            $bar->advance();
        }

        $bar->setMessage('Done.');
        $bar->finish();
        $this->newLine(2);

        $this->info("Dispatched {$dispatched} job(s) to the queue.");

        // ------------------------------------------------------------------
        // 3. Print overall stats table
        // ------------------------------------------------------------------
        $this->printStats();

        return Command::SUCCESS;
    }

    // -------------------------------------------------------------------------

    protected function printStats(): void
    {
        $stats = SmsLog::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        if (empty($stats)) {
            return;
        }

        $this->newLine();
        $this->table(
            ['Status', 'Count'],
            collect($stats)
                ->map(fn ($count, $status) => [$status, $count])
                ->values()
                ->toArray()
        );
    }
}
