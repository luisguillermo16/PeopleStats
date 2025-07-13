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
        Schema::table('users', function (Blueprint $table) {
            // Jerarquía: Alcalde -> Concejal -> Líder
            $table->foreignId('alcalde_id')->nullable()->after('password')->constrained('users')->onDelete('cascade');
            $table->foreignId('concejal_id')->nullable()->after('alcalde_id')->constrained('users')->onDelete('cascade');
            
            // Índices para optimización
            $table->index('alcalde_id');
            $table->index('concejal_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['alcalde_id']);
            $table->dropForeign(['concejal_id']);
            $table->dropIndex(['alcalde_id']);
            $table->dropIndex(['concejal_id']);
            $table->dropColumn(['alcalde_id', 'concejal_id']);
        });
    }
};