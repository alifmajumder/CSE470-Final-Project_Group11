<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_workers', function (Blueprint $table) {
            $table->tinyInteger('employer_rating')->nullable()->after('completed_at');
            $table->text('employer_review')->nullable()->after('employer_rating');
        });
    }

    public function down(): void
    {
        Schema::table('task_workers', function (Blueprint $table) {
            $table->dropColumn(['employer_rating', 'employer_review']);
        });
    }
};