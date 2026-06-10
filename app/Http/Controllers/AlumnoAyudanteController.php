<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\AlumnoAyudante;
use App\Models\Asignatura;
use App\Models\Profesor;
use App\Models\Grupo;
use App\Models\AnioAcademico;
use Illuminate\Http\Request;

class AlumnoAyudanteController extends Controller
{
    public function index()
    {
        $alumnos = Alumno::all();
        $asignaturas = Asignatura::all();
        $profesores = Profesor::all();
        $grupos = Grupo::all();
        $cursosAcademicos = \App\Models\CursoAcademico::all();
        
        // Cargar los alumnos ayudantes con sus relaciones
        $alumnosAyudantes = AlumnoAyudante::with([
            'alumno',
            'profesorAsignatura.profesor',
            'profesorAsignatura.asignatura',
            'cursoAcademico'
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
            'curso_academico_id'
        ]));

        return redirect()->route('alumnoayudante')->with('success', 'Alumno ayudante asignado correctamente');
    }

    public function storeFromForm(Request $request)
    {
        try {
            // Log de los datos recibidos
            \Log::info('Datos recibidos en alumnoayudante:', $request->all());

            // Validar que el alumno existe
            $alumno = Alumno::findOrFail($request->alumno_id);
            
            // Validar grupo si se proporciona
            if ($request->grupo_id) {
                $grupo = Grupo::findOrFail($request->grupo_id);
            }
            
            // Obtener asignatura, profesor y curso académico
            $asignatura = Asignatura::findOrFail($request->asignatura_id);
            $profesor = Profesor::findOrFail($request->profesor_id);
            $cursoAcademico = \App\Models\CursoAcademico::findOrFail($request->curso_academico_id);

            // VALIDAR que la relación profesor_asignatura EXISTE
            $profesorAsignatura = \App\Models\ProfesorAsignatura::where('profesor_id', $profesor->id)
                ->where('asignatura_id', $asignatura->id)
                ->first();

            if (!$profesorAsignatura) {
                throw new \Exception("El profesor {$profesor->nombre} no dicta la asignatura {$asignatura->nombre}");
            }
                        // VALIDAR que no exista ya el mismo alumno como ayudante en esta asignatura y año
            $existe = AlumnoAyudante::where('alumno_id', $alumno->id)
                ->where('profesor_asignatura_id', $profesorAsignatura->id)
                ->where('curso_academico_id', $request->curso_academico_id)
                ->exists();

            if ($existe) {
                throw new \Exception("El alumno {$alumno->nombre} ya está registrado como ayudante en la asignatura {$asignatura->nombre} para el período {$cursoAcademico->nombre}");
            }
                        // Crear registro en estudiante_ayudante
            $alumnoAyudante = AlumnoAyudante::create([
                'alumno_id' => $alumno->id,
                'profesor_asignatura_id' => $profesorAsignatura->id,
                'curso_academico_id' => $request->curso_academico_id,
            ]);

            \Log::info('Alumno ayudante creado exitosamente:', ['id' => $alumnoAyudante->id]);
            return response()->json(['success' => true, 'message' => 'Designación guardada correctamente', 'id' => $alumnoAyudante->id]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Error ModelNotFoundException: ' . $e->getMessage());
            return response()->json(['error' => 'Recurso no encontrado: ' . $e->getMessage()], 400);
        } catch (\Exception $e) {
            \Log::error('Error en storeFromForm: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
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
                return response()->json(['error' => $e->getMessage()], 400);
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
