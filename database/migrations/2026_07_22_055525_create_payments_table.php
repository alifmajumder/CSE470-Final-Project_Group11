<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Payment Record & Receipt feature.
 *
 * A permanent, tamper-evident record of a payment made to a worker for a
 * task, whether paid in cash or via mobile banking (bKash/Nagad). Each row
 * is a receipt: it captures who paid whom, how much, by what method, and
 * (for mobile banking) the transaction ID, plus an optional worker-side
 * confirmation so both sides have a shared record and there's no argument
 * later about whether payment happened.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->string('receipt_number', 30)->unique();

            // nullOnDelete rather than cascade: a financial record shouldn't
            // disappear just because the task or an account was later removed.
            $table->foreignId('task_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('employer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('worker_id')->nullable()->constrained('users')->nullOnDelete();

            $table->decimal('amount', 10, 2);
            $table->enum('method', ['cash', 'bkash', 'nagad'])->default('cash');

            // bKash/Nagad transaction ID (TrxID). Not applicable to cash.
            $table->string('transaction_reference', 100)->nullable();

            $table->text('note')->nullable();

            $table->timestamp('paid_at');

            // Worker confirms they actually received it -- second, independent
            // signature on the same record, so it's not just the employer's word.
            $table->timestamp('worker_confirmed_at')->nullable();

            $table->timestamps();

            $table->index(['worker_id', 'paid_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
