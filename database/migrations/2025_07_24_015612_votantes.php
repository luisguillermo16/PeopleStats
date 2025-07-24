<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class votantes extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('votantes', function (Blueprint $table) {
            $table->id();

            $table->string('nombre');
            $table->string('cedula')->unique(); // Puedes quitar el unique si varios usuarios pueden registrar la misma cédula
            $table->string('telefono')->nullable();
            $table->string('mesa')->nullable();
           
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lider_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('concejal_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('alcalde_id')->nullable()->constrained()->onDelete('set null');

           

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votantes');
    }
}
