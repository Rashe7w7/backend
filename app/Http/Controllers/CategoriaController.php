<?php

namespace App\Http\Controllers;

use App\Models\CategoriaIndicador;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|unique:categoria_indicador,nombre',
        ]);

        CategoriaIndicador::create($validated);

        return back()->with('success', 'Categoría creada');
    }

    public function destroy(CategoriaIndicador $categoria): RedirectResponse
    {
        if ($categoria->indicadores()->exists()) {
            return back()->withErrors(['categoria' => 'No se puede eliminar una categoría con indicadores asociados.']);
        }

        $categoria->delete();

        return back()->with('success', 'Categoría eliminada');
    }
}
