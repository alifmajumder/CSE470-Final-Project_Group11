<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_workers', function (Blueprint $table) {
            $table->string('contract_otp', 10)->nullable();
            $table->timestamp('contract_confirmed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('task_workers', function (Blueprint $table) {
            $table->dropColumn(['contract_otp', 'contract_confirmed_at']);
        });
    }
};