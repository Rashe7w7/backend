<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carrera extends Model
{
    protected $table = 'carrera';

    protected $fillable = ['nombre', 'facultad_id', 'duracion_anios'];

    public $timestamps = true;

    public function facultad(): BelongsTo
    {
        return $this->belongsTo(Facultad::class, 'facultad_id');
    }

    public function nivelesAcademicos(): HasMany
    {
        return $this->hasMany(AnioAcademico::class, 'carrera_id')->orderBy('nivel');
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
