<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Links a specific worker to a specific task ("who actually did this job").
 *
 * This is the missing piece that Payment Records and Skill Badges both
 * build on: without it there's no way to know which worker to pay, or
 * which worker to credit with a completed job.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_workers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('worker_id')->constrained('users')->cascadeOnDelete();

            $table->enum('status', ['assigned', 'completed', 'cancelled'])->default('assigned');

            $table->timestamp('joined_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // A worker can only be signed up for a given task once.
            $table->unique(['task_id', 'worker_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_workers');
    }
};
