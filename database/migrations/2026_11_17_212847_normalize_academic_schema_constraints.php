<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->ensureNoDuplicates('departamento', ['facultad_id', 'nombre']);
        $this->ensureNoDuplicates('carrera', ['facultad_id', 'nombre']);
        $this->ensureNoDuplicates('plan_estudio', ['carrera_id', 'nombre']);
        $this->ensureNoDuplicates('disciplina', ['plan_estudio_id', 'nombre']);
        $this->ensureNoDuplicates('anio_academico', ['carrera_id', 'nivel']);
        $this->ensureNoDuplicates('grupo', ['anio_academico_id', 'nombre']);
        $this->ensureNoDuplicates('profesor_asignatura', ['profesor_id', 'asignatura_id', 'curso_academico_id']);
        $this->ensureNoDuplicates('estudiante_ayudante', ['alumno_id', 'profesor_asignatura_id']);

        $this->dropUniqueIfPresent('anio_academico', ['nombre']);
        $this->dropUniqueIfPresent('profesor_asignatura', ['profesor_id', 'asignatura_id']);

        $this->addUniqueIfMissing('departamento', ['facultad_id', 'nombre']);
        $this->addUniqueIfMissing('carrera', ['facultad_id', 'nombre']);
        $this->addUniqueIfMissing('plan_estudio', ['carrera_id', 'nombre']);
        $this->addUniqueIfMissing('disciplina', ['plan_estudio_id', 'nombre']);
        $this->addUniqueIfMissing('anio_academico', ['carrera_id', 'nivel']);
        $this->addUniqueIfMissing('grupo', ['anio_academico_id', 'nombre']);
        $this->addUniqueIfMissing('profesor_asignatura', ['profesor_id', 'asignatura_id', 'curso_academico_id']);
        $this->addUniqueIfMissing('estudiante_ayudante', ['alumno_id', 'profesor_asignatura_id']);
    }

    public function down(): void
    {
        $this->dropUniqueIfPresent('estudiante_ayudante', ['alumno_id', 'profesor_asignatura_id']);
        $this->dropUniqueIfPresent('profesor_asignatura', ['profesor_id', 'asignatura_id', 'curso_academico_id']);
        $this->dropUniqueIfPresent('grupo', ['anio_academico_id', 'nombre']);
        $this->dropUniqueIfPresent('anio_academico', ['carrera_id', 'nivel']);
        $this->dropUniqueIfPresent('disciplina', ['plan_estudio_id', 'nombre']);
        $this->dropUniqueIfPresent('plan_estudio', ['carrera_id', 'nombre']);
        $this->dropUniqueIfPresent('carrera', ['facultad_id', 'nombre']);
        $this->dropUniqueIfPresent('departamento', ['facultad_id', 'nombre']);

        $this->addUniqueIfMissing('anio_academico', ['nombre']);
        $this->addUniqueIfMissing('profesor_asignatura', ['profesor_id', 'asignatura_id']);
    }

    /**
     * @param  list<string>  $columns
     */
    private function ensureNoDuplicates(string $table, array $columns): void
    {
        $hasDuplicates = DB::table($table)
            ->select($columns)
            ->groupBy($columns)
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($hasDuplicates) {
            throw new RuntimeException(sprintf(
                'No se puede normalizar %s: existen duplicados para las columnas %s.',
                $table,
                implode(', ', $columns),
            ));
        }
    }

    /**
     * @param  list<string>  $columns
     */
    private function addUniqueIfMissing(string $table, array $columns): void
    {
        if (! Schema::hasIndex($table, $columns, 'unique')) {
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->unique($columns));
        }
    }

    /**
     * @param  list<string>  $columns
     */
    private function dropUniqueIfPresent(string $table, array $columns): void
    {
        if (Schema::hasIndex($table, $columns, 'unique')) {
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropUnique($columns));
        }
    }
};
