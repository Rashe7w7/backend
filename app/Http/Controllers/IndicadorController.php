<?php

namespace App\Http\Controllers;

use App\Models\Indicador;
use App\Models\IndicadorValor;
use App\Models\IndicadorCarrera;
use App\Models\IndicadorGrupo;
use App\Models\IndicadorFacultad;
use App\Models\IndicadorDepartamento;
use App\Models\IndicadorAnio;
use App\Models\CategoriaIndicador;
use App\Models\CursoAcademico;
use Illuminate\Http\Request;

class IndicadorController extends Controller
{
    public function index()
    {
        // Cargar solo lo necesario; categoría es decorativa y no se persiste
        $indicadores = Indicador::all();
        $cursosAcademicos = CursoAcademico::all();

        // Entidades relacionadas para selección
        $carreras = \App\Models\Carrera::all();

        return view('indicadores', compact('indicadores', 'cursosAcademicos', 'carreras'));
    }

    public function store(Request $request)
    {
        // Procesar múltiples indicadores enviados desde el formulario dinámico
        if ($request->has('indicadores') && is_array($request->indicadores)) {
            // Validar todo primero (sin categoría; es decorativa)
            // Agregamos unique para evitar duplicados en la validación
            $request->validate([
                'indicadores.*.nombre' => 'required|string|max:255|unique:indicador,nombre',
                'indicadores.*.especificacion' => 'required|string|in:grupo,facultad,departamento,carrera,anno,general',
            ]);

            $cursoAcademicoId = $request->input('curso_academico_id');

            // Luego crear cada uno
            foreach ($request->indicadores as $indData) {
                if (!isset($indData['nombre']) || empty($indData['nombre'])) {
                    continue;
                }

                // Verificar si el indicador ya existe antes de crear
                $existe = Indicador::where('nombre', $indData['nombre'])->exists();
                if ($existe) {
                    continue; // Saltar si ya existe
                }

                // Crear el indicador (sin categoría)
                $indicador = Indicador::create([
                    'nombre' => $indData['nombre'],
                ]);

                // Crear registro en tabla de relación según especificación
                $especificacion = $indData['especificacion'] ?? 'general';
                
                switch ($especificacion) {
                    case 'carrera':
                        IndicadorCarrera::create([
                            'indicador_id' => $indicador->id,
                            'carrera_id' => null,
                            'curso_academico_id' => $cursoAcademicoId,
                        ]);
                        break;
                    case 'grupo':
                        IndicadorGrupo::create([
                            'indicador_id' => $indicador->id,
                            'grupo_id' => null,
                            'curso_academico_id' => $cursoAcademicoId,
                        ]);
                        break;
                    case 'facultad':
                        IndicadorFacultad::create([
                            'indicador_id' => $indicador->id,
                            'facultad_id' => null,
                            'curso_academico_id' => $cursoAcademicoId,
                        ]);
                        break;
                    case 'departamento':
                        IndicadorDepartamento::create([
                            'indicador_id' => $indicador->id,
                            'departamento_id' => null,
                            'curso_academico_id' => $cursoAcademicoId,
                        ]);
                        break;
                    case 'anno':
                        IndicadorAnio::create([
                            'indicador_id' => $indicador->id,
                            'anio_academico_id' => null,
                            'curso_academico_id' => $cursoAcademicoId,
                        ]);
                        break;
                }
            }
        } else {
            // Fallback para un solo indicador (formulario antiguo)
            $request->validate([
                'nombre' => 'required|string|max:255|unique:indicador,nombre',
            ]);

            // Verificar si el indicador ya existe
            $existe = Indicador::where('nombre', $request->nombre)->exists();
            if (!$existe) {
                Indicador::create([
                    'nombre' => $request->nombre,
                ]);
            }
        }

        return redirect()->route('indicadores')->with('success', 'Indicador(es) creado(s) exitosamente');
    }

    public function storeValores(Request $request)
    {
        // Validar y guardar cada valor de indicador
        if ($request->has('valores') && is_array($request->valores)) {
            foreach ($request->valores as $indicador_id => $valor) {
                if (!is_numeric($indicador_id) || $valor === null || $valor === '') {
                    continue;
                }

                // Validar que el indicador existe
                $indicador = Indicador::find($indicador_id);
                if ($indicador) {
                    IndicadorValor::updateOrCreate(
                        ['indicador_id' => $indicador_id],
                        ['valor' => $valor]
                    );
                }
            }
        }

        return redirect()->route('indicadores')->with('success', 'Valores guardados exitosamente');
    }
}
