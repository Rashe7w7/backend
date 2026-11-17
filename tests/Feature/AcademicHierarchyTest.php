<?php

use App\Models\Alumno;
use App\Models\AnioAcademico;
use App\Models\Carrera;
use App\Models\CursoAcademico;
use App\Models\Facultad;
use App\Models\Grupo;
use App\Models\User;

it('derives the career level faculty and institutional course from the student group', function () {
    $faculty = Facultad::query()->create(['nombre' => 'Ciencias']);
    $career = Carrera::query()->create(['nombre' => 'Matemáticas', 'facultad_id' => $faculty->id, 'duracion_anios' => 4]);
    $level = AnioAcademico::query()->create(['nombre' => 'Primer año', 'nivel' => 1, 'carrera_id' => $career->id]);
    $course = CursoAcademico::query()->create(['nombre' => '2026-2027', 'fecha_inicio' => '2026-09-01', 'fecha_fin' => '2027-07-31']);
    $group = Grupo::query()->create(['nombre' => 'Grupo 1', 'anio_academico_id' => $level->id, 'curso_academico_id' => $course->id]);
    $student = Alumno::query()->create(['nombre' => 'Ana Pérez', 'grupo_id' => $group->id]);

    $student->load('grupo.cursoAcademico', 'grupo.anioAcademico.carrera.facultad');

    expect($student->grupo->anioAcademico->nivel)->toBe(1)
        ->and($student->grupo->anioAcademico->carrera->is($career))->toBeTrue()
        ->and($student->grupo->anioAcademico->carrera->facultad->is($faculty))->toBeTrue()
        ->and($student->grupo->cursoAcademico->is($course))->toBeTrue()
        ->and($student->grupo->nombre_academico)->toContain('Matemáticas', '1.º año');
});

it('limits levels to the registered career duration and prevents duplicates', function () {
    $admin = User::factory()->withoutTwoFactor()->create(['role' => 'administrador']);
    $faculty = Facultad::query()->create(['nombre' => 'Ciencias']);
    $career = Carrera::query()->create(['nombre' => 'Matemáticas', 'facultad_id' => $faculty->id, 'duracion_anios' => 4]);

    $this->actingAs($admin)->postJson('/api/anios-academicos', ['nombre' => 'Quinto año', 'nivel' => 5, 'carrera_id' => $career->id])
        ->assertUnprocessable()->assertJsonValidationErrors('nivel');
    $this->postJson('/api/anios-academicos', ['nombre' => 'Primer año', 'nivel' => 1, 'carrera_id' => $career->id])->assertCreated();
    $this->postJson('/api/anios-academicos', ['nombre' => 'Nivel repetido', 'nivel' => 1, 'carrera_id' => $career->id])
        ->assertUnprocessable()->assertJsonValidationErrors('nivel');

    $otherCareer = Carrera::query()->create(['nombre' => 'Física', 'facultad_id' => $faculty->id, 'duracion_anios' => 5]);
    $this->postJson('/api/anios-academicos', ['nombre' => 'Primer año', 'nivel' => 1, 'carrera_id' => $otherCareer->id])->assertCreated();
});
