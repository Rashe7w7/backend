<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportPreviewRequest;
use App\Models\Alumno;
use App\Models\Asignatura;
use App\Models\Profesor;
use App\Services\SpreadsheetReader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ImportController extends Controller
{
    public function index(): View
    {
        return view('imports.index');
    }

    public function preview(ImportPreviewRequest $request, SpreadsheetReader $reader): View
    {
        $validated = $request->validated();
        $rows = $reader->read($validated['archivo']);
        $rowErrors = $this->validateRows($validated['tipo'], $rows);
        $request->session()->put('pending_import', ['tipo' => $validated['tipo'], 'rows' => $rows]);

        return view('imports.index', compact('rows', 'rowErrors') + ['tipo' => $validated['tipo']]);
    }

    public function store(Request $request): RedirectResponse
    {
        $pending = $request->session()->pull('pending_import');
        abort_unless($pending, 419, 'La vista previa expiró. Cargue nuevamente el archivo.');
        $errors = $this->validateRows($pending['tipo'], $pending['rows']);
        if ($errors) {
            return back()->withErrors(['archivo' => 'El archivo contiene errores; no se importó ninguna fila.']);
        }

        $count = DB::transaction(function () use ($pending) {
            foreach ($pending['rows'] as $row) {
                match ($pending['tipo']) {
                    'estudiantes' => Alumno::updateOrCreate(
                        ['identificador_institucional' => trim($row['identificador_institucional'])],
                        ['nombre' => $row['nombre'], 'email' => ($row['email'] ?? null) ?: null, 'grupo_id' => $row['grupo_id'], 'carrera' => $row['carrera'] ?? null],
                    ),
                    'profesores' => Profesor::updateOrCreate(
                        ['cedula' => trim($row['cedula'])],
                        ['nombre' => $row['nombre'], 'email' => ($row['email'] ?? null) ?: null],
                    ),
                    'asignaturas' => Asignatura::updateOrCreate(
                        ['codigo' => trim($row['codigo'])],
                        ['nombre' => $row['nombre'], 'disciplina_id' => $row['disciplina_id'], 'horas' => ($row['horas'] ?? '') === '' ? null : ($row['horas'] ?? null)],
                    ),
                };
            }

            return count($pending['rows']);
        });

        return redirect()->route('imports.index')->with('success', "Se procesaron {$count} filas correctamente.");
    }

    private function validateRows(string $type, array $rows): array
    {
        $rules = match ($type) {
            'estudiantes' => [
                'identificador_institucional' => ['required', 'string', 'max:100'],
                'nombre' => ['required', 'string', 'max:255'],
                'email' => ['nullable', 'email'],
                'grupo_id' => ['required', 'integer', 'exists:grupo,id'],
            ],
            'profesores' => [
                'cedula' => ['required', 'string', 'max:100'],
                'nombre' => ['required', 'string', 'max:255'],
                'email' => ['nullable', 'email'],
            ],
            'asignaturas' => [
                'codigo' => ['required', 'string', 'max:100'],
                'nombre' => ['required', 'string', 'max:255'],
                'disciplina_id' => ['required', 'integer', 'exists:disciplina,id'],
                'horas' => ['nullable', 'numeric', 'min:0'],
            ],
        };
        $errors = [];
        foreach ($rows as $index => $row) {
            $validator = Validator::make($row, $rules);
            if ($validator->fails()) {
                $errors[$index + 2] = $validator->errors()->all();
            }
        }

        return $errors;
    }
}
