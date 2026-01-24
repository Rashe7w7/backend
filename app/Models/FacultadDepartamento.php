<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacultadDepartamento extends Model
{
    protected $table = 'facultad_departamento';
    protected $fillable = ['facultad_id', 'departamento_id'];

    public function facultad()
    {
        return $this->belongsTo(Facultad::class, 'facultad_id');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }
}
