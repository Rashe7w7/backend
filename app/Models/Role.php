<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    public const DEFINITIONS = [
        'administrador' => 'Administrador',
        'ppa' => 'PPA',
        'jefe_departamento' => 'Jefe de Departamento',
        'rector' => 'Rector',
        'vicerrector' => 'Vicerrector',
        'decano' => 'Decano',
        'secretaria' => 'Secretaria',
    ];

    protected $fillable = ['nombre', 'slug'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
