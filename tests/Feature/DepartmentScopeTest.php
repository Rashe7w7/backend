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

test('a department chief cannot spoof ownership to delete a global indicator', function () {
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
        ->assertForbidden();

    $this->assertDatabaseHas('indicador', ['id' => $indicator->id]);
});

test('a department chief cannot write values through an unscoped web action', function () {
    JefeDepartamento::create([
        'profesor_id' => $this->professor->id,
        'departamento_id' => $this->department->id,
        'fecha_inicio' => now()->subDay(),
    ]);

    $this->actingAs($this->chief)
        ->post(route('valores.store'), ['valores' => [1 => 10]])
        ->assertForbidden();
});

test('a future department appointment does not grant write access', function () {
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
        ])->assertForbidden();

    $this->assertDatabaseMissing('indicador', ['nombre' => 'Indicador anticipado']);
});
