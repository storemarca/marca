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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable()->comment('nullable');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->text('description')->nullable()->comment('nullable');
            $table->text('description_ar')->nullable()->comment('nullable');
            $table->foreignId('category_id')->nullable()->comment('nullable')->constrained();
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->nullable()->comment('nullable');
            $table->decimal('cost', 10, 2)->nullable()->comment('nullable');
            $table->decimal('weight', 8, 2)->nullable()->comment('nullable');
            $table->string('weight_unit')->default('kg');
            $table->decimal('length', 8, 2)->nullable()->comment('nullable');
            $table->decimal('width', 8, 2)->nullable()->comment('nullable');
            $table->decimal('height', 8, 2)->nullable()->comment('nullable');
            $table->string('dimension_unit')->default('cm');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_digital')->default(false);
            $table->boolean('is_virtual')->default(false);
            $table->boolean('is_backorder')->default(false);
            $table->boolean('is_preorder')->default(false);
            $table->integer('stock_quantity')->default(0);
            $table->string('stock_status')->default('in_stock');
            $table->string('tax_class')->nullable()->comment('nullable');
            $table->string('meta_title')->nullable()->comment('nullable');
            $table->text('meta_description')->nullable()->comment('nullable');
            $table->string('meta_keywords')->nullable()->comment('nullable');
            $table->text('short_description')->nullable()->comment('nullable');
            $table->string('barcode')->nullable()->comment('nullable');
            $table->foreignId('warehouse_id')->nullable()->comment('nullable');
            $table->json('images')->nullable()->comment('nullable, Cloudinary URLs');
            $table->string('video_url')->nullable()->comment('nullable, YouTube URL');
            $table->integer('pieces_count')->default(1)->nullable()->comment('عدد القطع في المنتج');
            $table->json('videos')->nullable()->comment('nullable - فيديوهات المنتج');
            $table->json('colors')->nullable()->comment('nullable - الألوان المتاحة');
            $table->json('sizes')->nullable()->comment('nullable - المقاسات المتاحة');
            $table->json('attributes')->nullable()->comment('nullable, attributes (color, size, etc.)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
}; 