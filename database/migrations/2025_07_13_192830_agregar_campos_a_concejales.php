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
        Schema::table('concejales', function (Blueprint $table) {
            // Agregar la relación con alcalde
            $table->foreignId('alcalde_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Agregar índices para optimizar consultas
            $table->index(['alcalde_id']);
            $table->index(['user_id', 'alcalde_id']);
            $table->index(['activo', 'alcalde_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('concejales', function (Blueprint $table) {
            $table->dropForeign(['alcalde_id']);
            $table->dropColumn('alcalde_id');
        });
    }
};