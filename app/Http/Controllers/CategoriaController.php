<?php

namespace App\Http\Controllers;

use App\Models\CategoriaIndicador;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|unique:categoria_indicador,nombre',
        ]);

        CategoriaIndicador::create($validated);

        return back()->with('success', 'Categoría creada');
    }

    public function destroy(CategoriaIndicador $categoria)
    {
        $categoria->delete();
        return back()->with('success', 'Categoría eliminada');
    }
}
