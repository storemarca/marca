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
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique();
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->decimal('commission_rate', 5, 2)->default(10.00);
            $table->decimal('balance', 10, 2)->default(0.00);
            $table->decimal('lifetime_earnings', 10, 2)->default(0.00);
            $table->text('rejection_reason')->nullable();
            $table->json('payment_details')->nullable();
            $table->string('website')->nullable();
            $table->json('social_media')->nullable();
            $table->text('marketing_methods')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliates');
    }
};
