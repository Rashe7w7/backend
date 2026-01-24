<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndicadorAnio extends Model
{
    protected $table = 'indicador_anno';
    protected $fillable = ['nombre', 'indicador_id', 'anio_academico_id', 'curso_academico_id'];

    public function indicador()
    {
        return $this->belongsTo(Indicador::class, 'indicador_id');
    }

    public function anioAcademico()
    {
        return $this->belongsTo(AnioAcademico::class, 'anio_academico_id');
    }

    public function cursoAcademico()
    {
        return $this->belongsTo(CursoAcademico::class, 'curso_academico_id');
    }
}
