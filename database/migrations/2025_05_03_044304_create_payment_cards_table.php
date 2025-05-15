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
        Schema::create('payment_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade'); // Assuming there's a users table
            $table->enum('card_type', ['Visa', 'MasterCard', 'Local Wallet']);
            $table->string('card_number')->nullable();  // Store card number securely, or only store last 4 digits.
            $table->string('last_4_digits', 4);
            $table->string('holder_name');
            $table->date('expiry_date');
            $table->string('cvc_code')->nullable();  // Should be stored securely
            $table->string('title')->nullable();  // Optional custom title for the card
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_cards');
    }
};
