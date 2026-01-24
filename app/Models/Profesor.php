<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profesor extends Model
{
    protected $table = 'profesor';
    protected $fillable = ['nombre', 'email', 'cedula'];

    public function categoriaGrado()
    {
        return $this->hasMany(ProfesorCategoriaGrado::class, 'profesor_id');
    }

    public function departamentos()
    {
        return $this->belongsToMany(Departamento::class, 'miembro_departamento', 'profesor_id', 'departamento_id');
    }

    public function departamentoJefe()
    {
        return $this->hasMany(JefeDepartamento::class, 'profesor_id');
    }

    public function asignaturas()
    {
        return $this->belongsToMany(Asignatura::class, 'profesor_asignatura', 'profesor_id', 'asignatura_id');
    }
}
