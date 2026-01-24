<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curriculo extends Model
{
    protected $table = 'curriculo';
    protected $fillable = ['nombre', 'asignatura_id'];
    public $timestamps = true;

    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class, 'asignatura_id');
    }
}
