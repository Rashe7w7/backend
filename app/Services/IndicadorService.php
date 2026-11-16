<?php

namespace App\Services;

use App\Models\Indicador;
use App\Models\IndicadorAnio;
use App\Models\IndicadorCarrera;
use App\Models\IndicadorDepartamento;
use App\Models\IndicadorFacultad;
use App\Models\IndicadorGrupo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class IndicadorService
{
    private const ASSIGNMENTS = [
        'carrera' => [IndicadorCarrera::class, 'carrera_id'],
        'grupo' => [IndicadorGrupo::class, 'grupo_id'],
        'facultad' => [IndicadorFacultad::class, 'facultad_id'],
        'departamento' => [IndicadorDepartamento::class, 'departamento_id'],
        'anno' => [IndicadorAnio::class, 'anio_academico_id'],
    ];

    public function createAndAssign(array $data): Indicador
    {
        return DB::transaction(function () use ($data) {
            $indicador = Indicador::firstOrCreate(
                ['nombre' => $data['nombre']],
                ['categoria_indicador_id' => $data['categoria_indicador']],
            );

            if ((int) $indicador->categoria_indicador_id !== (int) $data['categoria_indicador']) {
                throw ValidationException::withMessages([
                    'categoria_indicador' => 'Ya existe un indicador con este nombre y una categoría diferente.',
                ]);
            }

            [$model, $entityKey] = self::ASSIGNMENTS[$data['especificacion']];
            $attributes = [
                'indicador_id' => $indicador->id,
                $entityKey => $data[$entityKey],
            ];

            if ($data['especificacion'] !== 'facultad') {
                $attributes['curso_academico_id'] = $data['curso_academico_id'];
            }

            if ($model::query()->where($attributes)->exists()) {
                throw ValidationException::withMessages([
                    'nombre' => 'Este indicador ya está asignado a la entidad y curso seleccionados.',
                ]);
            }

            $model::create($attributes + ['nombre' => $indicador->nombre]);
            $indicador->setAttribute('especificacion', $data['especificacion']);

            return $indicador;
        });
    }
}
