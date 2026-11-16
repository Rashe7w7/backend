<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlcanceUsuario extends Model
{
    protected $table = 'alcance_usuario';

    protected $fillable = ['user_id', 'ambito_tipo', 'ambito_id', 'puede_exportar'];

    protected function casts(): array
    {
        return ['puede_exportar' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
