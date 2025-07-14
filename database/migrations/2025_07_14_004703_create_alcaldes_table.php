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
        Schema::create('alcaldes', function (Blueprint $table) {
    $table->id();

    // Relaciona al usuario que es el alcalde
    $table->foreignId('user_id')->constrained()->onDelete('cascade');

    // Datos políticos extra
    $table->string('partido_politico')->nullable();
    $table->string('numero_lista')->nullable();

    $table->boolean('activo')->default(true);

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alcaldes');
    }
};
