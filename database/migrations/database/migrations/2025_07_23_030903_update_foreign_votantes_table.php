<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToVotantesTable extends Migration
{
    public function up()
    {
        Schema::table('votantes', function (Blueprint $table) {
            // Asegúrate que la columna no exista ya
            $table->unsignedBigInteger('user_id')->nullable()->after('id');

            $table->foreign('user_id')->references('id')->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('votantes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
}
