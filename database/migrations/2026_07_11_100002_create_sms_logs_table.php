<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the sms_logs table — a permanent audit trail of every
 * SMS send attempt (pending, sent, or failed).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();

            // Recipient in E.164 format (+8801XXXXXXXXX)
            $table->string('phone', 20);

            // Full SMS body (Unicode / Bangla)
            $table->text('message');

            // Lifecycle status
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');

            // Raw response body / error string from the gateway
            $table->text('provider_response')->nullable();

            // Which gateway was used
            $table->string('gateway_used', 20)->default('ssl');

            // Cost in BDT (or USD for Twilio); 4 decimal places
            $table->decimal('cost', 8, 4)->default(0);

            // How many times sending was attempted (max 3)
            $table->tinyInteger('attempt_count')->unsigned()->default(0);

            // When the gateway confirmed delivery
            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            // Fast lookups for the console command and admin dashboard
            $table->index(['status', 'attempt_count']);
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
