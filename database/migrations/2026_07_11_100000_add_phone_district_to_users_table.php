<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds phone number and district to the users table.
 * Required for the Missed Call feature to match callers to workers.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // BD phone numbers: +8801XXXXXXXXX (14 chars with +) — give headroom
            $table->string('phone', 20)->nullable()->unique()->after('email');
            // Division / district (e.g. "Rajshahi", "Sylhet")
            $table->string('district', 100)->nullable()->after('phone');
            // Worker role for future RBAC
            $table->enum('role', ['worker', 'employer', 'admin'])
                  ->default('worker')
                  ->after('district');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['phone']);
            $table->dropColumn(['phone', 'district', 'role']);
        });
    }
};
