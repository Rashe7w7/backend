<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndicadorValor extends Model
{
    use HasFactory;

    protected $table = 'indicador_valor';
    protected $fillable = ['indicador_id', 'valor'];

    public function indicador()
    {
        return $this->belongsTo(Indicador::class, 'indicador_id');
    }
}
