<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndicadorDepartamento extends Model
{
    protected $table = 'indicador_departamento';
    protected $fillable = ['nombre', 'indicador_id', 'departamento_id', 'curso_academico_id'];

    public function indicador()
    {
        return $this->belongsTo(Indicador::class, 'indicador_id');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function cursoAcademico()
    {
        return $this->belongsTo(CursoAcademico::class, 'curso_academico_id');
    }
}
