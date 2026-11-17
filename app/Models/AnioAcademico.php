<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnioAcademico extends Model
{
    protected $table = 'anio_academico';

    protected $fillable = ['nombre', 'carrera_id', 'nivel'];

    protected function casts(): array
    {
        return [
            'nivel' => 'integer',
        ];
    }

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

    public function bajas()
    {
        return BajaEstudiante::query()->whereHas('alumno.grupo', function ($query) {
            $query->where('anio_academico_id', $this->getKey());
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
