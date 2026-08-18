<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Skill Badge System.
 *
 * One row per badge a worker has actually earned (e.g. "Verified Fisher"
 * after 3 completed fishing jobs). Acts as the durable "digital resume"
 * record -- see App\Services\BadgeService for the award logic and
 * config/skills.php for the badge catalog.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('category', 50);
            $table->string('badge_label', 100);
            $table->unsignedInteger('jobs_completed_at_award');

            $table->timestamp('earned_at');

            $table->timestamps();

            // Only one badge per skill category per worker.
            $table->unique(['user_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_badges');
    }
};
