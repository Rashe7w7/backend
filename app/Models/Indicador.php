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

    /**
     * Relación: valores por grupo
     */
    public function valoresGrupo()
    {
        return $this->hasMany(IndicadorGrupoValor::class, 'indicador_id');
    }

    /**
     * Relación: valores por año académico
     */
    public function valoresAno()
    {
        return $this->hasMany(IndicadorAnoValor::class, 'indicador_id');
    }

    /**
     * Relación: valores por facultad
     */
    public function valoresFacultad()
    {
        return $this->hasMany(IndicadorFacultadValor::class, 'indicador_id');
    }

    /**
     * Función auxiliar: devuelve valor según tipo de entidad y su id
     */
    public function valorPorEntidad(string $tipo, int $entidadId)
    {
        switch ($tipo) {
            case 'grupo':
                return $this->valoresGrupo()->where('grupo_id', $entidadId)->first();
            case 'anno':
                return $this->valoresAno()->where('anio_id', $entidadId)->first();
            case 'facultad':
                return $this->valoresFacultad()->where('facultad_id', $entidadId)->first();
            default:
                return null;
        }
    }
}
