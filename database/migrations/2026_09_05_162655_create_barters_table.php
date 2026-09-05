<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('offering', 150);
            $table->string('seeking', 150);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barters');
    }
};