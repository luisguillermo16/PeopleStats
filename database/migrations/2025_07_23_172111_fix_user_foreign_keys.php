<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar claves foráneas antiguas
            $table->dropForeign(['concejal_id']);
            $table->dropForeign(['alcalde_id']);

            // Agregar claves foráneas nuevas apuntando a la tabla users
            $table->foreign('concejal_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('alcalde_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revertir cambios si se hace rollback
            $table->dropForeign(['concejal_id']);
            $table->dropForeign(['alcalde_id']);

            $table->foreign('concejal_id')->references('id')->on('concejales')->onDelete('set null');
            $table->foreign('alcalde_id')->references('id')->on('alcaldes')->onDelete('set null');
        });
    }
};
