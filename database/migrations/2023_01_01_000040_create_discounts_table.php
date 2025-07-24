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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('code')->unique();
            $table->string('type');
            $table->decimal('value', 10, 2);
            $table->decimal('min_order_amount', 10, 2);
            $table->decimal('max_discount_amount', 10, 2);
            $table->integer('usage_limit_per_user');
            $table->integer('usage_limit_total');
            $table->timestamp('starts_at')->nullable(); // ✅ فقط مرة واحدة
            $table->timestamp('ends_at')->nullable();   // ✅ فقط مرة واحدة
            $table->boolean('is_active');
            $table->string('applies_to');
        
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('country_id')->nullable()->constrained()->onDelete('set null');
        
            $table->json('conditions');
            $table->timestamps();
        
            $table->index('code');
            $table->index('is_active');
        });
        
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
}; 