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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable()->comment('nullable');
            $table->string('slug')->unique();
            $table->text('description')->nullable()->comment('nullable');
            $table->text('description_ar')->nullable()->comment('nullable');
            $table->string('image')->nullable()->comment('nullable');
            $table->string('icon')->nullable()->comment('nullable');
            $table->foreignId('parent_id')->nullable()->comment('nullable')->references('id')->on('categories');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('meta_title')->nullable()->comment('nullable');
            $table->string('meta_description')->nullable()->comment('nullable');
            $table->string('meta_keywords')->nullable()->comment('nullable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
}; 