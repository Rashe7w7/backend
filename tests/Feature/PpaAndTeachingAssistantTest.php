<?php

use App\Models\Alumno;
use App\Models\AlumnoAyudante;
use App\Models\AnioAcademico;
use App\Models\AsignacionPpa;
use App\Models\Asignatura;
use App\Models\Carrera;
use App\Models\CategoriaIndicador;
use App\Models\CursoAcademico;
use App\Models\Disciplina;
use App\Models\Facultad;
use App\Models\Grupo;
use App\Models\Indicador;
use App\Models\PlanEstudio;
use App\Models\Profesor;
use App\Models\User;

beforeEach(function () {
    $faculty = Facultad::query()->create(['nombre' => 'Ciencias']);
    $career = Carrera::query()->create(['nombre' => 'Matemáticas', 'facultad_id' => $faculty->id, 'duracion_anios' => 4]);
    $this->level = AnioAcademico::query()->create(['nombre' => 'Primer año', 'nivel' => 1, 'carrera_id' => $career->id]);
    $this->course = CursoAcademico::query()->create(['nombre' => '2026-2027', 'fecha_inicio' => '2026-09-01', 'fecha_fin' => '2027-07-31']);
    $this->group = Grupo::query()->create(['nombre' => 'Grupo 1', 'anio_academico_id' => $this->level->id, 'curso_academico_id' => $this->course->id]);
    $this->otherGroup = Grupo::query()->create(['nombre' => 'Grupo 2', 'anio_academico_id' => $this->level->id, 'curso_academico_id' => $this->course->id]);
    $this->student = Alumno::query()->create(['nombre' => 'Ana Pérez', 'grupo_id' => $this->group->id]);
    $this->outsider = Alumno::query()->create(['nombre' => 'Otra Estudiante', 'grupo_id' => $this->otherGroup->id]);
    $plan = PlanEstudio::query()->create(['nombre' => 'Plan', 'carrera_id' => $career->id, 'fecha_inicio' => '2026-09-01']);
    $discipline = Disciplina::query()->create(['nombre' => 'Ciencias básicas', 'plan_estudio_id' => $plan->id]);
    $this->subject = Asignatura::query()->create(['nombre' => 'Álgebra', 'disciplina_id' => $discipline->id]);
    $this->otherSubject = Asignatura::query()->create(['nombre' => 'Geometría', 'disciplina_id' => $discipline->id]);
    $this->professor = Profesor::query()->create(['nombre' => 'Profesor Responsable']);
    $this->ppa = User::factory()->withoutTwoFactor()->create(['role' => 'ppa']);
    AsignacionPpa::query()->create(['user_id' => $this->ppa->id, 'grupo_id' => $this->group->id, 'curso_academico_id' => $this->course->id, 'fecha_designacion' => now(), 'activo' => true]);
});

it('allows a PPA to designate one active teaching assistant in an assigned group', function () {
    $payload = ['alumno_id' => $this->student->id, 'grupo_id' => $this->group->id, 'asignatura_id' => $this->subject->id, 'profesor_id' => $this->professor->id, 'curso_academico_id' => $this->course->id, 'fecha_designacion' => '2026-10-01', 'estado' => 'activo'];

    $this->actingAs($this->ppa)->postJson('/api/alumnoayudante', $payload)->assertCreated();
    $this->assertDatabaseHas('estudiante_ayudante', ['alumno_id' => $this->student->id, 'grupo_id' => $this->group->id, 'estado' => 'activo']);

    $this->postJson('/api/alumnoayudante', array_replace($payload, ['asignatura_id' => $this->otherSubject->id]))
        ->assertUnprocessable()->assertJsonValidationErrors('alumno_id');
    expect(AlumnoAyudante::query()->count())->toBe(1);
});

it('prevents a PPA from managing students or year indicators outside assigned groups', function () {
    $this->actingAs($this->ppa)->postJson('/api/alumnoayudante', ['alumno_id' => $this->outsider->id, 'grupo_id' => $this->otherGroup->id, 'asignatura_id' => $this->subject->id, 'profesor_id' => $this->professor->id, 'curso_academico_id' => $this->course->id, 'fecha_designacion' => '2026-10-01', 'estado' => 'activo'])->assertForbidden();

    $category = CategoriaIndicador::query()->create(['nombre' => 'Calidad']);
    $indicator = Indicador::query()->create(['nombre' => 'Promoción', 'categoria_indicador_id' => $category->id]);
    $this->post(route('indicadores.apply'), ['indicador_id' => $indicator->id, 'especificacion' => 'anno', 'entidad_id' => $this->level->id, 'curso_academico_id' => $this->course->id])->assertForbidden();
});
