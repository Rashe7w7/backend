<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\CursoAcademico;
use App\Models\Indicador;
use App\Models\IndicadorValor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class IndicadorController extends Controller
{
    public function index(Request $request): View
    {
        $indicadores = Indicador::query()
            ->whereIn('especificacion', $request->user()->allowedIndicatorSpecifications())
            ->get();
        $cursosAcademicos = CursoAcademico::all();
        $carreras = Carrera::all();

        return view('indicadores', compact('indicadores', 'cursosAcademicos', 'carreras'));
    }

    public function storeValores(Request $request): RedirectResponse
    {
        if (! $request->user()->canManageAnyIndicator()) {
            Gate::authorize('manage-indicator', null);
        }

        $validated = $request->validate([
            'valores' => ['required', 'array'],
            'valores.*' => ['required', 'numeric'],
        ]);
        $indicatorIds = array_map('intval', array_keys($validated['valores']));
        $indicators = Indicador::query()->whereKey($indicatorIds)->get()->keyBy('id');

        foreach ($indicatorIds as $indicatorId) {
            $indicator = $indicators->get($indicatorId);
            abort_if($indicator === null, 404);
            Gate::authorize('manage-indicator', $indicator);
        }

        foreach ($validated['valores'] as $indicatorId => $value) {
            IndicadorValor::query()->updateOrCreate(
                ['indicador_id' => $indicatorId],
                ['valor' => $value],
            );
        }

        return redirect()->route('indicadores')->with('success', 'Valores guardados exitosamente');
    }
}
