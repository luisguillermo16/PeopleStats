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
        // Índices para la tabla votantes
        Schema::table('votantes', function (Blueprint $table) {
            // Índices compuestos para consultas frecuentes
            $table->index(['lider_id', 'created_at'], 'idx_votantes_lider_created');
            $table->index(['lider_id', 'barrio_id'], 'idx_votantes_lider_barrio');
            $table->index(['lider_id', 'lugar_votacion_id'], 'idx_votantes_lider_lugar');
            $table->index(['cedula', 'lider_id'], 'idx_votantes_cedula_lider');
            
            // Índices simples para campos de búsqueda
            $table->index('nombre');
            $table->index('telefono');
            $table->index('mesa');
        });

        // Índices para la tabla users
        Schema::table('users', function (Blueprint $table) {
            $table->index('email');
            $table->index(['alcalde_id', 'concejal_id'], 'idx_users_alcalde_concejal');
        });

        // Índices para la tabla barrios
        Schema::table('barrios', function (Blueprint $table) {
            $table->index('alcalde_id');
            $table->index('nombre');
        });

        // Índices para la tabla lugares_votacion
        Schema::table('lugares_votacion', function (Blueprint $table) {
            $table->index('alcalde_id');
            $table->index('concejal_id');
            $table->index('nombre');
        });

        // Índices para la tabla mesas
        Schema::table('mesas', function (Blueprint $table) {
            $table->index('lugar_votacion_id');
            $table->index('numero');
        });

        // Índices para la tabla sessions (si existe)
        if (Schema::hasTable('sessions')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->index('user_id');
                $table->index('last_activity');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover índices de votantes
        Schema::table('votantes', function (Blueprint $table) {
            $table->dropIndex('idx_votantes_lider_created');
            $table->dropIndex('idx_votantes_lider_barrio');
            $table->dropIndex('idx_votantes_lider_lugar');
            $table->dropIndex('idx_votantes_cedula_lider');
            $table->dropIndex(['nombre']);
            $table->dropIndex(['telefono']);
            $table->dropIndex(['mesa']);
        });

        // Remover índices de users
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex('idx_users_alcalde_concejal');
        });

        // Remover índices de barrios
        Schema::table('barrios', function (Blueprint $table) {
            $table->dropIndex(['alcalde_id']);
            $table->dropIndex(['nombre']);
        });

        // Remover índices de lugares_votacion
        Schema::table('lugares_votacion', function (Blueprint $table) {
            $table->dropIndex(['alcalde_id']);
            $table->dropIndex(['concejal_id']);
            $table->dropIndex(['nombre']);
        });

        // Remover índices de mesas
        Schema::table('mesas', function (Blueprint $table) {
            $table->dropIndex(['lugar_votacion_id']);
            $table->dropIndex(['numero']);
        });

        // Remover índices de sessions
        if (Schema::hasTable('sessions')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->dropIndex(['user_id']);
                $table->dropIndex(['last_activity']);
            });
        }
    }
};
