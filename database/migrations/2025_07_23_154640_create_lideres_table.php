<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lideres', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('concejal_id')->nullable(); // ← ID del user con rol concejal
            $table->unsignedBigInteger('alcalde_id')->nullable();  // ← ID del user con rol alcalde
            $table->timestamps();

            // Relaciones correctas
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('concejal_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('alcalde_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lideres');
    }
};
