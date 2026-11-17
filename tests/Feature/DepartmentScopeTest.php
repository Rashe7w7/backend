<?php

use App\Models\CategoriaIndicador;
use App\Models\Departamento;
use App\Models\Facultad;
use App\Models\Indicador;
use App\Models\JefeDepartamento;
use App\Models\MiembroDepartamento;
use App\Models\Profesor;
use App\Models\User;

beforeEach(function () {
    $this->faculty = Facultad::create(['nombre' => 'Facultad de pruebas']);
    $this->department = Departamento::create([
        'nombre' => 'Departamento de pruebas',
        'facultad_id' => $this->faculty->id,
    ]);
    $this->professor = Profesor::create([
        'nombre' => 'Jefe de pruebas',
        'email' => 'jefe@example.test',
        'cedula' => 'J-001',
    ]);
    MiembroDepartamento::create([
        'profesor_id' => $this->professor->id,
        'departamento_id' => $this->department->id,
    ]);
    $this->chief = User::factory()->withoutTwoFactor()->create([
        'role' => 'jefe_departamento',
        'profesor_id' => $this->professor->id,
    ]);
});

test('a department chief can delete a department indicator', function () {
    JefeDepartamento::create([
        'profesor_id' => $this->professor->id,
        'departamento_id' => $this->department->id,
        'fecha_inicio' => now()->subDay(),
    ]);
    $category = CategoriaIndicador::create(['nombre' => 'Calidad']);
    $indicator = Indicador::create([
        'nombre' => 'Indicador global',
        'categoria_indicador_id' => $category->id,
        'especificacion' => 'departamento',
    ]);

    $this->actingAs($this->chief)
        ->deleteJson('/api/indicadores/'.$indicator->id, ['departamento_id' => $this->department->id])
        ->assertOk();

    $this->assertDatabaseMissing('indicador', ['id' => $indicator->id]);
});

test('a department chief can write values for department indicators', function () {
    JefeDepartamento::create([
        'profesor_id' => $this->professor->id,
        'departamento_id' => $this->department->id,
        'fecha_inicio' => now()->subDay(),
    ]);

    $category = CategoriaIndicador::create(['nombre' => 'Calidad']);
    $indicator = Indicador::create([
        'nombre' => 'Indicador departamental',
        'categoria_indicador_id' => $category->id,
        'especificacion' => 'departamento',
    ]);

    $this->actingAs($this->chief)
        ->post(route('valores.store'), ['valores' => [$indicator->id => 10]])
        ->assertRedirect(route('indicadores'));

    $this->assertDatabaseHas('indicador_valor', [
        'indicador_id' => $indicator->id,
        'valor' => 10,
    ]);
});

test('indicator permissions depend on the role and not on appointment dates', function () {
    JefeDepartamento::create([
        'profesor_id' => $this->professor->id,
        'departamento_id' => $this->department->id,
        'fecha_inicio' => now()->addDay(),
    ]);
    $category = CategoriaIndicador::create(['nombre' => 'Calidad']);

    $this->actingAs($this->chief)
        ->postJson('/api/indicadores', [
            'nombre' => 'Indicador anticipado',
            'categoria_indicador_id' => $category->id,
            'especificacion' => 'departamento',
        ])->assertCreated();

    $this->assertDatabaseHas('indicador', ['nombre' => 'Indicador anticipado']);
});
