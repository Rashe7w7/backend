<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaDocente extends Model
{
    protected $table = 'categoria_docente';
    protected $fillable = ['nombre'];
    
    public function profesoresCategoria()
    {
        return $this->hasMany(ProfesorCategoriaGrado::class, 'categoria_docente_id');
    }
}
