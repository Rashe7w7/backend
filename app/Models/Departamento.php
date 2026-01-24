<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamento';
    protected $fillable = ['nombre', 'facultad_id'];
    public $timestamps = true;

    public function facultad()
    {
        return $this->belongsTo(Facultad::class, 'facultad_id');
    }

    public function profesoresMiembros()
    {
        return $this->belongsToMany(Profesor::class, 'miembro_departamento', 'departamento_id', 'profesor_id');
    }

    public function jefe()
    {
        return $this->hasOne(JefeDepartamento::class, 'departamento_id');
    }

    public function carreras()
    {
        return $this->belongsToMany(Carrera::class, 'carrera_departamento', 'departamento_id', 'carrera_id');
    }

    public function indicadores()
    {
        return $this->hasMany(IndicadorDepartamento::class, 'departamento_id');
    }
}
