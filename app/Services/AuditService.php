<?php

namespace App\Services;

use App\Models\Auditoria;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class AuditService
{
    /** @param array<string, mixed>|null $before @param array<string, mixed>|null $after */
    public function record(
        string $action,
        string $entity,
        string|int|null $entityId = null,
        ?array $before = null,
        ?array $after = null,
        string $result = 'exitoso',
    ): Auditoria {
        return Auditoria::query()->create([
            'user_id' => auth()->id(),
            'accion' => $action,
            'entidad' => $entity,
            'entidad_id' => $entityId,
            'datos_anteriores' => $this->safe($before),
            'datos_nuevos' => $this->safe($after),
            'resultado' => $result,
            'ip' => request()?->ip(),
        ]);
    }

    public function recordModel(string $action, Model $model, ?array $before = null): Auditoria
    {
        return $this->record($action, $model::class, $model->getKey(), $before, $model->attributesToArray());
    }

    /** @param array<string, mixed>|null $values @return array<string, mixed>|null */
    private function safe(?array $values): ?array
    {
        return $values === null ? null : Arr::except($values, [
            'password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes',
        ]);
    }
}
