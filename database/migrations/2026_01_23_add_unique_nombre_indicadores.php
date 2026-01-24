<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agregar restricción UNIQUE en el nombre de los indicadores
     * para evitar duplicados.
     */
    public function up(): void
    {
        Schema::table('indicador', function (Blueprint $table) {
            // Agregar índice unique si no existe
            $table->unique(['nombre']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indicador', function (Blueprint $table) {
            $table->dropUnique(['nombre']);
        });
    }
};
