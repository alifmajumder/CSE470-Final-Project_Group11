<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the missed_calls table — records every inbound missed call
 * to the system DID so we can audit what happened and replay if needed.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('missed_calls', function (Blueprint $table) {
            $table->id();

            // The worker's phone (caller)
            $table->string('caller_number', 20);

            // The platform's virtual / DID number (called)
            $table->string('called_number', 20)->nullable();

            // Resolved user — null if the caller is not yet registered
            $table->foreignId('worker_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Worker's district at the time of the call (for job matching)
            $table->string('district', 100)->nullable();

            // Number of job SMS dispatched in response
            $table->tinyInteger('jobs_sent')->unsigned()->default(0);

            // Outcome of this missed call
            $table->enum('status', [
                'processing',
                'jobs_sent',
                'no_jobs',
                'welcome_sent',
                'error',
            ])->default('processing');

            $table->timestamps();

            $table->index('caller_number');
            $table->index('worker_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('missed_calls');
    }
};
