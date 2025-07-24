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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_gateway_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->comment('Transaction ID from the payment gateway');
            $table->decimal('amount', 15, 4);
            $table->decimal('fee', 15, 4)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->string('status')->comment('Enum: pending, paid, failed, refunded, cancelled');
            $table->text('response_data')->comment('Raw response from payment gateway');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
}; 