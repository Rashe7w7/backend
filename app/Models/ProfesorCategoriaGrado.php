<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfesorCategoriaGrado extends Model
{
    protected $table = 'profesor_categoria_grado';
    protected $fillable = ['profesor_id', 'categoria_docente_id', 'categoria_cientifica_id', 'grado_titulo_id'];

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesor_id');
    }

    public function categoriaDocente()
    {
        return $this->belongsTo(CategoriaDocente::class, 'categoria_docente_id');
    }

    public function categoriaCientifica()
    {
        return $this->belongsTo(CategoriaCientifica::class, 'categoria_cientifica_id');
    }

    public function gradoTitulo()
    {
        return $this->belongsTo(GradoTitulo::class, 'grado_titulo_id');
    }
}
