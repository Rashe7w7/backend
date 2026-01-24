<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndicadorCategoria extends Model
{
    protected $table = 'indicador_categoria';
    protected $fillable = ['indicador_id', 'categoria_indicador_id'];

    public function indicador()
    {
        return $this->belongsTo(Indicador::class, 'indicador_id');
    }

    public function categoriaIndicador()
    {
        return $this->belongsTo(CategoriaIndicador::class, 'categoria_indicador_id');
    }
}
