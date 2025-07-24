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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue');
            $table->longText('payload');
            $table->tinyInteger('attempts');
            $table->integer('reserved_at')->nullable()->comment('nullable');
            $table->integer('available_at');
            $table->integer('created_at');
            
            $table->index('queue');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
}; 