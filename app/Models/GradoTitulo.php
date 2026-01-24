<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradoTitulo extends Model
{
    protected $table = 'grado_titulo';
    protected $fillable = ['nombre'];
    
    public function profesoresGrado()
    {
        return $this->hasMany(ProfesorCategoriaGrado::class, 'grado_titulo_id');
    }
}
