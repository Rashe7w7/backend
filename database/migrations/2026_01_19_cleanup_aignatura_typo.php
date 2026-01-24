<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Eliminar la columna con typo si existe
        if (Schema::hasColumn('anio_asignatura', 'aignatura_id')) {
            Schema::table('anio_asignatura', function (Blueprint $table) {
                $table->dropColumn('aignatura_id');
            });
        }
    }

    public function down(): void
    {
        // No es necesario revertir
    }
};
