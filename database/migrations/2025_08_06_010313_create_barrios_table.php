<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('barrios', function (Blueprint $table) {
        $table->id();
        $table->string('nombre')->unique();
        $table->unsignedBigInteger('alcalde_id'); // el alcalde que lo creó
        $table->timestamps();

        $table->foreign('alcalde_id')->references('id')->on('users')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barrios');
    }
};
