<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAndAssignIndicadorRequest;
use App\Http\Requests\ListAlumnosRequest;
use App\Models\Alumno;
use App\Models\AnioAcademico;
use App\Models\Asignatura;
use App\Models\Carrera;
use App\Models\CategoriaIndicador;
use App\Models\Curriculo;
use App\Models\CursoAcademico;
use App\Models\Departamento;
use App\Models\Disciplina;
use App\Models\Facultad;
use App\Models\Grupo;
use App\Models\Indicador;
use App\Models\IndicadorAnio;
use App\Models\IndicadorCarrera;
use App\Models\IndicadorDepartamento;
use App\Models\IndicadorFacultad;
use App\Models\IndicadorGrupo;
use App\Models\PlanEstudio;
use App\Services\IndicadorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DataController extends Controller
{
    // Facultades
    public function getFacultades()
    {
        return response()->json(Facultad::all());
    }

    public function storeFacultad(Request $request)
    {
        $validated = $request->validate(['nombre' => 'required|string|unique:facultad,nombre']);
        $facultad = Facultad::create($validated);

        return response()->json($facultad, 201);
    }

    // Departamentos
    public function getDepartamentos()
    {
        return response()->json(Departamento::all());
    }

    public function storeDepartamento(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string',
            'facultad_id' => 'required|exists:facultad,id',
        ]);
        $departamento = Departamento::create($validated);

        return response()->json($departamento, 201);
    }

    // Carreras
    public function getCarreras()
    {
        try {
            $carreras = Carrera::all();
            \Log::info('Carreras obtenidas:', ['count' => count($carreras)]);

            return response()->json($carreras);
        } catch (\Exception $e) {
            \Log::error('Error obteniendo carreras:', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'No se pudieron obtener las carreras.'], 500);
        }
    }

    public function storeCarrera(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string',
            'facultad_id' => 'required|exists:facultad,id',
        ]);
        $carrera = Carrera::create($validated);

        return response()->json($carrera, 201);
    }

    // Planes de Estudio
    public function getPlanesEstudio()
    {
        return response()->json(PlanEstudio::all());
    }

    public function getPlanesByCarrera($carreraId)
    {
        try {
            $planes = PlanEstudio::where('carrera_id', $carreraId)
                ->with('carrera')
                ->orderBy('nombre')
                ->get()
                ->map(function ($plan) {
                    return [
                        'id' => $plan->id,
                        'nombre' => $plan->nombre,
                        'carrera_id' => $plan->carrera_id,
                        'fecha_inicio' => $plan->fecha_inicio,
                        'fecha_fin' => $plan->fecha_fin,
                        'vigente' => $plan->vigente,
                        'anio_vigencia' => $plan->fecha_inicio ? $plan->fecha_inicio->year : null,
                        'carrera' => $plan->carrera,
                    ];
                });

            return response()->json($planes);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudieron obtener los planes de estudio.'], 500);
        }
    }

    public function storePlanEstudio(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string',
            'carrera_id' => 'required|exists:carrera,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date',
            'vigente' => 'boolean',
        ]);
        $plan = PlanEstudio::create($validated);

        return response()->json($plan, 201);
    }

    // Disciplinas
    public function getDisciplinas(Request $request)
    {
        $query = Disciplina::with([
            'planEstudio' => function ($query) {
                $query->with('carrera');
            },
        ]);

        // Filtrar por plan_estudio_id si se proporciona
        if ($request->has('plan_estudio_id') && $request->plan_estudio_id) {
            $query->where('plan_estudio_id', $request->plan_estudio_id);
        }

        return response()->json($query->get());
    }

    public function storeDisciplina(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string',
            'plan_estudio_id' => 'required|exists:plan_estudio,id',
        ]);
        $disciplina = Disciplina::create($validated);

        return response()->json($disciplina, 201);
    }

    // Asignaturas
    public function getAsignaturas(Request $request)
    {
        $query = Asignatura::with([
            'disciplina' => function ($query) {
                $query->with('planEstudio.carrera');
            },
            'aniosAcademicos',
        ]);

        // Filtrar por plan_estudio_id si se proporciona
        if ($request->has('plan_estudio_id') && $request->plan_estudio_id) {
            $query->whereHas('disciplina', function ($q) {
                $q->where('plan_estudio_id', request('plan_estudio_id'));
            });
        }
        // Filtrar por carrera_id si se proporciona
        elseif ($request->has('carrera_id') && $request->carrera_id) {
            $query->whereHas('disciplina.planEstudio', function ($q) {
                $q->where('carrera_id', request('carrera_id'));
            });
        }

        $asignaturas = $query->get();

        // Mapear los datos para incluir el año de cada asignatura
        return response()->json($asignaturas->map(function ($asignatura) {
            return [
                'id' => $asignatura->id,
                'nombre' => $asignatura->nombre,
                'codigo' => $asignatura->codigo ?? null,
                'horas' => $asignatura->horas ?? null,
                'disciplina' => $asignatura->disciplina ? [
                    'id' => $asignatura->disciplina->id,
                    'nombre' => $asignatura->disciplina->nombre,
                    'plan_estudio' => $asignatura->disciplina->planEstudio ? [
                        'id' => $asignatura->disciplina->planEstudio->id,
                        'nombre' => $asignatura->disciplina->planEstudio->nombre,
                        'carrera' => $asignatura->disciplina->planEstudio->carrera ? [
                            'id' => $asignatura->disciplina->planEstudio->carrera->id,
                            'nombre' => $asignatura->disciplina->planEstudio->carrera->nombre,
                        ] : null,
                    ] : null,
                ] : null,
                'ano' => $asignatura->aniosAcademicos->first()?->nombre ?? 'Sin Año',
            ];
        }));
    }

    public function storeAsignatura(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string',
            'disciplina_id' => 'required|exists:disciplina,id',
        ]);
        $asignatura = Asignatura::create($validated);

        return response()->json($asignatura, 201);
    }

    // Currículos
    public function getCurriculos()
    {
        return response()->json(Curriculo::all());
    }

    public function storeCurriculo(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string',
            'asignatura_id' => 'required|exists:asignatura,id',
        ]);
        $curriculo = Curriculo::create($validated);

        return response()->json($curriculo, 201);
    }

    // Años Académicos / Cursos Académicos
    public function getAniosAcademicos()
    {
        return response()->json(CursoAcademico::all());
    }

    // Función para obtener verdaderos años académicos (1er año, 2do año, etc.)
    public function getYearsAcademicos()
    {
        return response()->json(AnioAcademico::all());
    }

    public function storeAnioAcademico(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|unique:anio_academico,nombre',
            'carrera_id' => 'required|exists:carrera,id',
        ]);
        $anio = AnioAcademico::create($validated);

        return response()->json($anio, 201);
    }

    // Grupos
    public function getGrupos()
    {
        return response()->json(Grupo::all());
    }

    public function storeGrupo(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string',
            'anio_academico_id' => 'required|exists:anio_academico,id',
        ]);
        $grupo = Grupo::create($validated);

        return response()->json($grupo, 201);
    }

    // Alumnos
    public function getAlumnos(ListAlumnosRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $query = Alumno::query()->with('grupo');

        $query->when($request->integer('grupo_id'), fn ($q, $id) => $q->where('grupo_id', $id));
        $query->when($request->integer('carrera_id'), function ($q, $id) {
            $q->whereHas('grupo.anioAcademico', fn ($year) => $year->where('carrera_id', $id));
        });
        $query->when($request->string('search')->toString(), function ($q, $search) {
            $q->where(fn ($student) => $student
                ->whereLike('nombre', "%{$search}%")
                ->orWhereLike('identificador_institucional', "%{$search}%"));
        });

        return response()->json($query->orderBy('nombre')->paginate($validated['per_page'] ?? 50)->withQueryString());
    }

    public function storeAlumno(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string',
            'identificador_institucional' => 'nullable|string|max:100|unique:estudiante,identificador_institucional',
            'email' => 'nullable|email|unique:estudiante,email',
            'grupo_id' => 'required|exists:grupo,id',
        ]);
        $alumno = Alumno::create($validated);

        return response()->json($alumno, 201);
    }

    // Categorías de Indicador
    public function getCategoriasIndicador()
    {
        return response()->json(CategoriaIndicador::all());
    }

    public function storeCategoriaIndicador(Request $request)
    {
        $validated = $request->validate(['nombre' => 'required|string|unique:categoria_indicador']);
        $categoria = CategoriaIndicador::create($validated);

        return response()->json($categoria, 201);
    }

    // Indicadores
    public function getIndicadores()
    {
        try {
            $result = collect();

            // Cargar indicadores de carrera
            $carrera = IndicadorCarrera::with('indicador.categoria')->get()->map(function ($ic) {
                return [
                    'id' => $ic->indicador->id,
                    'nombre' => $ic->nombre,
                    'categoria_indicador_id' => $ic->indicador->categoria_indicador_id,
                    'especificacion' => 'carrera',
                    'created_at' => $ic->indicador->created_at,
                    'updated_at' => $ic->indicador->updated_at,
                ];
            });
            $result = $result->concat($carrera);

            // Cargar indicadores de grupo
            $grupo = IndicadorGrupo::with('indicador.categoria')->get()->map(function ($ig) {
                return [
                    'id' => $ig->indicador->id,
                    'nombre' => $ig->nombre,
                    'categoria_indicador_id' => $ig->indicador->categoria_indicador_id,
                    'especificacion' => 'grupo',
                    'created_at' => $ig->indicador->created_at,
                    'updated_at' => $ig->indicador->updated_at,
                ];
            });
            $result = $result->concat($grupo);

            // Cargar indicadores de facultad
            $facultad = IndicadorFacultad::with('indicador.categoria')->get()->map(function ($if) {
                return [
                    'id' => $if->indicador->id,
                    'nombre' => $if->nombre,
                    'categoria_indicador_id' => $if->indicador->categoria_indicador_id,
                    'especificacion' => 'facultad',
                    'created_at' => $if->indicador->created_at,
                    'updated_at' => $if->indicador->updated_at,
                ];
            });
            $result = $result->concat($facultad);

            // Cargar indicadores de departamento
            $departamento = IndicadorDepartamento::with('indicador.categoria')->get()->map(function ($id) {
                return [
                    'id' => $id->indicador->id,
                    'nombre' => $id->nombre,
                    'categoria_indicador_id' => $id->indicador->categoria_indicador_id,
                    'especificacion' => 'departamento',
                    'created_at' => $id->indicador->created_at,
                    'updated_at' => $id->indicador->updated_at,
                ];
            });
            $result = $result->concat($departamento);

            // Cargar indicadores de año académico
            $anno = IndicadorAnio::with('indicador.categoria')->get()->map(function ($ia) {
                return [
                    'id' => $ia->indicador->id,
                    'nombre' => $ia->nombre,
                    'categoria_indicador_id' => $ia->indicador->categoria_indicador_id,
                    'especificacion' => 'anno',
                    'created_at' => $ia->indicador->created_at,
                    'updated_at' => $ia->indicador->updated_at,
                ];
            });
            $result = $result->concat($anno);

            // Eliminar duplicados (mismo indicador puede estar en múltiples especificaciones)
            $result = $result->unique('id')->values();

            return response()->json($result);
        } catch (\Exception $e) {
            \Log::error('Error en getIndicadores: '.$e->getMessage());

            return response()->json(['error' => 'No se pudieron obtener los indicadores.'], 500);
        }
    }

    public function storeIndicador(Request $request)
    {
        // Crear solo el indicador padre; la asignación a entidad específica
        // se hará en pasos posteriores cuando el usuario seleccione destino
        $validated = $request->validate([
            'nombre' => 'required|string|unique:indicador,nombre',
            'especificacion' => 'required|string|in:grupo,facultad,departamento,carrera,anno',
        ]);

        $indicador = Indicador::create(['nombre' => $validated['nombre']]);

        $response = $indicador->toArray();
        $response['especificacion'] = strtolower($validated['especificacion']);

        return response()->json($response, 201);
    }

    public function destroyIndicador($id)
    {
        // Eliminar el indicador padre; relaciones hijas se eliminarán por cascada si están configuradas
        $indicador = Indicador::find($id);
        if (! $indicador) {
            return response()->json(['error' => 'Indicador no encontrado'], 404);
        }

        $indicador->delete();

        return response()->json(['message' => 'Indicador eliminado correctamente'], 200);
    }

    // Indicadores por Carrera
    public function getIndicadoresCarrera(Request $request)
    {
        $validated = $request->validate([
            'carrera_id' => 'required|integer|exists:carrera,id',
            'curso_academico_id' => 'required|integer|exists:curso_academico,id',
        ]);

        $rows = IndicadorCarrera::with('indicador')
            ->where('carrera_id', $validated['carrera_id'])
            ->where('curso_academico_id', $validated['curso_academico_id'])
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'indicador_id' => $r->indicador_id,
                    'nombre' => $r->indicador->nombre ?? null,
                    'carrera_id' => $r->carrera_id,
                    'curso_academico_id' => $r->curso_academico_id,
                ];
            });

        return response()->json($rows);
    }

    public function storeIndicadoresCarrera(Request $request)
    {
        $validated = $request->validate([
            'indicador_id' => 'required|integer|exists:indicador,id',
            'carrera_id' => 'required|integer|exists:carrera,id',
            'curso_academico_id' => 'required|integer|exists:curso_academico,id',
        ]);

        // Evitar duplicados para misma combinación
        $exists = IndicadorCarrera::where('indicador_id', $validated['indicador_id'])
            ->where('carrera_id', $validated['carrera_id'])
            ->where('curso_academico_id', $validated['curso_academico_id'])
            ->first();

        if ($exists) {
            return response()->json($exists, 200);
        }

        $validated['nombre'] = Indicador::findOrFail($validated['indicador_id'])->nombre;
        $row = IndicadorCarrera::create($validated);

        return response()->json($row, 201);
    }

    /**
     * Crear indicador y asignarlo a su especificación en un solo paso
     */
    public function createAndAssignIndicador(CreateAndAssignIndicadorRequest $request, IndicadorService $service): JsonResponse
    {
        $indicador = $service->createAndAssign($request->validated());

        return response()->json($indicador, 201);
    }
}
