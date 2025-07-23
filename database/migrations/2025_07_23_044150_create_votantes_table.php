<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVotantesTable extends Migration
{
    public function up()
    {
        Schema::create('votantes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('cedula')->unique();
            $table->string('telefono');

            $table->unsignedBigInteger('user_id');     // Usuario que registra al votante
            $table->unsignedBigInteger('lider_id');    // User_id del líder
            $table->unsignedBigInteger('concejal_id')->nullable(); // User_id del concejal
            $table->unsignedBigInteger('alcalde_id')->nullable();  // User_id del alcalde

            $table->timestamps();

            // 🔗 Relaciones
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('lider_id')->references('user_id')->on('lideres')->onDelete('cascade');
           $table->foreign('concejal_id')->references('id')->on('concejales')->onDelete('cascade');
            $table->foreign('alcalde_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('votantes');
    }
}
