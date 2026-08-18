<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('upazila')->nullable()->after('district');
            $table->json('skills')->nullable()->after('upazila');
            $table->string('nid')->nullable()->after('skills');
            $table->boolean('sms_opt_in')->default(true)->after('nid');
            $table->string('locale', 2)->default('en')->after('sms_opt_in');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'upazila',
                'skills',
                'nid',
                'sms_opt_in',
                'locale'
            ]);
        });
    }
};