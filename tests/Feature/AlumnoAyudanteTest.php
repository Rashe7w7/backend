<?php

use App\Models\Alumno;
use App\Models\AlumnoAyudante;
use App\Models\AnioAcademico;
use App\Models\Asignatura;
use App\Models\Carrera;
use App\Models\CursoAcademico;
use App\Models\Disciplina;
use App\Models\Facultad;
use App\Models\Grupo;
use App\Models\PlanEstudio;
use App\Models\Profesor;
use App\Models\ProfesorAsignatura;
use App\Models\User;

beforeEach(function () {
    $this->facultad = Facultad::create(['nombre' => 'Facultad de Ciencias']);
    $this->carrera = Carrera::create([
        'nombre' => 'Ingeniería de Pruebas',
        'facultad_id' => $this->facultad->id,
    ]);
    $this->anio = AnioAcademico::create([
        'nombre' => 'Primer año de pruebas',
        'nivel' => 1,
        'carrera_id' => $this->carrera->id,
    ]);
    $this->grupo = Grupo::create([
        'nombre' => 'Grupo A',
        'anio_academico_id' => $this->anio->id,
    ]);
    $this->alumnos = collect(range(1, 4))->map(fn (int $numero): Alumno => Alumno::create([
        'nombre' => "Estudiante {$numero}",
        'email' => "estudiante{$numero}@example.test",
        'grupo_id' => $this->grupo->id,
    ]));
    $this->plan = PlanEstudio::create([
        'nombre' => 'Plan de pruebas',
        'carrera_id' => $this->carrera->id,
        'fecha_inicio' => '2026-09-01',
        'vigente' => true,
    ]);
    $this->disciplina = Disciplina::create([
        'nombre' => 'Disciplina de pruebas',
        'plan_estudio_id' => $this->plan->id,
    ]);
    $this->asignatura = Asignatura::create([
        'nombre' => 'Programación de pruebas',
        'disciplina_id' => $this->disciplina->id,
    ]);
    $this->profesor = Profesor::create([
        'nombre' => 'Profesor de pruebas',
        'email' => 'profesor@example.test',
        'cedula' => 'P-001',
    ]);
    $this->curso = CursoAcademico::create([
        'nombre' => '2026-2027',
        'fecha_inicio' => '2026-09-01',
        'fecha_fin' => '2027-07-31',
        'activo' => true,
    ]);
    $this->profesorAsignatura = ProfesorAsignatura::create([
        'profesor_id' => $this->profesor->id,
        'asignatura_id' => $this->asignatura->id,
        'curso_academico_id' => $this->curso->id,
    ]);
    $this->administrador = User::factory()->withoutTwoFactor()->create(['role' => 'administrador']);
});

test('an administrator can register a student helper in a specific subject assignment', function () {
    $this->actingAs($this->administrador)
        ->post(route('alumnoayudante.store'), [
            'alumno_id' => $this->alumnos->first()->id,
            'profesor_asignatura_id' => $this->profesorAsignatura->id,
        ])
        ->assertRedirect(route('alumnoayudante'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('estudiante_ayudante', [
        'alumno_id' => $this->alumnos->first()->id,
        'profesor_asignatura_id' => $this->profesorAsignatura->id,
        'curso_academico_id' => $this->curso->id,
    ]);
});

test('the same subject assignment cannot be registered twice for a student', function () {
    AlumnoAyudante::create([
        'alumno_id' => $this->alumnos->first()->id,
        'profesor_asignatura_id' => $this->profesorAsignatura->id,
        'curso_academico_id' => $this->curso->id,
    ]);

    $this->actingAs($this->administrador)
        ->from(route('alumnoayudante'))
        ->post(route('alumnoayudante.store'), [
            'alumno_id' => $this->alumnos->first()->id,
            'profesor_asignatura_id' => $this->profesorAsignatura->id,
        ])
        ->assertRedirect(route('alumnoayudante'))
        ->assertSessionHasErrors('profesor_asignatura_id');

    expect(AlumnoAyudante::query()->count())->toBe(1);
});

test('reports show unique helper totals and percentages by career faculty and academic year', function () {
    $segundaAsignatura = Asignatura::create([
        'nombre' => 'Bases de datos de pruebas',
        'disciplina_id' => $this->disciplina->id,
    ]);
    $segundaAsignacion = ProfesorAsignatura::create([
        'profesor_id' => $this->profesor->id,
        'asignatura_id' => $segundaAsignatura->id,
        'curso_academico_id' => $this->curso->id,
    ]);

    foreach ([$this->profesorAsignatura, $segundaAsignacion] as $asignacion) {
        AlumnoAyudante::create([
            'alumno_id' => $this->alumnos->first()->id,
            'profesor_asignatura_id' => $asignacion->id,
            'curso_academico_id' => $this->curso->id,
        ]);
    }

    $this->actingAs($this->administrador)
        ->get(route('alumnoayudante'))
        ->assertOk()
        ->assertSee('Ingeniería de Pruebas')
        ->assertSee('Facultad de Ciencias')
        ->assertSee('Primer año de pruebas')
        ->assertSee('25,00%')
        ->assertViewHas('reportesCarrera', fn ($reporte): bool => $reporte->first()['total'] === 1)
        ->assertViewHas('reportesFacultad', fn ($reporte): bool => $reporte->first()['total'] === 1)
        ->assertViewHas('reportesAnio', fn ($reporte): bool => $reporte->first()['total'] === 1);
});

test('users outside PPA and administration cannot consult or register helpers', function () {
    $decano = User::factory()->withoutTwoFactor()->create(['role' => 'decano']);

    $this->actingAs($decano)->get(route('alumnoayudante'))->assertForbidden();
    $this->actingAs($decano)
        ->post(route('alumnoayudante.store'), [
            'alumno_id' => $this->alumnos->first()->id,
            'profesor_asignatura_id' => $this->profesorAsignatura->id,
        ])
        ->assertForbidden();

    $this->assertDatabaseCount('estudiante_ayudante', 0);
});

test('a PPA user can register a designation for a specific subject assignment', function () {
    $ppa = User::factory()->withoutTwoFactor()->create(['role' => 'ppa']);

    $this->actingAs($ppa)
        ->post(route('alumnoayudante.store'), [
            'alumno_id' => $this->alumnos->first()->id,
            'profesor_asignatura_id' => $this->profesorAsignatura->id,
        ])
        ->assertRedirect(route('alumnoayudante'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('estudiante_ayudante', [
        'alumno_id' => $this->alumnos->first()->id,
        'profesor_asignatura_id' => $this->profesorAsignatura->id,
    ]);
});

test('the page offers an autocomplete student selector with comfortable spacing', function () {
    $this->actingAs($this->administrador)
        ->get(route('alumnoayudante'))
        ->assertOk()
        ->assertSee('list="alumnos_disponibles"', false)
        ->assertSee('class="px-4"', false)
        ->assertSee('Escriba un nombre, correo o carrera');
});

test('an administrator can delete a student helper designation', function () {
    $designacion = AlumnoAyudante::create([
        'alumno_id' => $this->alumnos->first()->id,
        'profesor_asignatura_id' => $this->profesorAsignatura->id,
        'curso_academico_id' => $this->curso->id,
    ]);

    $this->actingAs($this->administrador)
        ->delete(route('alumnoayudante.destroy', $designacion->id))
        ->assertRedirect(route('alumnoayudante'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('estudiante_ayudante', ['id' => $designacion->id]);
});

test('a PPA user cannot delete a student helper designation', function () {
    $designacion = AlumnoAyudante::create([
        'alumno_id' => $this->alumnos->first()->id,
        'profesor_asignatura_id' => $this->profesorAsignatura->id,
        'curso_academico_id' => $this->curso->id,
    ]);
    $ppa = User::factory()->withoutTwoFactor()->create(['role' => 'ppa']);

    $this->actingAs($ppa)
        ->delete(route('alumnoayudante.destroy', $designacion->id))
        ->assertForbidden();

    $this->assertDatabaseHas('estudiante_ayudante', ['id' => $designacion->id]);
});
