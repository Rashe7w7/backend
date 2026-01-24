<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    protected $table = 'version';
    protected $fillable = ['nombre', 'descripcion', 'creada_por'];

    public function modificaciones()
    {
        return $this->belongsToMany(Modificacion::class, 'version_modificaciones', 'version_id', 'modificacion_id');
    }
}
