<?php

use App\Models\AlcanceUsuario;
use App\Models\Auditoria;
use App\Models\Carrera;
use App\Models\CategoriaIndicador;
use App\Models\CursoAcademico;
use App\Models\Facultad;
use App\Models\Indicador;
use App\Models\IndicadorCarrera;
use App\Models\User;

it('reuses a general indicator across entities without duplicating the definition', function () {
    $admin = User::factory()->withoutTwoFactor()->create(['role' => 'administrador']);
    $category = CategoriaIndicador::query()->create(['nombre' => 'Calidad']);
    $faculty = Facultad::query()->create(['nombre' => 'Ciencias']);
    $first = Carrera::query()->create(['nombre' => 'Matemáticas', 'facultad_id' => $faculty->id, 'duracion_anios' => 4]);
    $second = Carrera::query()->create(['nombre' => 'Física', 'facultad_id' => $faculty->id, 'duracion_anios' => 5]);
    $course = CursoAcademico::query()->create(['nombre' => '2026-2027', 'fecha_inicio' => '2026-09-01', 'fecha_fin' => '2027-07-31']);

    $this->actingAs($admin)->post(route('indicadores.store'), ['nombre' => 'Estudiantes con arrastre', 'categoria_indicador_id' => $category->id])->assertRedirect();
    $indicator = Indicador::query()->firstOrFail();
    foreach ([$first, $second] as $career) {
        $this->post(route('indicadores.apply'), ['indicador_id' => $indicator->id, 'especificacion' => 'carrera', 'entidad_id' => $career->id, 'curso_academico_id' => $course->id])->assertRedirect();
    }

    $this->assertDatabaseCount('indicador', 1);
    $this->assertDatabaseCount('indicador_carrera', 2);

    $secretary = User::factory()->withoutTwoFactor()->create(['role' => 'secretaria']);
    $application = IndicadorCarrera::query()->firstOrFail();
    $this->actingAs($secretary)->delete(route('indicadores.applications.destroy', ['carrera', $application->id]))->assertForbidden();
    $this->assertDatabaseHas('indicador_carrera', ['id' => $application->id]);
});

it('exports temporary values without persisting them and audits only export metadata', function () {
    $secretary = User::factory()->withoutTwoFactor()->create(['role' => 'secretaria']);
    $category = CategoriaIndicador::query()->create(['nombre' => 'Calidad']);
    $faculty = Facultad::query()->create(['nombre' => 'Ciencias']);
    $career = Carrera::query()->create(['nombre' => 'Matemáticas', 'facultad_id' => $faculty->id, 'duracion_anios' => 4]);
    $indicator = Indicador::query()->create(['nombre' => 'Matrícula', 'categoria_indicador_id' => $category->id]);
    AlcanceUsuario::query()->create(['user_id' => $secretary->id, 'ambito_tipo' => 'carrera', 'ambito_id' => $career->id, 'puede_exportar' => true]);

    $response = $this->actingAs($secretary)->post(route('indicadores.csv'), ['especificacion' => 'carrera', 'entidad_id' => $career->id, 'indicadores' => [$indicator->id], 'valores' => [$indicator->id => 100]]);
    $response->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
    $this->assertDatabaseCount('indicador_valor', 0);
    $audit = Auditoria::query()->where('accion', 'csv.generado')->firstOrFail();
    expect($audit->datos_nuevos)->not->toHaveKey('valores');

    $this->post(route('indicadores.store'), ['nombre' => 'No permitido', 'categoria_indicador_id' => $category->id])->assertForbidden();
});

it('keeps audit records immutable and visible only to administrators', function () {
    $record = Auditoria::query()->create(['accion' => 'prueba', 'entidad' => 'test', 'resultado' => 'exitoso']);
    expect($record->update(['resultado' => 'alterado']))->toBeFalse()->and($record->delete())->toBeFalse();

    $secretary = User::factory()->withoutTwoFactor()->create(['role' => 'secretaria']);
    $this->actingAs($secretary)->get(route('audit.index'))->assertForbidden();
    $admin = User::factory()->withoutTwoFactor()->create(['role' => 'administrador']);
    $this->actingAs($admin)->get(route('audit.index'))->assertOk();
});
