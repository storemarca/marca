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
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->decimal('base_cost', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable()->comment('nullable');
            $table->boolean('weight_based')->default(false);
            $table->decimal('cost_per_kg', 10, 2)->default(0);
            $table->decimal('free_shipping_threshold', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
}; 