<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grupo extends Model
{
    protected $table = 'grupo';

    protected $fillable = ['nombre', 'anio_academico_id', 'curso_academico_id'];

    public $timestamps = true;

    public function anioAcademico(): BelongsTo
    {
        return $this->belongsTo(AnioAcademico::class, 'anio_academico_id');
    }

    public function alumnos(): HasMany
    {
        return $this->hasMany(Alumno::class, 'grupo_id');
    }

    public function cursoAcademico(): BelongsTo
    {
        return $this->belongsTo(CursoAcademico::class, 'curso_academico_id');
    }

    public function asignacionesPpa(): HasMany
    {
        return $this->hasMany(AsignacionPpa::class, 'grupo_id');
    }

    public function getNombreAcademicoAttribute(): string
    {
        $carrera = $this->anioAcademico?->carrera?->nombre ?? 'Carrera sin definir';
        $nivel = $this->anioAcademico?->nivel ?? $this->anioAcademico?->nombre ?? 'sin año';

        return "{$this->nombre} — {$carrera} — {$nivel}.º año";
    }

    public function indicadores()
    {
        return $this->hasMany(IndicadorGrupo::class, 'grupo_id');
    }
}
