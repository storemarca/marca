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
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->integer('reserved_quantity')->default(0)->comment('Quantity reserved for orders');
            $table->decimal('cost_price', 15, 2)->nullable()->comment('nullable, cost price');
            $table->decimal('selling_price', 15, 2);
            $table->decimal('sale_price', 15, 2)->nullable()->comment('nullable');
            $table->timestamp('sale_start_date')->nullable()->comment('nullable');
            $table->timestamp('sale_end_date')->nullable()->comment('nullable');
            $table->timestamps();

            $table->unique(['product_id', 'warehouse_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_stocks');
    }
}; 