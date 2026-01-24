<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndicadorFacultad extends Model
{
    protected $table = 'indicador_facultad';
    protected $fillable = ['nombre', 'indicador_id', 'facultad_id', 'curso_academico_id'];

    public function indicador()
    {
        return $this->belongsTo(Indicador::class, 'indicador_id');
    }

    public function facultad()
    {
        return $this->belongsTo(Facultad::class, 'facultad_id');
    }

    public function cursoAcademico()
    {
        return $this->belongsTo(CursoAcademico::class, 'curso_academico_id');
    }
}
