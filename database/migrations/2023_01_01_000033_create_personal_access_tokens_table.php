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
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tokenable_id');
            $table->string('tokenable_type');
            $table->text('name');
            $table->string('token', 64)->unique()->comment('Length 64');
            $table->text('abilities')->nullable()->comment('nullable');
            $table->timestamp('last_used_at')->nullable()->comment('nullable');
            $table->timestamp('expires_at')->nullable()->comment('nullable');
            $table->timestamps();

            $table->index(['tokenable_id', 'tokenable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
}; 