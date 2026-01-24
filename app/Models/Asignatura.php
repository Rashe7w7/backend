<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asignatura extends Model
{
    protected $table = 'asignatura';
    protected $fillable = ['nombre', 'disciplina_id', 'codigo', 'horas'];
    public $timestamps = true;

    public function disciplina()
    {
        return $this->belongsTo(Disciplina::class, 'disciplina_id');
    }

    public function curriculos()
    {
        return $this->hasMany(Curriculo::class, 'asignatura_id');
    }

    public function profesores()
    {
        return $this->belongsToMany(Profesor::class, 'profesor_asignatura', 'asignatura_id', 'profesor_id');
    }

    public function aniosAcademicos()
    {
        return $this->belongsToMany(AnioAcademico::class, 'anio_asignatura', 'asignatura_id', 'anio_academico_id');
    }
}
