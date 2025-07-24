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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable()->comment('nullable');
            $table->foreignId('user_id')->nullable()->comment('nullable')->constrained()->onDelete('set null');
            $table->foreignId('country_id')->constrained();
            $table->string('address_line1');
            $table->string('address_line2')->nullable()->comment('nullable');
            $table->string('city');
            $table->foreignId('governorate_id')->nullable()->comment('nullable');
            $table->foreignId('district_id')->nullable()->comment('nullable');
            $table->foreignId('area_id')->nullable()->comment('nullable');
            $table->string('state')->nullable()->comment('nullable');
            $table->string('postal_code')->nullable()->comment('nullable');
            $table->string('phone');
            $table->boolean('is_default_shipping')->default(false);
            $table->boolean('is_default_billing')->default(false);
            $table->string('label')->nullable()->comment('nullable');
            $table->text('delivery_instructions')->nullable()->comment('nullable');
            $table->decimal('latitude', 10, 8)->nullable()->comment('nullable');
            $table->decimal('longitude', 11, 8)->nullable()->comment('nullable');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
}; 