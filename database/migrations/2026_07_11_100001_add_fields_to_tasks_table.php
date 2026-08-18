<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the core fields to the tasks table so the SMS system can
 * format and dispatch meaningful job alert messages.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('employer_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('id');

            $table->string('title', 255)->after('employer_id');
            $table->text('description')->nullable()->after('title');

            // Wage stored as a string so it can say "৫০০" or "500-700"
            $table->string('wage', 50)->nullable()->after('description');

            $table->string('district', 100)->nullable()->after('wage');
            $table->string('location', 255)->nullable()->after('district');

            $table->integer('required_workers')->default(1)->after('location');
            $table->integer('registered_workers')->default(0)->after('required_workers');

            $table->enum('status', ['active', 'inactive', 'completed', 'cancelled'])
                  ->default('active')
                  ->after('location');

            // Index for the "fetch nearby active jobs" query
            $table->index(['status', 'district']);
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['status', 'district']);
            $table->dropForeign(['employer_id']);
            $table->dropColumn([
                'employer_id', 'title', 'description',
                'wage', 'district', 'location', 'status',
            ]);
        });
    }
};
