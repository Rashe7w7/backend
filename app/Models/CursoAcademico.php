<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CursoAcademico extends Model
{
    protected $table = 'curso_academico';
    protected $fillable = ['nombre', 'fecha_inicio', 'fecha_fin', 'activo'];
    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'activo' => 'boolean',
    ];

    public function indicadores()
    {
        return $this->hasMany(IndicadorCurso::class, 'curso_academico_id');
    }

    public function indicadorFacultades()
    {
        return $this->hasMany(IndicadorFacultad::class, 'curso_academico_id');
    }

    public function indicadorDepartamentos()
    {
        return $this->hasMany(IndicadorDepartamento::class, 'curso_academico_id');
    }

    public function indicadorCarreras()
    {
        return $this->hasMany(IndicadorCarrera::class, 'curso_academico_id');
    }

    public function indicadorGrupos()
    {
        return $this->hasMany(IndicadorGrupo::class, 'curso_academico_id');
    }

    public function indicadorAnios()
    {
        return $this->hasMany(IndicadorAnio::class, 'curso_academico_id');
    }
}
