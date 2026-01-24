<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndicadorCurso extends Model
{
    protected $table = 'indicador_curso';
    protected $fillable = ['indicador_id', 'curso_academico_id'];

    public function indicador()
    {
        return $this->belongsTo(Indicador::class, 'indicador_id');
    }

    public function cursoAcademico()
    {
        return $this->belongsTo(CursoAcademico::class, 'curso_academico_id');
    }
}
