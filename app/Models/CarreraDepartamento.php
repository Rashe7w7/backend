<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarreraDepartamento extends Model
{
    protected $table = 'carrera_departamento';
    protected $fillable = ['carrera_id', 'departamento_id'];

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }
}
