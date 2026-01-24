<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfesorAsignatura extends Model
{
    protected $table = 'profesor_asignatura';
    protected $fillable = ['profesor_id', 'asignatura_id', 'curso_academico_id'];

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesor_id');
    }

    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class, 'asignatura_id');
    }

    public function cursoAcademico()
    {
        return $this->belongsTo(CursoAcademico::class, 'curso_academico_id');
    }

    public function alumnosAyudantes()
    {
        return $this->hasMany(AlumnoAyudante::class, 'profesor_asignatura_id');
    }
}
