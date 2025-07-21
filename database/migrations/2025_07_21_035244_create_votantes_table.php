<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('votantes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('cedula')->unique();
            $table->string('telefono')->nullable();
            $table->string('mesa')->nullable();
            $table->string('donacion')->nullable();
            $table->foreignId('lider_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('concejal_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('alcalde_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('tambien_vota_alcalde')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votantes');
    }
};
