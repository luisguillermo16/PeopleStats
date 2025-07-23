<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'alcalde_id')) {
                $table->unsignedBigInteger('alcalde_id')->nullable()->after('id');
            }

            if (!Schema::hasColumn('users', 'concejal_id')) {
                $table->unsignedBigInteger('concejal_id')->nullable()->after('alcalde_id');
            }

            // Agrega otras columnas aquí si es necesario...
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'alcalde_id')) {
                $table->dropColumn('alcalde_id');
            }

            if (Schema::hasColumn('users', 'concejal_id')) {
                $table->dropColumn('concejal_id');
            }
        });
    }
};
