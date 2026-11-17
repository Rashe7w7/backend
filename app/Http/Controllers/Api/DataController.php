<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListAlumnosRequest;
use App\Http\Requests\StoreIndicadorRequest;
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
use App\Models\PlanEstudio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DataController extends Controller
{
    // Facultades
    public function getFacultades()
    {
        return response()->json(Facultad::query()->orderBy('nombre')->get());
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
        return response()->json(Departamento::query()->orderBy('nombre')->get());
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
            $carreras = Carrera::query()->orderBy('nombre')->get();
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
        return response()->json(CursoAcademico::query()->orderBy('nombre')->get());
    }

    // Función para obtener verdaderos años académicos (1er año, 2do año, etc.)
    public function getYearsAcademicos()
    {
        return response()->json(AnioAcademico::query()->orderBy('nivel')->orderBy('nombre')->get());
    }

    public function getNivelesAcademicos(): JsonResponse
    {
        $niveles = AnioAcademico::query()
            ->select('nivel')
            ->distinct()
            ->orderBy('nivel')
            ->pluck('nivel')
            ->map(fn (int $nivel): array => [
                'id' => $nivel,
                'nombre' => $nivel.'.º año',
                'nivel' => $nivel,
            ])
            ->values();

        return response()->json($niveles);
    }

    public function storeAnioAcademico(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|unique:anio_academico,nombre',
            'carrera_id' => 'required|exists:carrera,id',
            'nivel' => 'required|integer|between:1,5',
        ]);
        $anio = AnioAcademico::create($validated);

        return response()->json($anio, 201);
    }

    // Grupos
    public function getGrupos()
    {
        $grupos = Grupo::query()
            ->with('anioAcademico.carrera')
            ->orderBy('nombre')
            ->get()
            ->map(fn (Grupo $grupo): array => [
                'id' => $grupo->id,
                'nombre' => $grupo->nombre,
                'anio_academico_id' => $grupo->anio_academico_id,
                'contexto' => collect([
                    $grupo->anioAcademico?->carrera?->nombre,
                    $grupo->anioAcademico?->nivel ? $grupo->anioAcademico->nivel.'.º año' : null,
                ])->filter()->join(' — '),
            ]);

        return response()->json($grupos);
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
        return response()->json(CategoriaIndicador::query()->orderBy('nombre')->get());
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
        return response()->json(
            Indicador::query()
                ->with('categoria:id,nombre')
                ->orderBy('nombre')
                ->get()
        );
    }

    public function storeIndicador(StoreIndicadorRequest $request): JsonResponse
    {
        $indicador = Indicador::query()->create($request->validated());

        return response()->json($indicador->load('categoria:id,nombre'), 201);
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
}
