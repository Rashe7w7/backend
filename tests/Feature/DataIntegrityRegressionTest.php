<?php

use App\Models\Alumno;
use App\Models\CategoriaIndicador;
use App\Models\Indicador;
use App\Models\User;

test('indicator creation stores its specification without entity assignments', function () {
    $this->actingAs(User::factory()->withoutTwoFactor()->create(['role' => 'administrador']));
    $category = CategoriaIndicador::create(['nombre' => 'Calidad']);

    $this->postJson('/api/indicadores', [
        'nombre' => 'Indicador general',
        'categoria_indicador_id' => $category->id,
        'especificacion' => 'carrera',
    ])->assertCreated()
        ->assertJsonPath('nombre', 'Indicador general')
        ->assertJsonPath('especificacion', 'carrera');

    $this->assertDatabaseHas('indicador', [
        'nombre' => 'Indicador general',
        'categoria_indicador_id' => $category->id,
        'especificacion' => 'carrera',
    ]);
    $this->assertDatabaseCount('indicador_carrera', 0);
});

test('indicator creation rejects duplicate names and invalid specifications', function () {
    $this->actingAs(User::factory()->withoutTwoFactor()->create(['role' => 'administrador']));
    $category = CategoriaIndicador::create(['nombre' => 'Calidad']);
    Indicador::create([
        'nombre' => 'Indicador existente',
        'categoria_indicador_id' => $category->id,
        'especificacion' => 'facultad',
    ]);

    $this->postJson('/api/indicadores', [
        'nombre' => 'Indicador existente',
        'categoria_indicador_id' => $category->id,
        'especificacion' => 'general',
    ])->assertUnprocessable()->assertJsonValidationErrors(['nombre', 'especificacion']);
});

test('student pagination rejects unsafe page sizes', function ($pageSize) {
    $this->actingAs(User::factory()->withoutTwoFactor()->create(['role' => 'administrador']));

    $this->getJson('/api/alumnos?per_page='.urlencode((string) $pageSize))
        ->assertUnprocessable()->assertJsonValidationErrors('per_page');
})->with([-1, 0, 101, 'invalid']);

test('student pagination preserves filters in its navigation links', function () {
    $this->actingAs(User::factory()->withoutTwoFactor()->create(['role' => 'consulta']));

    $response = $this->getJson('/api/alumnos?per_page=10&search=Ana')->assertOk();

    expect($response->json('per_page'))->toBe(10);
    expect($response->json('first_page_url'))->toContain('search=Ana', 'per_page=10');
});

test('student search is case insensitive across database engines', function () {
    $this->actingAs(User::factory()->withoutTwoFactor()->create(['role' => 'consulta']));

    Alumno::query()->create([
        'nombre' => 'Ángela Pérez',
        'identificador_institucional' => 'EST-POSTGRES-001',
    ]);

    $response = $this->getJson('/api/alumnos?search=pérez')->assertOk();

    expect($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.nombre'))->toBe('Ángela Pérez');
});
