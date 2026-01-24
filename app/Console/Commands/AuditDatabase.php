<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Alumno;

class AuditDatabase extends Command
{
    protected $signature = 'db:audit';
    protected $description = 'Auditoría completa de integridad de base de datos';

    public function handle()
    {
        $this->info("=== AUDITORIA DE BASE DE DATOS ===\n");

        // 1. Alumnos con grupos inválidos
        $this->info("1. Alumnos con grupos no válidos:");
        $alumnosInvalidos = Alumno::whereNotExists(function($q) {
            $q->select(DB::raw(1))->from('grupos')->whereColumn('grupos.id', 'alumnos.grupo_id');
        })->count();
        $this->line("   Encontrados: $alumnosInvalidos");

        // 2. Grupos con años inválidos
        $this->info("\n2. Grupos con años académicos no válidos:");
        $gruposInvalidos = DB::table('grupos')->whereNotExists(function($q) {
            $q->select(DB::raw(1))->from('anios_academicos')->whereColumn('anios_academicos.id', 'grupos.anio_academico_id');
        })->count();
        $this->line("   Encontrados: $gruposInvalidos");

        // 3. Años con carreras inválidas
        $this->info("\n3. Años académicos con carreras no válidas:");
        $aniosInvalidos = DB::table('anios_academicos')->whereNotExists(function($q) {
            $q->select(DB::raw(1))->from('carreras')->whereColumn('carreras.id', 'anios_academicos.carrera_id');
        })->count();
        $this->line("   Encontrados: $aniosInvalidos");

        // 4. Asignaturas con disciplinas inválidas
        $this->info("\n4. Asignaturas con disciplinas no válidas:");
        $asignaturasInvalidas = DB::table('asignaturas')->whereNotExists(function($q) {
            $q->select(DB::raw(1))->from('disciplinas')->whereColumn('disciplinas.id', 'asignaturas.disciplina_id');
        })->count();
        $this->line("   Encontrados: $asignaturasInvalidas");

        // 5. Profesor_asignatura inválidos
        $this->info("\n5. Relaciones profesor_asignatura con referencias inválidas:");
        $profInvalidos = DB::table('profesor_asignatura')->whereNotExists(function($q) {
            $q->select(DB::raw(1))->from('profesores')->whereColumn('profesores.id', 'profesor_asignatura.profesor_id');
        })->count();
        $this->line("   Profesores inválidos: $profInvalidos");

        $asignInvalidos = DB::table('profesor_asignatura')->whereNotExists(function($q) {
            $q->select(DB::raw(1))->from('asignaturas')->whereColumn('asignaturas.id', 'profesor_asignatura.asignatura_id');
        })->count();
        $this->line("   Asignaturas inválidas: $asignInvalidos");

        // 6. Alumno_ayudante inválidos
        $this->info("\n6. Alumnos ayudantes con referencias inválidas:");
        $aaAlumnosInvalidos = DB::table('alumno_ayudante')->whereNotExists(function($q) {
            $q->select(DB::raw(1))->from('alumnos')->whereColumn('alumnos.id', 'alumno_ayudante.alumno_id');
        })->count();
        $this->line("   Alumnos inválidos: $aaAlumnosInvalidos");

        $aaAsignInvalidos = DB::table('alumno_ayudante')->whereNotExists(function($q) {
            $q->select(DB::raw(1))->from('asignaturas')->whereColumn('asignaturas.id', 'alumno_ayudante.asignatura_id');
        })->count();
        $this->line("   Asignaturas inválidas: $aaAsignInvalidos");

        // 7. Estadísticas
        $this->info("\n7. Estadísticas generales:");
        $this->line("   Facultades: " . DB::table('facultades')->count());
        $this->line("   Carreras: " . DB::table('carreras')->count());
        $this->line("   Años académicos: " . DB::table('anios_academicos')->count());
        $this->line("   Grupos: " . DB::table('grupos')->count());
        $this->line("   Alumnos: " . DB::table('alumnos')->count());
        $this->line("   Profesores: " . DB::table('profesores')->count());
        $this->line("   Asignaturas: " . DB::table('asignaturas')->count());
        $this->line("   Profesor-Asignatura: " . DB::table('profesor_asignatura')->count());
        $this->line("   Alumno Ayudante: " . DB::table('alumno_ayudante')->count());
        $this->line("   Planes de Estudio: " . DB::table('planes_estudio')->count());

        // 8. Planes inválidos
        $this->info("\n8. Planes de estudio con carreras inválidas:");
        $planesInvalidos = DB::table('planes_estudio')->whereNotExists(function($q) {
            $q->select(DB::raw(1))->from('carreras')->whereColumn('carreras.id', 'planes_estudio.carrera_id');
        })->count();
        $this->line("   Encontrados: $planesInvalidos");

        // 9. Verificar disciplinas
        $this->info("\n9. Disciplinas con planes de estudio inválidos:");
        $disciplinasInvalidas = DB::table('disciplinas')->whereNotExists(function($q) {
            $q->select(DB::raw(1))->from('planes_estudio')->whereColumn('planes_estudio.id', 'disciplinas.plan_estudio_id');
        })->count();
        $this->line("   Encontrados: $disciplinasInvalidas");

        $this->info("\n=== FIN AUDITORIA ===\n");
    }
}
