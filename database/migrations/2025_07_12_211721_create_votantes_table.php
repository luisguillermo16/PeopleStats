<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('votantes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('cedula');
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->text('direccion')->nullable();
            
            // Relaciones
            $table->foreignId('alcalde_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('concejal_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('registrado_por')->constrained('users')->onDelete('cascade');
            
            $table->timestamps();
            
            // Índices únicos para validaciones
            $table->unique(['cedula', 'alcalde_id'], 'unique_votante_alcalde');
            $table->unique(['cedula', 'concejal_id'], 'unique_votante_concejal');
            
            // Índices para optimización
            $table->index(['alcalde_id', 'concejal_id']);
            $table->index('registrado_por');
        });
    }

    public function down()
    {
        Schema::dropIfExists('votantes');
    }
};