<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\CursoAcademico;
use App\Models\Indicador;
use App\Models\IndicadorValor;
use Illuminate\Http\Request;

class IndicadorController extends Controller
{
    public function index()
    {
        // Cargar solo lo necesario; categoría es decorativa y no se persiste
        $indicadores = Indicador::all();
        $cursosAcademicos = CursoAcademico::all();

        // Entidades relacionadas para selección
        $carreras = Carrera::all();

        return view('indicadores', compact('indicadores', 'cursosAcademicos', 'carreras'));
    }

    public function storeValores(Request $request)
    {
        // Validar y guardar cada valor de indicador
        if ($request->has('valores') && is_array($request->valores)) {
            foreach ($request->valores as $indicador_id => $valor) {
                if (! is_numeric($indicador_id) || ! is_numeric($valor)) {
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
