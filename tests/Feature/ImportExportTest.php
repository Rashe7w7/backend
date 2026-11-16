<?php

use App\Models\AnioAcademico;
use App\Models\Carrera;
use App\Models\CategoriaIndicador;
use App\Models\Facultad;
use App\Models\Grupo;
use App\Models\Indicador;
use App\Models\User;
use Illuminate\Http\UploadedFile;

test('an administrator can preview and confirm a student csv import', function () {
    $administrator = User::factory()->withoutTwoFactor()->create(['role' => 'administrador']);
    $faculty = Facultad::create(['nombre' => 'Ciencias']);
    $degree = Carrera::create(['nombre' => 'Informática', 'facultad_id' => $faculty->id]);
    $year = AnioAcademico::create(['nombre' => 'Primer año', 'nivel' => 1, 'carrera_id' => $degree->id]);
    $group = Grupo::create(['nombre' => 'G-1', 'anio_academico_id' => $year->id]);
    $file = UploadedFile::fake()->createWithContent(
        'estudiantes.csv',
        "identificador_institucional,nombre,email,grupo_id\nEST-001,Ana Pérez,ana@example.test,{$group->id}\n",
    );

    $this->actingAs($administrator)
        ->post(route('imports.preview'), ['tipo' => 'estudiantes', 'archivo' => $file])
        ->assertOk()
        ->assertSee('EST-001');

    $this->post(route('imports.store'))
        ->assertRedirect(route('imports.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('estudiante', [
        'identificador_institucional' => 'EST-001',
        'nombre' => 'Ana Pérez',
        'grupo_id' => $group->id,
    ]);
});

test('indicator exports produce spreadsheet xml and paginated pdf documents', function () {
    $administrator = User::factory()->withoutTwoFactor()->create(['role' => 'administrador']);
    $category = CategoriaIndicador::create(['nombre' => 'Calidad']);
    foreach (range(1, 120) as $index) {
        Indicador::create([
            'nombre' => 'Indicador '.str_pad((string) $index, 3, '0', STR_PAD_LEFT),
            'categoria_indicador_id' => $category->id,
            'especificacion' => 'facultad',
        ]);
    }

    $excel = $this->actingAs($administrator)->get(route('reports.indicadores.excel'));
    $excel->assertOk()
        ->assertHeader('content-type', 'application/xml; charset=UTF-8')
        ->assertSee('Excel.Sheet', false)
        ->assertSee('Indicador 120');

    $pdf = $this->get(route('reports.indicadores.pdf'));
    $pdf->assertOk()->assertHeader('content-type', 'application/pdf');
    $pdfContent = $pdf->getContent();
    expect($pdfContent)->toStartWith('%PDF-1.4')
        ->and(substr_count($pdfContent, '/Type /Page '))->toBeGreaterThanOrEqual(3);
});
