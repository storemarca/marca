<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // إضافة العمود الجديد
        Schema::table('categories', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('order');
        });
        
        // نسخ قيم عمود order إلى عمود sort_order
        DB::statement('UPDATE categories SET sort_order = `order`');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
