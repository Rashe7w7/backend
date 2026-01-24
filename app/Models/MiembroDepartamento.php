<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MiembroDepartamento extends Model
{
    protected $table = 'miembro_departamento';
    protected $fillable = ['profesor_id', 'departamento_id'];

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesor_id');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }
}
