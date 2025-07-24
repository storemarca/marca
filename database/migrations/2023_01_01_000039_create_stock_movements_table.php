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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->integer('old_quantity');
            $table->integer('new_quantity');
            $table->integer('quantity_change');
            $table->string('operation');
            $table->string('reason')->nullable()->comment('nullable');
            $table->foreignId('order_id')->nullable()->comment('nullable')->constrained()->onDelete('set null');
            $table->foreignId('purchase_order_id')->nullable()->comment('nullable')->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->comment('nullable')->constrained()->onDelete('set null');
            $table->timestamps();

            $table->index(['product_id', 'warehouse_id']);
            $table->index('operation');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
}; 