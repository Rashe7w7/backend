<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = 'grupo';
    protected $fillable = ['nombre', 'anio_academico_id'];
    public $timestamps = true;

    public function anioAcademico()
    {
        return $this->belongsTo(AnioAcademico::class, 'anio_academico_id');
    }

    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'grupo_id');
    }

    public function indicadores()
    {
        return $this->hasMany(IndicadorGrupo::class, 'grupo_id');
    }
}
