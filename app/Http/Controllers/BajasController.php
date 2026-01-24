<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BajasEstudiantes;
use App\Models\AnioAcademico;
use App\Models\Carrera;
use App\Models\Facultad;
use App\Models\Grupo;

class BajasController extends Controller
{
    public function index(Request $request)
    {
        // Obtener año académico actual o del request
        $anioAcademicoId = $request->get('anio_academico_id');
        $facultadId = $request->get('facultad_id');
        $carreraId = $request->get('carrera_id');
        $grupoId = $request->get('grupo_id');
        
        $anioAcademico = null;
        
        if ($anioAcademicoId) {
            $anioAcademico = AnioAcademico::find($anioAcademicoId);
        } else {
            // Obtener el más reciente
            $anioAcademico = AnioAcademico::latest('id')->first();
        }

        if (!$anioAcademico) {
            return view('indicadores.bajas', [
                'error' => 'No hay años académicos registrados',
                'aniosAcademicos' => [],
                'facultades' => [],
                'carreras' => [],
                'grupos' => [],
                'anioAcademicoActual' => null,
                'matriculaInicial' => 0,
                'totalBajas' => 0,
                'porcentajeBajas' => 0,
                'porCausal' => [],
                'dataBajasDetalle' => []
            ]);
        }

        // Obtener todas las bajas asociadas a este año académico
        $query = $anioAcademico->bajas()->with(['alumno', 'alumno.grupo']);
        
        // Filtrar por facultad si se especifica
        if ($facultadId) {
            $query = $query->whereHas('alumno.grupo.anioAcademico.carrera.facultad', function ($q) use ($facultadId) {
                $q->where('facultades.id', $facultadId);
            });
        }
        
        // Filtrar por carrera si se especifica
        if ($carreraId) {
            $query = $query->whereHas('alumno.grupo.anioAcademico.carrera', function ($q) use ($carreraId) {
                $q->where('carreras.id', $carreraId);
            });
        }
        
        // Filtrar por grupo si se especifica
        if ($grupoId) {
            $query = $query->whereHas('alumno.grupo', function ($q) use ($grupoId) {
                $q->where('grupos.id', $grupoId);
            });
        }
        
        $bajas = $query->get();
        
        // Contar matrícula inicial (alumnos del año académico con filtros aplicados)
        $alumnosQuery = $anioAcademico->alumnos();
        
        if ($facultadId) {
            $alumnosQuery = $alumnosQuery->whereHas('grupo.anioAcademico.carrera.facultad', function ($q) use ($facultadId) {
                $q->where('facultades.id', $facultadId);
            });
        }
        
        if ($carreraId) {
            $alumnosQuery = $alumnosQuery->whereHas('grupo.anioAcademico.carrera', function ($q) use ($carreraId) {
                $q->where('carreras.id', $carreraId);
            });
        }
        
        if ($grupoId) {
            $alumnosQuery = $alumnosQuery->whereHas('grupo', function ($q) use ($grupoId) {
                $q->where('grupos.id', $grupoId);
            });
        }
        
        $matriculaInicial = $alumnosQuery->count();
        
        // Total de bajas
        $totalBajas = $bajas->count();
        
        // Porcentaje de bajas
        $porcentajeBajas = $matriculaInicial > 0 ? round(($totalBajas / $matriculaInicial) * 100, 2) : 0;
        
        // Agrupar bajas por causa
        $bajasporCausa = collect();
        if ($totalBajas > 0) {
            $bajasporCausa = $bajas->groupBy('causa')->map(function ($items) use ($totalBajas, $matriculaInicial) {
                $cantidad = $items->count();
                return [
                    'causa' => BajasEstudiantes::CAUSAS[$items->first()->causa] ?? ucwords(str_replace('_', ' ', $items->first()->causa)),
                    'causa_key' => $items->first()->causa,
                    'cantidad' => $cantidad,
                    'porcentaje_matricula' => $matriculaInicial > 0 ? round(($cantidad / $matriculaInicial) * 100, 2) : 0,
                    'porcentaje_bajas' => $totalBajas > 0 ? round(($cantidad / $totalBajas) * 100, 2) : 0,
                ];
            })->sortByDesc('cantidad')->values();
        }

        // Datos detallados de cada baja para la tabla
        $dataBajasDetalle = $bajas->map(function ($baja) {
            return [
                'id' => $baja->id,
                'alumno_nombre' => $baja->alumno->nombre,
                'alumno_email' => $baja->alumno->email,
                'grupo' => $baja->alumno->grupo->nombre,
                'causa' => BajasEstudiantes::CAUSAS[$baja->causa] ?? ucwords(str_replace('_', ' ', $baja->causa)),
                'observaciones' => $baja->observaciones,
                'fecha' => $baja->created_at->format('d/m/Y'),
            ];
        });

        // Obtener todos los años académicos para el filtro
        $aniosAcademicos = AnioAcademico::all();
        
        // Obtener facultades para el filtro
        $facultades = Facultad::all();
        
        // Obtener carreras (filtradas por facultad si se selecciona)
        if ($facultadId) {
            $carreras = Carrera::where('facultad_id', $facultadId)->get();
        } else {
            $carreras = $anioAcademico->carrera->facultad->carreras ?? collect();
        }
        
        // Obtener grupos (filtrados por año académico)
        $grupos = Grupo::where('anio_academico_id', $anioAcademico->id)->get();

        return view('indicadores.bajas', [
            'anioAcademicoActual' => $anioAcademico,
            'aniosAcademicos' => $aniosAcademicos,
            'facultades' => $facultades,
            'carreras' => $carreras,
            'grupos' => $grupos,
            'matriculaInicial' => $matriculaInicial,
            'totalBajas' => $totalBajas,
            'porcentajeBajas' => $porcentajeBajas,
            'porCausal' => $bajasporCausa,
            'dataBajasDetalle' => $dataBajasDetalle,
            'selectedFacultad' => $facultadId,
            'selectedCarrera' => $carreraId,
            'selectedGrupo' => $grupoId,
        ]);
    }
}

