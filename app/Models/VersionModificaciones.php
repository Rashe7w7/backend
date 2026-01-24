<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VersionModificaciones extends Model
{
    protected $table = 'version_modificacion';
    protected $fillable = ['version_id', 'modificacion_id'];

    public function version()
    {
        return $this->belongsTo(Version::class, 'version_id');
    }

    public function modificacion()
    {
        return $this->belongsTo(Modificacion::class, 'modificacion_id');
    }
}
