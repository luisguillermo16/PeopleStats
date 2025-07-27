<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('votantes', function (Blueprint $table) {
            // Añadir columna si no existe
            if (!Schema::hasColumn('votantes', 'lugar_votacion_id')) {
                $table->unsignedBigInteger('lugar_votacion_id')->nullable()->after('donacion');
            }

            // Clave foránea correcta hacia la tabla lugares_votacion
            $table->foreign('lugar_votacion_id')
                ->references('id')
                ->on('lugares_votacion')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('votantes', function (Blueprint $table) {
            $table->dropForeign(['lugar_votacion_id']);
            $table->dropColumn('lugar_votacion_id');
        });
    }
};
