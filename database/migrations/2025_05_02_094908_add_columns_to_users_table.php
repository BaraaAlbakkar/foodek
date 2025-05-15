<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('phone_number');
            $table->date('join_date')->nullable()->after('username');
            $table->boolean('is_active')->default(false)->after('join_date');
            $table->boolean('is_logged_in')->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username','join_date','is_active','is_logged_in']);
        });
    }
};
