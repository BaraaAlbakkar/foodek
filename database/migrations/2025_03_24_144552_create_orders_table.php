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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['Active', 'On Shipment', 'Complete','canceled'])->default('Active');
            $table->decimal('total_price', 10, 2);
            $table->decimal('delivery_charge',10,2)->default(0.00);
            $table->time('estimated_delivery_time')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->timestamps();
        });
    }//

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
