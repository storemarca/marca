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
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('rating')->unsigned();
            $table->string('title')->nullable();
            $table->text('review');
            $table->json('pros')->nullable();
            $table->json('cons')->nullable();
            $table->boolean('verified_purchase')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->integer('helpful_votes')->unsigned()->default(0);
            $table->integer('unhelpful_votes')->unsigned()->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('product_review_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_review_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->string('thumbnail_path')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });

        Schema::create('product_review_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_review_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->boolean('is_helpful');
            $table->timestamps();

            $table->unique(['product_review_id', 'customer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_review_votes');
        Schema::dropIfExists('product_review_images');
        Schema::dropIfExists('product_reviews');
    }
};
