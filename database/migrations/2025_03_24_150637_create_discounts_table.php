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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('title_en')->comment('English Title with allowed symbols: %, $, ?, emojis');
            $table->string('title_ar')->comment('Arabic Title with allowed symbols: %, $, ?, emojis');
            $table->text('description_en')->nullable()->comment('English Description');
            $table->text('description_ar')->nullable()->comment('Arabic Description');
            $table->string('code')->unique()->nullable();
            $table->integer('percentage')->nullable()->check('percentage > 0 AND percentage <= 50')->comment('Integer value between 1 and 50');
            $table->date('start_date')->comment('Must be current or future date');
            $table->date('end_date')->comment('Must be current or future date and greater than start date');
            $table->string('image')->nullable()->comment('Optional Image URL');
            $table->integer('limit_amount');
            // Status (New, Active, Expired, Canceled)
            $table->enum('status', ['new', 'active', 'expired', 'canceled'])->default('new');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
