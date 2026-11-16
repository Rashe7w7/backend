<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaIndicador extends Model
{
    protected $table = 'categoria_indicador';

    protected $fillable = ['nombre'];

    public $timestamps = true;

    /**
     * Relación: una categoría tiene muchos indicadores
     */
    public function indicadores(): HasMany
    {
        return $this->hasMany(Indicador::class, 'categoria_indicador_id');
    }
}
