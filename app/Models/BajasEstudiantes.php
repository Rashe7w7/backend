<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BajaEstudiante extends Model
{
    protected $table = 'baja_estudiante';
    protected $fillable = ['alumno_id', 'causa', 'observaciones'];
    public $timestamps = true;

    // Mapeo de causas a nombres legibles
    public const CAUSAS = [
        'insuficiencia_docente' => 'Insuficiencia Docente',
        'sancion_disciplinaria' => 'Sanción Disciplinaria',
        'voluntaria' => 'Voluntaria',
        'inasistencia' => 'Inasistencia',
        'desercion' => 'Deserción',
        'definitiva' => 'Definitiva',
        'otras_causas' => 'Otras Causas'
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    public function getCausaFormateada()
    {
        return self::CAUSAS[$this->causa] ?? ucwords(str_replace('_', ' ', $this->causa));
    }
}
