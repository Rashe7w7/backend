<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaIndicador extends Model
{
    protected $table = 'categoria_indicador';
    protected $fillable = ['nombre'];
    public $timestamps = true;

    /**
     * Relación: una categoría tiene muchos indicadores
     */
    public function indicadores()
    {
        return $this->hasMany(Indicador::class, 'categoria_indicador_id');
    }
}
