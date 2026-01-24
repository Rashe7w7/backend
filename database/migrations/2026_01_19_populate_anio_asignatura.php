<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Marcar la migración anterior como ejecutada en la base de datos si no lo está
        $exists = DB::table('migrations')
            ->where('migration', '2026_01_19_create_anio_asignatura_table')
            ->exists();

        if (!$exists) {
            DB::table('migrations')->insert([
                'migration' => '2026_01_19_create_anio_asignatura_table',
                'batch' => DB::table('migrations')->max('batch') + 1,
            ]);
        }

        // Poblar la tabla anio_asignatura con datos
        // Obtener años académicos y asignaturas
        $aniosAcademicos = DB::table('anio_academico')->get();
        $asignaturas = DB::table('asignatura')->get();

        if ($aniosAcademicos->count() > 0 && $asignaturas->count() > 0) {
            foreach ($aniosAcademicos as $anio) {
                foreach ($asignaturas as $asignatura) {
                    // Verificar si la asociación ya existe
                    $exists = DB::table('anio_asignatura')
                        ->where('anio_academico_id', $anio->id)
                        ->where('asignatura_id', $asignatura->id)
                        ->exists();

                    if (!$exists) {
                        DB::table('anio_asignatura')->insert([
                            'anio_academico_id' => $anio->id,
                            'asignatura_id' => $asignatura->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        // Limpiar datos pero no eliminar la tabla
        DB::table('anio_asignatura')->truncate();
    }
};
