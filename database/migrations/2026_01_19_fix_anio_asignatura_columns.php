<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Verificar si falta la columna asignatura_id
        if (!Schema::hasColumn('anio_asignatura', 'asignatura_id')) {
            Schema::table('anio_asignatura', function (Blueprint $table) {
                $table->foreignId('asignatura_id')->constrained('asignatura')->cascadeOnDelete();
            });
        }

        // Verificar si falta la columna anio_academico_id
        if (!Schema::hasColumn('anio_asignatura', 'anio_academico_id')) {
            Schema::table('anio_asignatura', function (Blueprint $table) {
                $table->foreignId('anio_academico_id')->constrained('anio_academico')->cascadeOnDelete();
            });
        }

        // Verificar y agregar timestamps si faltan
        if (!Schema::hasColumn('anio_asignatura', 'created_at')) {
            Schema::table('anio_asignatura', function (Blueprint $table) {
                $table->timestamps();
            });
        }

        // Agregar índice único si no existe
        $indexExists = DB::select("
            SELECT 1 FROM pg_indexes 
            WHERE tablename = 'anio_asignatura' 
            AND indexname = 'anio_asignatura_anio_academico_id_asignatura_id_unique'
        ");

        if (empty($indexExists)) {
            Schema::table('anio_asignatura', function (Blueprint $table) {
                $table->unique(['anio_academico_id', 'asignatura_id']);
            });
        }
    }

    public function down(): void
    {
        // No eliminar columnas en down
    }
};
