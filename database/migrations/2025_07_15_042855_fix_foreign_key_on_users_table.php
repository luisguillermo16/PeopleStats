<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar la clave foránea anterior si existe
            $table->dropForeign(['concejal_id']);

            // Crear nueva clave foránea apuntando a concejales.id
            $table->foreign('concejal_id')->references('id')->on('concejales')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['concejal_id']);

            // (Opcional) Restaurar la relación anterior si fuera necesario
            // $table->foreign('concejal_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
