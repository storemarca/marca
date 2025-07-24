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
        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->string('model_type');
            $table->bigInteger('model_id');
            //$table->bigInteger('team_id')->nullable()->comment('nullable if teams enabled');

            $table->index(['model_id', 'model_type']);
            $table->primary(['role_id', 'model_id', 'model_type', /*'team_id'*/], 'model_has_roles_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_has_roles');
    }
}; 