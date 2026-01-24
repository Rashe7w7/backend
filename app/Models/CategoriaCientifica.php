<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaCientifica extends Model
{
    protected $table = 'categoria_cientifica';
    protected $fillable = ['nombre'];
    
    public function profesoresCategoria()
    {
        return $this->hasMany(ProfesorCategoriaGrado::class, 'categoria_cientifica_id');
    }
}
