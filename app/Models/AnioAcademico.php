<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnioAcademico extends Model
{
    protected $table = 'anio_academico';
    protected $fillable = ['nombre', 'carrera_id'];
    public $timestamps = true;

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'anio_academico_id');
    }

    public function alumnos()
    {
        return $this->hasManyThrough(
            Alumno::class,
            Grupo::class,
            'anio_academico_id',
            'grupo_id'
        );
    }

    /**
     * Bajas de los estudiantes de los grupos de este año académico.
     *
     * baja_estudiante -> estudiante -> grupo -> anio_academico son dos tablas
     * intermedias, así que no se puede expresar con hasManyThrough (la versión
     * anterior comparaba estudiante.grupo_id con anio_academico.id). Devuelve un
     * query builder, no una relación: usar $anio->bajas()->get(), no $anio->bajas.
     */
    public function bajas()
    {
        return BajaEstudiante::query()->whereHas('alumno.grupo', function ($q) {
            $q->where('anio_academico_id', $this->getKey());
        });
    }

    public function indicadores()
    {
        return $this->hasMany(IndicadorAnio::class, 'anio_academico_id');
    }

    public function asignaturas()
    {
        return $this->belongsToMany(Asignatura::class, 'anio_asignatura', 'anio_academico_id', 'asignatura_id');
    }
}

