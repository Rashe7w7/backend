<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indicador extends Model
{
    use HasFactory;

    protected $table = 'indicador';
    protected $fillable = ['nombre', 'categoria_indicador_id'];

    /**
     * Relación: un indicador pertenece a una categoría
     */
    public function categoria()
    {
        return $this->belongsTo(CategoriaIndicador::class, 'categoria_indicador_id');
    }

    /**
     * Relación: valores del indicador
     */
    public function valores()
    {
        return $this->hasMany(IndicadorValor::class, 'indicador_id');
    }

    // Se eliminaron valoresGrupo/valoresAno/valoresFacultad/valorPorEntidad:
    // apuntaban a modelos inexistentes (IndicadorGrupoValor, IndicadorAnoValor,
    // IndicadorFacultadValor) y no se usaban en ningún sitio.
}
