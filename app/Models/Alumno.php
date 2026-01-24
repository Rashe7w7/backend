<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alumno extends Model
{
    use SoftDeletes;

    protected $table = 'estudiante';
    protected $fillable = ['nombre', 'email', 'grupo_id', 'carrera'];
    public $timestamps = true;

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }

    public function bajas()
    {
        return $this->hasMany(BajaEstudiante::class, 'alumno_id');
    }

    public function alumnoAyudante()
    {
        return $this->hasMany(EstudianteAyudante::class, 'alumno_id');
    }
}
