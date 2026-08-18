<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Feature: Work Completion Photo Upload.
 *
 * Lets worker attach a photo of the finished job (e.g. a ploughed
 * field) to their task_workers row as proof of completion, so the
 * employer has evidence before marking the job complete / releasing
 * payment -- reducing "was it really done?" disputes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_workers', function (Blueprint $table) {
            $table->string('completion_photo_path')->nullable()->after('completed_at');
            $table->timestamp('completion_photo_uploaded_at')->nullable()->after('completion_photo_path');
        });
    }

    public function down(): void
    {
        Schema::table('task_workers', function (Blueprint $table) {
            $table->dropColumn(['completion_photo_path', 'completion_photo_uploaded_at']);
        });
    }
};