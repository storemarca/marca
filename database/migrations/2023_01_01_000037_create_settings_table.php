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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->comment('مفتاح الإعداد');
            $table->string('group')->comment('مجموعة الإعدادات');
            $table->text('value')->nullable()->comment('القيمة (nullable)');
            $table->string('type')->default('string')->comment('نوع البيانات');

            $table->index('key', 'idx_settings_key');
            $table->index('group', 'idx_settings_group');
            $table->unique(['key', 'group'], 'uniq_key_group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
}; 