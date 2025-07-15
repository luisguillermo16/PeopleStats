<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lideres', function (Blueprint $table) {
            // Eliminar claves foráneas de forma segura si existen
            try {
                DB::statement('ALTER TABLE lideres DROP FOREIGN KEY lideres_concejal_id_foreign');
            } catch (\Throwable $e) {
                // La clave no existe, ignorar
            }

            try {
                DB::statement('ALTER TABLE lideres DROP FOREIGN KEY lideres_alcalde_id_foreign');
            } catch (\Throwable $e) {
                // La clave no existe, ignorar
            }

            // Asegurar que las columnas existen antes de agregar las nuevas claves foráneas
            if (Schema::hasColumn('lideres', 'concejal_id')) {
                $table->foreign('concejal_id')->references('id')->on('users')->onDelete('set null');
            }

            if (Schema::hasColumn('lideres', 'alcalde_id')) {
                $table->foreign('alcalde_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lideres', function (Blueprint $table) {
            $table->dropForeign(['concejal_id']);
            $table->dropForeign(['alcalde_id']);
        });
    }
};
