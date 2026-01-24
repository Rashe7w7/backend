<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $table = 'carrera';
    protected $fillable = ['nombre', 'facultad_id'];
    public $timestamps = true;

    public function facultad()
    {
        return $this->belongsTo(Facultad::class, 'facultad_id');
    }

    public function planesEstudio()
    {
        return $this->hasMany(PlanEstudio::class, 'carrera_id');
    }

    public function departamentos()
    {
        return $this->belongsToMany(Departamento::class, 'carrera_departamento', 'carrera_id', 'departamento_id');
    }

    public function planes()
    {
        return $this->belongsToMany(PlanEstudio::class, 'carrera_plan', 'carrera_id', 'plan_estudio_id');
    }

    public function indicadores()
    {
        return $this->hasMany(IndicadorCarrera::class, 'carrera_id');
    }
}
