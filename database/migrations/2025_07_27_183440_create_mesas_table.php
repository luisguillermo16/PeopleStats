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
    Schema::create('mesas', function (Blueprint $table) {
        $table->id();
        $table->string('numero'); // número o nombre de la mesa
        $table->foreignId('lugar_votacion_id')
              ->constrained('lugares_votacion')
              ->onDelete('cascade');
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mesas');
    }
};
