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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('country_id')->constrained();
            $table->text('address')->nullable()->comment('nullable');
            $table->string('city')->nullable()->comment('nullable');
            $table->string('state')->nullable()->comment('nullable');
            $table->foreignId('governorate_id')->nullable()->comment('nullable')->constrained();
            $table->foreignId('district_id')->nullable()->comment('nullable')->constrained();
            $table->foreignId('area_id')->nullable()->comment('nullable')->constrained();
            $table->string('postal_code')->nullable()->comment('nullable');
            $table->string('address_line1')->nullable()->comment('nullable');
            $table->string('address_line2')->nullable()->comment('nullable');
            $table->string('phone')->nullable()->comment('nullable');
            $table->string('email')->nullable()->comment('nullable');
            $table->string('manager_name')->nullable()->comment('nullable');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
}; 