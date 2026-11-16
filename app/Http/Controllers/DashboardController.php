<?php

namespace App\Http\Controllers;

use App\Models\BajaEstudiante;
use App\Models\Carrera;
use App\Models\CursoAcademico;
use App\Models\Indicador;
use App\Models\IndicadorValor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $values = IndicadorValor::query()->with('indicador')
            ->when($request->integer('indicador_id'), fn ($q, $id) => $q->where('indicador_id', $id))
            ->get();

        return view('dashboard', [
            'indicadores' => Indicador::orderBy('nombre')->get(['id', 'nombre']),
            'cursos' => CursoAcademico::orderByDesc('id')->get(),
            'carreras' => Carrera::orderBy('nombre')->get(),
            'totalIndicadores' => Indicador::count(),
            'totalValores' => $values->count(),
            'promedio' => round((float) $values->avg('valor'), 2),
            'totalBajas' => BajaEstudiante::count(),
            'chartLabels' => $values->pluck('indicador.nombre')->values(),
            'chartValues' => $values->pluck('valor')->map(fn ($value) => (float) $value)->values(),
        ]);
    }
}
