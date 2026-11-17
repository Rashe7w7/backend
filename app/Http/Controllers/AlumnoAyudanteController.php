<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlumnoAyudanteRequest;
use App\Models\Alumno;
use App\Models\AlumnoAyudante;
use App\Models\Asignatura;
use App\Models\CursoAcademico;
use App\Models\Grupo;
use App\Models\Profesor;
use App\Services\AcademicScopeService;
use App\Services\AlumnoAyudanteService;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlumnoAyudanteController extends Controller
{
    public function index(Request $request, AcademicScopeService $scopeService): View
    {
        abort_unless($request->user()->isAdministrator() || $request->user()->hasRole('ppa'), 403);

        $grupos = Grupo::query()->with('anioAcademico.carrera')
            ->when(! $request->user()->isAdministrator(), fn ($query) => $query->whereHas(
                'asignacionesPpa',
                fn ($assignments) => $assignments->where('user_id', $request->user()->id)->where('activo', true),
            ))->get();
        $groupIds = $grupos->pluck('id');
        $alumnos = Alumno::query()->whereIn('grupo_id', $groupIds)->orderBy('nombre')->get();
        $asignaturas = Asignatura::query()->orderBy('nombre')->get();
        $profesores = Profesor::query()->orderBy('nombre')->get();
        $cursosAcademicos = CursoAcademico::all();

        // Cargar los alumnos ayudantes con sus relaciones
        $alumnosAyudantes = AlumnoAyudante::with([
            'alumno',
            'profesorAsignatura.profesor',
            'profesorAsignatura.asignatura',
            'cursoAcademico',
            'grupo.anioAcademico.carrera',
        ])->when(! $request->user()->isAdministrator(), fn ($query) => $query->whereIn('grupo_id', $groupIds))->get();

        return view('tables.alumnoayudante', compact(
            'alumnos',
            'asignaturas',
            'profesores',
            'grupos',
            'cursosAcademicos',
            'alumnosAyudantes'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'alumno_id' => 'required|exists:estudiante,id',
            'profesor_asignatura_id' => 'required|exists:profesor_asignatura,id',
            'curso_academico_id' => 'required|exists:curso_academico,id',
        ]);

        AlumnoAyudante::create($request->only([
            'alumno_id',
            'profesor_asignatura_id',
            'curso_academico_id',
        ]));

        return redirect()->route('alumnoayudante')->with('success', 'Alumno ayudante asignado correctamente');
    }

    public function storeFromForm(StoreAlumnoAyudanteRequest $request, AlumnoAyudanteService $service): JsonResponse
    {
        $alumnoAyudante = $service->create($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Designación guardada correctamente',
            'id' => $alumnoAyudante->id,
        ], 201);
    }

    public function destroy(Request $request, int $id, AcademicScopeService $scopeService, AuditService $audit): JsonResponse|RedirectResponse
    {
        try {
            $alumnoAyudante = AlumnoAyudante::findOrFail($id);
            abort_unless(
                $request->user()->isAdministrator()
                || ($alumnoAyudante->grupo_id !== null && $scopeService->canAccess($request->user(), 'grupo', $alumnoAyudante->grupo_id)),
                403,
            );
            $before = $alumnoAyudante->attributesToArray();
            $alumnoAyudante->update(['estado' => 'inactivo']);
            $audit->recordModel('alumno_ayudante.desactivado', $alumnoAyudante, $before);

            // Si es petición AJAX, retornar JSON
            if (request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Alumno ayudante eliminado correctamente']);
            }

            return redirect()->route('alumnoayudante')->with('success', 'Alumno ayudante eliminado correctamente');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['error' => 'No se pudo eliminar la designación.'], 400);
            }

            return redirect()->route('alumnoayudante')->with('error', 'Error al eliminar');
        }
    }

    /**
     * API para obtener profesores según asignatura seleccionada
     */
    public function getProfesoresByAsignatura($asignaturaId)
    {
        $profesores = Profesor::whereHas('asignaturas', function ($query) use ($asignaturaId) {
            $query->where('asignatura_id', $asignaturaId);
        })->get();

        return response()->json($profesores);
    }

    /**
     * API para obtener asignaturas según profesor seleccionado
     */
    public function getAsignaturasByProfesor($profesorId)
    {
        $profesor = Profesor::with('asignaturas')->findOrFail($profesorId);

        return response()->json($profesor->asignaturas);
    }
}
