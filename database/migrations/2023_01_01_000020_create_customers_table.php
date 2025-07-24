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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->comment('nullable with ON DELETE CASCADE')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable()->comment('nullable');
            $table->string('phone')->nullable()->comment('nullable');
            $table->string('name')->nullable()->comment('nullable');
            $table->text('notes')->nullable()->comment('nullable');
            $table->date('birth_date')->nullable()->comment('nullable');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->comment('nullable');
            $table->foreignId('default_country_id')->nullable()->comment('nullable')->constrained('countries');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login')->nullable()->comment('nullable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
}; 