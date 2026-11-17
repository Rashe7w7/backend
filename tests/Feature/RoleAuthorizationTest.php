<?php

use App\Models\CategoriaIndicador;
use App\Models\Indicador;
use App\Models\User;
use Database\Seeders\LocalAdminSeeder;
use Database\Seeders\RoleUserSeeder;
use Illuminate\Support\Facades\Hash;

const INDICATOR_PERMISSION_MESSAGE = 'No tienes permisos para realizar esta operación con este tipo de indicador';

test('the backend seeds one static account for every application role', function () {
    $this->seed(RoleUserSeeder::class);

    $accounts = [
        'administrador@sistema.test' => ['administrador', 'Administrador123!'],
        'ppa@sistema.test' => ['ppa', 'Ppa12345!'],
        'jefe.departamento@sistema.test' => ['jefe_departamento', 'JefeDepartamento123!'],
        'decano@sistema.test' => ['decano', 'Decano123!'],
        'rector@sistema.test' => ['rector', 'Rector123!'],
        'vicerrector@sistema.test' => ['vicerrector', 'Vicerrector123!'],
        'secretaria@sistema.test' => ['secretaria', 'Secretaria123!'],
    ];

    expect(User::query()->count())->toBe(7);

    foreach ($accounts as $email => [$role, $password]) {
        $user = User::query()->where('email', $email)->firstOrFail();

        expect($user->role)->toBe($role)
            ->and(Hash::check($password, $user->password))->toBeTrue();
    }
});

test('the local administrator account keeps its documented credentials', function () {
    $this->seed(LocalAdminSeeder::class);

    $administrator = User::query()->where('email', 'admin@example.com')->firstOrFail();

    expect($administrator->role)->toBe('administrador')
        ->and(Hash::check('password', $administrator->password))->toBeTrue();
});

test('each role can create only its assigned indicator types', function (string $role, string $allowed, string $denied) {
    $category = CategoriaIndicador::query()->firstOrFail();
    $user = User::factory()->withoutTwoFactor()->create(['role' => $role]);

    $this->actingAs($user)
        ->postJson('/api/indicadores', [
            'nombre' => "Permitido para {$role}",
            'categoria_indicador_id' => $category->id,
            'especificacion' => $allowed,
        ])
        ->assertCreated();

    $this->postJson('/api/indicadores', [
        'nombre' => "Denegado para {$role}",
        'categoria_indicador_id' => $category->id,
        'especificacion' => $denied,
    ])->assertForbidden()->assertJsonPath('message', INDICATOR_PERMISSION_MESSAGE);
})->with([
    'PPA' => ['ppa', 'grupo', 'departamento'],
    'jefe de departamento' => ['jefe_departamento', 'departamento', 'carrera'],
    'decano' => ['decano', 'carrera', 'facultad'],
    'rector' => ['rector', 'facultad', 'grupo'],
    'vicerrector' => ['vicerrector', 'facultad', 'nivel_academico'],
]);

test('the administrator can create every indicator type', function (string $specification) {
    $category = CategoriaIndicador::query()->firstOrFail();
    $administrator = User::factory()->withoutTwoFactor()->create(['role' => 'administrador']);

    $this->actingAs($administrator)
        ->postJson('/api/indicadores', [
            'nombre' => "Indicador {$specification}",
            'categoria_indicador_id' => $category->id,
            'especificacion' => $specification,
        ])->assertCreated();
})->with(['carrera', 'facultad', 'departamento', 'grupo', 'nivel_academico']);

test('indicator listings expose only the types available to the role', function () {
    $category = CategoriaIndicador::query()->firstOrFail();
    foreach (['carrera', 'facultad', 'departamento', 'grupo', 'nivel_academico'] as $specification) {
        Indicador::query()->create([
            'nombre' => "Indicador {$specification}",
            'categoria_indicador_id' => $category->id,
            'especificacion' => $specification,
        ]);
    }

    $ppa = User::factory()->withoutTwoFactor()->create(['role' => 'ppa']);
    $this->actingAs($ppa)->getJson('/api/indicadores')
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.especificacion', 'grupo');

    $secretary = User::factory()->withoutTwoFactor()->create(['role' => 'secretaria']);
    $this->actingAs($secretary)->getJson('/api/indicadores')
        ->assertOk()
        ->assertJsonCount(5);
});

test('role restrictions protect update delete and value operations in the backend', function () {
    $category = CategoriaIndicador::query()->firstOrFail();
    $departmentIndicator = Indicador::query()->create([
        'nombre' => 'Indicador departamental',
        'categoria_indicador_id' => $category->id,
        'especificacion' => 'departamento',
    ]);
    $facultyIndicator = Indicador::query()->create([
        'nombre' => 'Indicador de facultad',
        'categoria_indicador_id' => $category->id,
        'especificacion' => 'facultad',
    ]);
    $chief = User::factory()->withoutTwoFactor()->create(['role' => 'jefe_departamento']);

    $this->actingAs($chief)->putJson("/api/indicadores/{$departmentIndicator->id}", [
        'nombre' => 'Indicador departamental actualizado',
        'categoria_indicador_id' => $category->id,
        'especificacion' => 'grupo',
    ])->assertOk()->assertJsonPath('especificacion', 'grupo');

    $this->putJson("/api/indicadores/{$departmentIndicator->id}", [
        'nombre' => 'Cambio indebido',
        'categoria_indicador_id' => $category->id,
        'especificacion' => 'facultad',
    ])->assertForbidden()->assertJsonPath('message', INDICATOR_PERMISSION_MESSAGE);

    $this->deleteJson("/api/indicadores/{$facultyIndicator->id}")
        ->assertForbidden()
        ->assertJsonPath('message', INDICATOR_PERMISSION_MESSAGE);

    $this->post(route('valores.store'), ['valores' => [$facultyIndicator->id => 25]])
        ->assertForbidden()
        ->assertSee(INDICATOR_PERMISSION_MESSAGE);

    $this->assertDatabaseHas('indicador', ['id' => $facultyIndicator->id]);
    $this->assertDatabaseMissing('indicador_valor', ['indicador_id' => $facultyIndicator->id]);
});

test('the secretary can select existing indicators but cannot mutate them', function () {
    $category = CategoriaIndicador::query()->firstOrFail();
    $indicator = Indicador::query()->create([
        'nombre' => 'Indicador exportable',
        'categoria_indicador_id' => $category->id,
        'especificacion' => 'carrera',
    ]);
    $secretary = User::factory()->withoutTwoFactor()->create(['role' => 'secretaria']);

    $this->actingAs($secretary)->getJson('/api/indicadores')
        ->assertOk()
        ->assertJsonPath('0.id', $indicator->id);
    $this->get(route('indicadores'))->assertOk();
    $this->get(route('reports.indicadores.excel'))->assertForbidden();
    $this->get(route('reports.indicadores.pdf'))->assertForbidden();

    $this->postJson('/api/indicadores', [
        'nombre' => 'No permitido',
        'categoria_indicador_id' => $category->id,
        'especificacion' => 'carrera',
    ])->assertForbidden()->assertJsonPath('message', INDICATOR_PERMISSION_MESSAGE);

    $this->deleteJson("/api/indicadores/{$indicator->id}")
        ->assertForbidden()
        ->assertJsonPath('message', INDICATOR_PERMISSION_MESSAGE);
});
