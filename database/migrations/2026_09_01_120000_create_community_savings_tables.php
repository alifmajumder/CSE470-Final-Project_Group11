<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_savings_pools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->restrictOnDelete();
            $table->string('name', 120);
            $table->text('purpose')->nullable();
            $table->decimal('balance', 12, 2)->default(0);
            $table->decimal('target_amount', 12, 2)->nullable();
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->timestamps();
            $table->index(['owner_id', 'status']);
        });

        Schema::create('community_savings_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pool_id')->constrained('community_savings_pools')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('auto_deposit_amount', 10, 2)->nullable();
            $table->enum('auto_deposit_frequency', ['weekly', 'monthly'])->nullable();
            $table->date('next_auto_deposit_at')->nullable();
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();

            $table->unique(['pool_id', 'user_id']);
            $table->index(['next_auto_deposit_at', 'auto_deposit_frequency']);
        });

        Schema::create('community_savings_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pool_id')->constrained('community_savings_pools')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['deposit', 'withdrawal']);
            $table->decimal('amount', 10, 2);
            $table->string('purpose', 160)->nullable();
            $table->string('reference', 40)->unique();
            $table->timestamp('transacted_at')->useCurrent();
            $table->timestamps();

            $table->index(['pool_id', 'transacted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_savings_transactions');
        Schema::dropIfExists('community_savings_members');
        Schema::dropIfExists('community_savings_pools');
    }
};