<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JefeDepartamento extends Model
{
    protected $table = 'jefe_departamento';
    protected $fillable = ['profesor_id', 'departamento_id', 'fecha_inicio', 'fecha_fin'];
    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesor_id');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }
}
