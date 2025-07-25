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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable()->comment('nullable');
            $table->string('email')->nullable()->comment('nullable');
            $table->string('phone')->nullable()->comment('nullable');
            $table->text('address')->nullable()->comment('nullable');
            $table->string('tax_number')->nullable()->comment('nullable');
            $table->foreignId('country_id')->constrained();
            $table->text('notes')->nullable()->comment('nullable');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
}; 