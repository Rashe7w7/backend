<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facultad extends Model
{
    protected $table = 'facultad';
    protected $fillable = ['nombre'];
    public $timestamps = true;

    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'facultad_id');
    }

    public function carreras()
    {
        return $this->hasMany(Carrera::class, 'facultad_id');
    }

    public function indicadores()
    {
        return $this->hasMany(IndicadorFacultad::class, 'facultad_id');
    }
}
