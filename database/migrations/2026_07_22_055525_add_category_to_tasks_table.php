<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds a job category to tasks (e.g. "fishing", "farming").
 *
 * This powers the Skill Badge System: when a worker completes a task,
 * the task's category tells us which skill badge (if any) to credit
 * them for. See config/skills.php for the category list.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('category', 50)->default('other')->after('title');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropColumn('category');
        });
    }
};
