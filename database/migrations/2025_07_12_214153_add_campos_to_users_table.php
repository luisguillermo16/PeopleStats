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

            // Verifica si no existen antes de agregarlas
            if (!Schema::hasColumn('users', 'alcalde_id')) {
                $table->foreignId('alcalde_id')->nullable()->after('password')->constrained('users')->onDelete('cascade');
                $table->index('alcalde_id');
            }

            if (!Schema::hasColumn('users', 'concejal_id')) {
                $table->foreignId('concejal_id')->nullable()->after('alcalde_id')->constrained('users')->onDelete('cascade');
                $table->index('concejal_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Elimina claves foráneas solo si las columnas existen
            if (Schema::hasColumn('users', 'alcalde_id')) {
                $table->dropForeign(['alcalde_id']);
                $table->dropIndex(['alcalde_id']);
                $table->dropColumn('alcalde_id');
            }

            if (Schema::hasColumn('users', 'concejal_id')) {
                $table->dropForeign(['concejal_id']);
                $table->dropIndex(['concejal_id']);
                $table->dropColumn('concejal_id');
            }
        });
    }
};
