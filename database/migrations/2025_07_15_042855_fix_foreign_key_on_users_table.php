<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;



return new class extends Migration {
    public function up(): void
    {
        // Intenta eliminar la clave solo si existe
        try {
         
        } catch (\Exception $e) {
            // Solo informa en consola si falla
            info("No se pudo eliminar la foreign key users_concejal_id_foreign: " . $e->getMessage());
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('concejal_id')->nullable()->change();
            $table->foreign('concejal_id')->references('id')->on('concejales')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['concejal_id']);
        });
    }
};
