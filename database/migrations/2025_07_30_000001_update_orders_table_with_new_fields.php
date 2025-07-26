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
        Schema::table('orders', function (Blueprint $table) {
            // تعديل حقل status ليشمل القيم المطلوبة
            $table->dropColumn('status');
            $table->enum('status', [
                'new', 'opened', 'incomplete', 'completed', 
                'shipped', 'cancelled', 'refunded', 'failed'
            ])->after('token')->default('new');

            // إضافة حقل opened_at
            $table->timestamp('opened_at')->nullable()->after('paid_at')
                ->comment('وقت فتح الطلب بواسطة المساعد');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('opened_at');
            $table->dropColumn('status');
            $table->string('status')->after('token');
        });
    }
};
