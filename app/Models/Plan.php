<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'plan_estudio';
    protected $fillable = ['carrera_id', 'codigo', 'version', 'nombre'];
    public $timestamps = true;

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }
}
