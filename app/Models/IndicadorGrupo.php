<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndicadorGrupo extends Model
{
    protected $table = 'indicador_grupo';
    protected $fillable = ['nombre', 'indicador_id', 'grupo_id', 'curso_academico_id'];

    public function indicador()
    {
        return $this->belongsTo(Indicador::class, 'indicador_id');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }

    public function cursoAcademico()
    {
        return $this->belongsTo(CursoAcademico::class, 'curso_academico_id');
    }
}
