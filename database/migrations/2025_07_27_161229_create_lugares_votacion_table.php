<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLugaresVotacionTable extends Migration
{
    public function up()
    {
        Schema::create('lugares_votacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('direccion')->nullable();
            $table->unsignedBigInteger('alcalde_id')->nullable();
            $table->unsignedBigInteger('concejal_id')->nullable();
            $table->timestamps();

            // Llaves foráneas (opcional, si existen las relaciones)
            $table->foreign('alcalde_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('concejal_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lugares_votacion');
    }
}
