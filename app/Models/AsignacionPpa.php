<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsignacionPpa extends Model
{
    protected $table = 'asignacion_ppa';

    protected $fillable = [
        'user_id', 'grupo_id', 'curso_academico_id', 'resolucion_decanal',
        'fecha_designacion', 'activo',
    ];

    protected function casts(): array
    {
        return ['fecha_designacion' => 'date', 'activo' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }

    public function cursoAcademico(): BelongsTo
    {
        return $this->belongsTo(CursoAcademico::class, 'curso_academico_id');
    }
}
