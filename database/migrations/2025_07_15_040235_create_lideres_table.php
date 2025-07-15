<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lideres', function (Blueprint $table) {
            $table->id();

            // Usuario asociado al rol líder
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique('user_id');

            // Concejal que lo crea
            $table->unsignedBigInteger('concejal_id');
            $table->foreign('concejal_id')->references('id')->on('concejales')->onDelete('cascade');

            // Alcalde al que pertenece el concejal
            $table->unsignedBigInteger('alcalde_id')->nullable();
            $table->foreign('alcalde_id')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();

            // Índices útiles para filtros
            $table->index(['concejal_id']);
            $table->index(['alcalde_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lideres');
    }
};
