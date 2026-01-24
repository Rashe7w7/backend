<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlumnoAyudante extends Model
{
    protected $table = 'estudiante_ayudante';
    protected $fillable = ['alumno_id', 'profesor_asignatura_id', 'curso_academico_id'];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    public function profesorAsignatura()
    {
        return $this->belongsTo(ProfesorAsignatura::class, 'profesor_asignatura_id');
    }

    public function cursoAcademico()
    {
        return $this->belongsTo(CursoAcademico::class, 'curso_academico_id');
    }

    public function asignatura()
    {
        return $this->hasOneThrough(Asignatura::class, ProfesorAsignatura::class, 'id', 'id', 'profesor_asignatura_id', 'asignatura_id');
    }

    public function profesor()
    {
        return $this->hasOneThrough(Profesor::class, ProfesorAsignatura::class, 'id', 'id', 'profesor_asignatura_id', 'profesor_id');
    }
}
