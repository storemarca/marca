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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('shipping_company_id')->nullable();
            $table->foreign('shipping_company_id')->references('id')->on('shipping_companies')->nullOnDelete();            $table->string('tracking_number');
            $table->string('tracking_url');
            $table->string('status')->default('pending')->comment('Enum: pending, processing, shipped, in_transit, out_for_delivery, delivered, failed, returned');
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('cod_amount', 15, 2)->default(0);
            $table->boolean('is_cod')->default(false);
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('tracking_history')->nullable()->comment('nullable');
            $table->timestamp('last_tracking_update')->nullable()->comment('nullable');
            $table->timestamp('last_status_update')->nullable()->comment('nullable');
            $table->timestamp('expected_delivery_date')->nullable()->comment('nullable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
}; 