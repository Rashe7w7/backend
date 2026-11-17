<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alumno extends Model
{
    use SoftDeletes;

    protected $table = 'estudiante';

    protected $fillable = ['identificador_institucional', 'nombre', 'email', 'grupo_id', 'carrera'];

    public $timestamps = true;

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }

    public function bajas(): HasMany
    {
        return $this->hasMany(BajaEstudiante::class, 'alumno_id');
    }

    public function alumnoAyudante(): HasMany
    {
        return $this->hasMany(AlumnoAyudante::class, 'alumno_id');
    }
}
