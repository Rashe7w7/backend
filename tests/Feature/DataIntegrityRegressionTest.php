<?php

use App\Models\Alumno;
use App\Models\CategoriaIndicador;
use App\Models\Facultad;
use App\Models\Indicador;
use App\Models\User;
use App\Services\IndicadorService;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

test('indicator creation rolls back its parent when the assignment fails', function () {
    $category = CategoriaIndicador::create(['nombre' => 'Calidad']);

    expect(fn () => app(IndicadorService::class)->createAndAssign([
        'nombre' => 'Indicador transaccional',
        'categoria_indicador' => $category->id,
        'especificacion' => 'facultad',
        'facultad_id' => 999999,
    ]))->toThrow(QueryException::class);

    $this->assertDatabaseMissing('indicador', ['nombre' => 'Indicador transaccional']);
    $this->assertDatabaseCount('indicador_facultad', 0);
});

test('duplicate assignments do not create additional indicator rows', function () {
    $category = CategoriaIndicador::create(['nombre' => 'Calidad']);
    $faculty = Facultad::create(['nombre' => 'Ciencias']);
    $data = [
        'nombre' => 'Indicador compartido',
        'categoria_indicador' => $category->id,
        'especificacion' => 'facultad',
        'facultad_id' => $faculty->id,
    ];

    app(IndicadorService::class)->createAndAssign($data);

    expect(fn () => app(IndicadorService::class)->createAndAssign($data))
        ->toThrow(ValidationException::class);

    $this->assertDatabaseCount('indicador', 1);
    $this->assertDatabaseCount('indicador_facultad', 1);
});

test('one indicator can be assigned to multiple faculties without duplicating its parent', function () {
    $category = CategoriaIndicador::create(['nombre' => 'Calidad']);
    $first = Facultad::create(['nombre' => 'Ciencias']);
    $second = Facultad::create(['nombre' => 'Ingeniería']);
    $data = [
        'nombre' => 'Indicador compartido',
        'categoria_indicador' => $category->id,
        'especificacion' => 'facultad',
        'facultad_id' => $first->id,
    ];

    $indicator = app(IndicadorService::class)->createAndAssign($data);
    $other = app(IndicadorService::class)->createAndAssign(array_replace($data, ['facultad_id' => $second->id]));

    expect($other->id)->toBe($indicator->id);
    $this->assertDatabaseCount('indicador', 1);
    $this->assertDatabaseCount('indicador_facultad', 2);
});

test('assignment validation rejects missing academic courses before creating an indicator', function () {
    $this->actingAs(User::factory()->withoutTwoFactor()->create(['role' => 'administrador']));
    $category = CategoriaIndicador::create(['nombre' => 'Calidad']);

    $this->postJson('/api/indicadores/create-and-assign', [
        'nombre' => 'Indicador sin curso',
        'categoria_indicador' => $category->id,
        'especificacion' => 'carrera',
        'carrera_id' => 999999,
    ])->assertUnprocessable()->assertJsonValidationErrors(['curso_academico_id', 'carrera_id']);

    expect(Indicador::where('nombre', 'Indicador sin curso')->exists())->toBeFalse();
});

test('an existing indicator cannot silently accept a different category', function () {
    $original = CategoriaIndicador::create(['nombre' => 'Calidad']);
    $different = CategoriaIndicador::create(['nombre' => 'Investigación']);
    $first = Facultad::create(['nombre' => 'Ciencias']);
    $second = Facultad::create(['nombre' => 'Ingeniería']);
    $data = [
        'nombre' => 'Indicador con categoría',
        'categoria_indicador' => $original->id,
        'especificacion' => 'facultad',
        'facultad_id' => $first->id,
    ];
    $indicator = app(IndicadorService::class)->createAndAssign($data);

    expect(fn () => app(IndicadorService::class)->createAndAssign(array_replace($data, [
        'categoria_indicador' => $different->id,
        'facultad_id' => $second->id,
    ])))->toThrow(ValidationException::class);

    expect($indicator->fresh()->categoria_indicador_id)->toBe($original->id);
    $this->assertDatabaseCount('indicador_facultad', 1);
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
