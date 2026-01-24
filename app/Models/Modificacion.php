<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modificacion extends Model
{
    protected $table = 'modificacion';
    protected $fillable = ['tabla', 'registro_id', 'tipo', 'datos_anteriores', 'datos_nuevos', 'usuario_id'];
    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
    ];

    public function versiones()
    {
        return $this->belongsToMany(Version::class, 'version_modificaciones', 'modificacion_id', 'version_id');
    }
}
