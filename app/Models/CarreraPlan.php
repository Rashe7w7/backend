<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarreraPlan extends Model
{
    protected $table = 'carrera_plan';
    protected $fillable = ['carrera_id', 'plan_estudio_id'];

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }

    public function planEstudio()
    {
        return $this->belongsTo(PlanEstudio::class, 'plan_estudio_id');
    }
}
