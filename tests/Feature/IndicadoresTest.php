<?php

use App\Models\AnioAcademico;
use App\Models\Carrera;
use App\Models\CategoriaIndicador;
use App\Models\Facultad;
use App\Models\Indicador;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->withoutTwoFactor()->create(['role' => 'administrador']));
});

test('indicator listing returns each parent once with category and specification', function () {
    $category = CategoriaIndicador::query()->firstOrCreate(['nombre' => 'Numérico']);
    $indicator = Indicador::create([
        'nombre' => 'Matrícula total',
        'categoria_indicador_id' => $category->id,
        'especificacion' => 'facultad',
    ]);

    $this->getJson('/api/indicadores')
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.id', $indicator->id)
        ->assertJsonPath('0.categoria.nombre', 'Numérico')
        ->assertJsonPath('0.especificacion', 'facultad');
});

test('academic level endpoint returns distinct ordered levels', function () {
    $faculty = Facultad::create(['nombre' => 'Ciencias']);
    $firstDegree = Carrera::create(['nombre' => 'Matemática', 'facultad_id' => $faculty->id]);
    $secondDegree = Carrera::create(['nombre' => 'Física', 'facultad_id' => $faculty->id]);

    foreach (range(1, 5) as $level) {
        AnioAcademico::create([
            'nombre' => "{$level}.º año - Matemática",
            'nivel' => $level,
            'carrera_id' => $firstDegree->id,
        ]);
        AnioAcademico::create([
            'nombre' => "{$level}.º año - Física",
            'nivel' => $level,
            'carrera_id' => $secondDegree->id,
        ]);
    }

    $this->getJson('/api/niveles-academicos')
        ->assertOk()
        ->assertJsonCount(5)
        ->assertJsonPath('0.nivel', 1)
        ->assertJsonPath('4.nivel', 5);
});

test('indicator page moves instance selection out of the creation form', function () {
    $response = $this->get('/indicadores');

    $response->assertOk()
        ->assertSee('id="instanciaSelect"', false)
        ->assertSee('Nivel académico')
        ->assertDontSee('id="carreraCrear"', false)
        ->assertDontSee('id="camposEspecificacion"', false)
        ->assertDontSee('Guardar Vista');
});
