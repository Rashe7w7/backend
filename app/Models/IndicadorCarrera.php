<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndicadorCarrera extends Model
{
    protected $table = 'indicador_carrera';
    protected $fillable = ['nombre', 'indicador_id', 'carrera_id', 'curso_academico_id'];

    public function indicador()
    {
        return $this->belongsTo(Indicador::class, 'indicador_id');
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }

    public function cursoAcademico()
    {
        return $this->belongsTo(CursoAcademico::class, 'curso_academico_id');
    }
}
