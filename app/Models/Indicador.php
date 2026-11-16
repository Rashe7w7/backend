<?php

namespace App\Models;

use App\EspecificacionIndicador;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Indicador extends Model
{
    use HasFactory;

    protected $table = 'indicador';

    protected $fillable = ['nombre', 'categoria_indicador_id', 'especificacion'];

    protected function casts(): array
    {
        return [
            'especificacion' => EspecificacionIndicador::class,
        ];
    }

    /**
     * Relación: un indicador pertenece a una categoría
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaIndicador::class, 'categoria_indicador_id');
    }

    /**
     * Relación: valores del indicador
     */
    public function valores(): HasMany
    {
        return $this->hasMany(IndicadorValor::class, 'indicador_id');
    }
}
