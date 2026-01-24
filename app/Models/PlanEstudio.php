<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanEstudio extends Model
{
    protected $table = 'plan_estudio';
    protected $fillable = ['nombre', 'carrera_id', 'fecha_inicio', 'fecha_fin', 'vigente'];
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'vigente' => 'boolean',
    ];
    public $timestamps = true;

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }

    public function disciplinas()
    {
        return $this->hasMany(Disciplina::class, 'plan_estudio_id');
    }
}
