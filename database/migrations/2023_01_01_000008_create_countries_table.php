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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable()->comment('nullable');
            $table->string('code', 3)->unique()->comment('ISO country code');
            $table->string('currency_code', 3)->comment('ISO currency code');
            $table->string('currency_symbol', 10);
            $table->string('currency')->nullable()->comment('nullable');
            $table->decimal('tax_rate', 5, 2)->default(0)->comment('VAT/Tax rate');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
}; 