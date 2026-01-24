<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disciplina extends Model
{
    protected $table = 'disciplina';
    protected $fillable = ['nombre', 'plan_estudio_id'];
    public $timestamps = true;

    public function planEstudio()
    {
        return $this->belongsTo(PlanEstudio::class, 'plan_estudio_id');
    }

    public function asignaturas()
    {
        return $this->hasMany(Asignatura::class, 'disciplina_id');
    }
}
