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
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable()->comment('nullable');
            $table->dateTime('sale_price_start_date')->nullable()->comment('nullable');
            $table->decimal('cost', 10, 2)->nullable()->comment('nullable');
            $table->dateTime('sale_price_end_date')->nullable()->comment('nullable');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['product_id', 'country_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
}; 