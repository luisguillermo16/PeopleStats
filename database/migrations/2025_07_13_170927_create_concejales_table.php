<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('concejales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('partido_politico')->nullable();
            $table->integer('numero_lista')->nullable();
            $table->text('propuestas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            // Clave foránea
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Índice único para evitar duplicados
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('concejales');
    }
};