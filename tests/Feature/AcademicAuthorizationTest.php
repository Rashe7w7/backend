<?php

use App\Models\AlcanceUsuario;
use App\Models\AnioAcademico;
use App\Models\AsignacionPpa;
use App\Models\Carrera;
use App\Models\CursoAcademico;
use App\Models\Facultad;
use App\Models\Grupo;
use App\Models\User;
use App\Services\AcademicScopeService;

it('enforces indicator scopes for administrator PPA dean rector and secretary', function () {
    $faculty = Facultad::query()->create(['nombre' => 'Ciencias']);
    $career = Carrera::query()->create(['nombre' => 'Matemáticas', 'facultad_id' => $faculty->id, 'duracion_anios' => 4]);
    $level = AnioAcademico::query()->create(['nombre' => 'Primer año', 'nivel' => 1, 'carrera_id' => $career->id]);
    $course = CursoAcademico::query()->create(['nombre' => '2026-2027', 'fecha_inicio' => '2026-09-01', 'fecha_fin' => '2027-07-31']);
    $group = Grupo::query()->create(['nombre' => 'Grupo 1', 'anio_academico_id' => $level->id, 'curso_academico_id' => $course->id]);
    $service = app(AcademicScopeService::class);

    $admin = User::factory()->withoutTwoFactor()->create(['role' => 'administrador']);
    expect($service->canAccess($admin, 'facultad', $faculty->id))->toBeTrue();

    $ppa = User::factory()->withoutTwoFactor()->create(['role' => 'ppa']);
    AsignacionPpa::query()->create(['user_id' => $ppa->id, 'grupo_id' => $group->id, 'curso_academico_id' => $course->id, 'fecha_designacion' => now(), 'activo' => true]);
    expect($service->canAccess($ppa, 'grupo', $group->id))->toBeTrue()->and($service->canAccess($ppa, 'anno', $level->id))->toBeFalse();

    $dean = User::factory()->withoutTwoFactor()->create(['role' => 'decano']);
    AlcanceUsuario::query()->create(['user_id' => $dean->id, 'ambito_tipo' => 'facultad', 'ambito_id' => $faculty->id]);
    expect($service->canAccess($dean, 'carrera', $career->id))->toBeTrue()->and($service->canAccess($dean, 'anno', $level->id))->toBeTrue()->and($service->canAccess($dean, 'grupo', $group->id))->toBeFalse();

    $rector = User::factory()->withoutTwoFactor()->create(['role' => 'rector']);
    AlcanceUsuario::query()->create(['user_id' => $rector->id, 'ambito_tipo' => 'facultad', 'ambito_id' => $faculty->id]);
    expect($service->canAccess($rector, 'facultad', $faculty->id))->toBeTrue()->and($service->canAccess($rector, 'carrera', $career->id))->toBeFalse();

    $secretary = User::factory()->withoutTwoFactor()->create(['role' => 'secretaria']);
    AlcanceUsuario::query()->create(['user_id' => $secretary->id, 'ambito_tipo' => 'carrera', 'ambito_id' => $career->id, 'puede_exportar' => true]);
    expect($service->canAccess($secretary, 'carrera', $career->id))->toBeFalse()->and($service->canAccess($secretary, 'carrera', $career->id, true))->toBeTrue();
});
