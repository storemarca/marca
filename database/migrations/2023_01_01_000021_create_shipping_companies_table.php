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
        Schema::create('shipping_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->comment('Unique code for the shipping company');
            $table->text('description');
            $table->string('website');
            $table->string('tracking_url_template')->comment('URL template with {tracking_number} placeholder');
            $table->string('logo')->comment('URL to logo image');
            $table->string('contact_person');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->boolean('has_api_integration')->default(false);
            $table->json('api_credentials');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_companies');
    }
}; 