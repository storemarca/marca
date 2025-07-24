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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('user_id')->nullable()->comment('nullable')->constrained()->onDelete('set null');
            $table->foreignId('country_id')->nullable()->comment('nullable')->constrained()->onDelete('set null');
            $table->string('order_number')->unique();
            $table->string('token', 64)->comment('رمز فريد للوصول إلى الطلب');
            $table->string('status');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 10)->default('SAR');
            $table->string('payment_method');
            $table->foreignId('shipping_method_id')->constrained();
            $table->foreignId('shipping_company_id')->nullable()->comment('nullable')->constrained()->onDelete('set null');
            $table->string('shipping_name');
            $table->string('shipping_phone');
            $table->string('shipping_email')->nullable()->comment('nullable');
            $table->string('shipping_address_line1');
            $table->string('shipping_address_line2');
            $table->string('shipping_city');
            $table->string('shipping_state');
            $table->string('shipping_postal_code');
            $table->string('shipping_country');
            $table->string('shipping_coordinates')->nullable()->comment('nullable');
            $table->string('coupon_code')->nullable();
            $table->text('notes')->nullable();
            $table->text('payment_notes')->nullable()->comment('nullable');
            $table->text('admin_notes')->nullable()->comment('nullable');
            $table->string('customer_email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}; 