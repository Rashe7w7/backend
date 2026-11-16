<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\AlumnoAyudante;
use App\Models\Asignatura;
use App\Models\CursoAcademico;
use App\Models\Grupo;
use App\Models\Profesor;
use App\Models\ProfesorAsignatura;
use Illuminate\Http\Request;

class AlumnoAyudanteController extends Controller
{
    public function index()
    {
        $alumnos = Alumno::all();
        $asignaturas = Asignatura::all();
        $profesores = Profesor::all();
        $grupos = Grupo::all();
        $cursosAcademicos = CursoAcademico::all();

        // Cargar los alumnos ayudantes con sus relaciones
        $alumnosAyudantes = AlumnoAyudante::with([
            'alumno',
            'profesorAsignatura.profesor',
            'profesorAsignatura.asignatura',
            'cursoAcademico',
        ])->get();

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

    public function storeFromForm(Request $request)
    {
        $validated = $request->validate([
            'alumno_id' => ['required', 'integer', 'exists:estudiante,id'],
            'grupo_id' => ['nullable', 'integer', 'exists:grupo,id'],
            'asignatura_id' => ['required', 'integer', 'exists:asignatura,id'],
            'profesor_id' => ['required', 'integer', 'exists:profesor,id'],
            'curso_academico_id' => ['required', 'integer', 'exists:curso_academico,id'],
        ]);

        $profesorAsignatura = ProfesorAsignatura::query()
            ->where('profesor_id', $validated['profesor_id'])
            ->where('asignatura_id', $validated['asignatura_id'])
            ->where('curso_academico_id', $validated['curso_academico_id'])
            ->first();

        if (! $profesorAsignatura) {
            return response()->json([
                'message' => 'El profesor no imparte esa asignatura en el curso seleccionado.',
            ], 422);
        }

        $existe = AlumnoAyudante::query()
            ->where('alumno_id', $validated['alumno_id'])
            ->where('profesor_asignatura_id', $profesorAsignatura->id)
            ->where('curso_academico_id', $validated['curso_academico_id'])
            ->exists();

        if ($existe) {
            return response()->json(['message' => 'La designación ya existe.'], 422);
        }

        $alumnoAyudante = AlumnoAyudante::create([
            'alumno_id' => $validated['alumno_id'],
            'profesor_asignatura_id' => $profesorAsignatura->id,
            'curso_academico_id' => $validated['curso_academico_id'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Designación guardada correctamente',
            'id' => $alumnoAyudante->id,
        ], 201);
    }

    public function destroy($id)
    {
        try {
            $alumnoAyudante = AlumnoAyudante::findOrFail($id);
            $alumnoAyudante->delete();

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
