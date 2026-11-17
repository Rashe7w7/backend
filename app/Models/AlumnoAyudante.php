<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlumnoAyudante extends Model
{
    protected $table = 'estudiante_ayudante';

    protected $fillable = [
        'alumno_id', 'profesor_asignatura_id', 'curso_academico_id', 'grupo_id',
        'asignacion_ppa_id', 'fecha_designacion', 'estado',
    ];

    protected function casts(): array
    {
        return ['fecha_designacion' => 'date'];
    }

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    public function profesorAsignatura(): BelongsTo
    {
        return $this->belongsTo(ProfesorAsignatura::class, 'profesor_asignatura_id');
    }

    public function cursoAcademico(): BelongsTo
    {
        return $this->belongsTo(CursoAcademico::class, 'curso_academico_id');
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }

    public function asignacionPpa(): BelongsTo
    {
        return $this->belongsTo(AsignacionPpa::class, 'asignacion_ppa_id');
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
