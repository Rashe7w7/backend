<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnioAsignatura extends Model
{
    protected $table = 'anio_asignatura';
    protected $fillable = ['anio_academico_id', 'asignatura_id'];
    public $timestamps = true;

    public function anioAcademico()
    {
        return $this->belongsTo(AnioAcademico::class, 'anio_academico_id');
    }

    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class, 'asignatura_id');
    }
}
