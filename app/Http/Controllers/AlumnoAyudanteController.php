<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlumnoAyudanteRequest;
use App\Models\Alumno;
use App\Models\AlumnoAyudante;
use App\Models\AnioAcademico;
use App\Models\Profesor;
use App\Models\ProfesorAsignatura;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AlumnoAyudanteController extends Controller
{
    public function index(): View
    {
        Gate::authorize('view-student-helpers');

        $alumnos = Alumno::query()
            ->with('grupo.anioAcademico.carrera.facultad')
            ->orderBy('nombre')
            ->get();

        $profesorAsignaturas = ProfesorAsignatura::query()
            ->with(['asignatura', 'profesor', 'cursoAcademico'])
            ->whereNotNull('curso_academico_id')
            ->get()
            ->sortBy([
                ['asignatura.nombre', 'asc'],
                ['profesor.nombre', 'asc'],
            ]);

        $alumnosAyudantes = AlumnoAyudante::query()->with([
            'alumno.grupo.anioAcademico.carrera.facultad',
            'profesorAsignatura.profesor',
            'profesorAsignatura.asignatura',
            'cursoAcademico',
        ])->latest()->paginate(25)->withQueryString();

        [$reportesCarrera, $reportesFacultad, $reportesAnio] = $this->reportes();

        return view('tables.alumnoayudante', compact(
            'alumnos',
            'profesorAsignaturas',
            'alumnosAyudantes',
            'reportesCarrera',
            'reportesFacultad',
            'reportesAnio',
        ));
    }

    public function store(StoreAlumnoAyudanteRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $profesorAsignatura = ProfesorAsignatura::query()->findOrFail($validated['profesor_asignatura_id']);

        AlumnoAyudante::query()->create([
            'alumno_id' => $validated['alumno_id'],
            'profesor_asignatura_id' => $profesorAsignatura->id,
            'curso_academico_id' => $profesorAsignatura->curso_academico_id,
        ]);

        return redirect()->route('alumnoayudante')->with('success', 'Alumno ayudante designado correctamente.');
    }

    public function destroy(int $id): JsonResponse|RedirectResponse
    {
        Gate::authorize('delete-student-helpers');

        $alumnoAyudante = AlumnoAyudante::query()->findOrFail($id);
        $alumnoAyudante->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Alumno ayudante eliminado correctamente',
            ]);
        }

        return redirect()->route('alumnoayudante')->with('success', 'Alumno ayudante eliminado correctamente');
    }

    public function getProfesoresByAsignatura(int $asignaturaId): JsonResponse
    {
        $profesores = Profesor::query()
            ->whereHas('asignaturas', fn (Builder $query): Builder => $query->where('asignatura_id', $asignaturaId))
            ->get();

        return response()->json($profesores);
    }

    public function getAsignaturasByProfesor(int $profesorId): JsonResponse
    {
        $profesor = Profesor::query()->with('asignaturas')->findOrFail($profesorId);

        return response()->json($profesor->asignaturas);
    }

    /**
     * @return array{Collection<int, array<string, int|float|string>>, Collection<int, array<string, int|float|string>>, Collection<int, array<string, int|float|string>>}
     */
    private function reportes(): array
    {
        $anios = AnioAcademico::query()
            ->with('carrera.facultad')
            ->withCount([
                'alumnos as total_alumnos',
                'alumnos as total_ayudantes' => fn (Builder $query): Builder => $query->whereHas('alumnoAyudante'),
            ])
            ->orderBy('nivel')
            ->get();

        $crearFila = fn (string $nombre, int $ayudantes, int $alumnos): array => [
            'nombre' => $nombre,
            'total' => $ayudantes,
            'alumnos' => $alumnos,
            'porcentaje' => $alumnos > 0 ? round(($ayudantes / $alumnos) * 100, 2) : 0.0,
        ];

        $reportesAnio = $anios->map(fn (AnioAcademico $anio): array => $crearFila(
            $anio->nombre,
            (int) $anio->total_ayudantes,
            (int) $anio->total_alumnos,
        ))->values();

        $reportesCarrera = $anios
            ->filter(fn (AnioAcademico $anio): bool => $anio->carrera !== null)
            ->groupBy('carrera_id')
            ->map(function (Collection $aniosCarrera) use ($crearFila): array {
                $carrera = $aniosCarrera->first()->carrera;

                return $crearFila(
                    $carrera->nombre,
                    (int) $aniosCarrera->sum('total_ayudantes'),
                    (int) $aniosCarrera->sum('total_alumnos'),
                );
            })->sortBy('nombre')->values();

        $reportesFacultad = $anios
            ->filter(fn (AnioAcademico $anio): bool => $anio->carrera?->facultad !== null)
            ->groupBy(fn (AnioAcademico $anio): int => $anio->carrera->facultad_id)
            ->map(function (Collection $aniosFacultad) use ($crearFila): array {
                $facultad = $aniosFacultad->first()->carrera->facultad;

                return $crearFila(
                    $facultad->nombre,
                    (int) $aniosFacultad->sum('total_ayudantes'),
                    (int) $aniosFacultad->sum('total_alumnos'),
                );
            })->sortBy('nombre')->values();

        return [$reportesCarrera, $reportesFacultad, $reportesAnio];
    }
}
