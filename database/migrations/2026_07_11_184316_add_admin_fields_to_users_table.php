<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // NOTE: 'role' is already added (as an enum) by the earlier
            // 2026_07_11_100000_add_phone_district_to_users_table migration.
            // Re-adding it here caused `php artisan migrate` to fail with
            // "duplicate column name: role", so only 'is_verified' belongs here.
            $table->boolean('is_verified')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_verified']);
        });
    }
};