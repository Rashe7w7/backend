<?php

namespace App\Services;

use App\Models\AnioAcademico;
use App\Models\Carrera;
use App\Models\Departamento;
use App\Models\Facultad;
use App\Models\Grupo;
use App\Models\User;

class AcademicScopeService
{
    /** @return array<int, array{type:string,id:int,name:string}> */
    public function entityOptions(User $user, bool $forExport = false): array
    {
        $options = [];

        foreach ($this->allowedApplicationTypes($user, $forExport) as $type) {
            $entities = match ($type) {
                'grupo' => Grupo::query()->with('anioAcademico.carrera')->get()->map(fn (Grupo $group) => [$group->id, $group->nombre_academico]),
                'departamento' => Departamento::query()->orderBy('nombre')->get()->map(fn ($department) => [$department->id, $department->nombre]),
                'carrera' => Carrera::query()->orderBy('nombre')->get()->map(fn ($career) => [$career->id, $career->nombre]),
                'anno' => AnioAcademico::query()->with('carrera')->get()->map(fn ($level) => [$level->id, $level->carrera->nombre.' — '.($level->nivel ?? $level->nombre).'.º año']),
                'facultad' => Facultad::query()->orderBy('nombre')->get()->map(fn ($faculty) => [$faculty->id, $faculty->nombre]),
                default => collect(),
            };

            foreach ($entities as [$id, $name]) {
                if ($this->canAccess($user, $type, (int) $id, $forExport)) {
                    $options[] = ['type' => $type, 'id' => (int) $id, 'name' => (string) $name];
                }
            }
        }

        return $options;
    }

    /** @return list<string> */
    public function allowedApplicationTypes(User $user, bool $forExport = false): array
    {
        if ($user->isAdministrator()) {
            return ['grupo', 'departamento', 'carrera', 'anno', 'facultad'];
        }

        if ($user->hasRole('secretaria')) {
            return $forExport
                ? $user->alcancesAcademicos()->where('puede_exportar', true)->distinct()->pluck('ambito_tipo')->all()
                : [];
        }

        return match (true) {
            $user->hasRole('ppa') => ['grupo'],
            $user->hasRole('jefe_departamento') => ['grupo', 'departamento'],
            $user->hasRole('decano') => ['carrera', 'anno'],
            $user->hasRole('rector'), $user->hasRole('vicerrector') => ['facultad'],
            default => [],
        };
    }

    public function canAccess(User $user, string $type, int $entityId, bool $forExport = false): bool
    {
        if (! in_array($type, $this->allowedApplicationTypes($user, $forExport), true)) {
            return false;
        }

        if ($user->isAdministrator()) {
            return true;
        }

        if ($user->hasRole('secretaria')) {
            return $user->alcancesAcademicos()
                ->where('ambito_tipo', $type)
                ->where('ambito_id', $entityId)
                ->where('puede_exportar', true)
                ->exists();
        }

        if ($user->hasRole('ppa')) {
            return $type === 'grupo' && $user->asignacionesPpa()
                ->where('grupo_id', $entityId)
                ->where('activo', true)
                ->exists();
        }

        if ($user->hasRole('jefe_departamento')) {
            if ($type === 'departamento') {
                return $user->departmentIds()->contains($entityId);
            }

            if ($type === 'grupo') {
                return Grupo::query()->whereKey($entityId)
                    ->whereHas('anioAcademico.carrera.departamentos', fn ($query) => $query->whereIn('departamento.id', $user->departmentIds()))
                    ->exists();
            }
        }

        return $this->matchesExplicitScope($user, $type, $entityId);
    }

    private function matchesExplicitScope(User $user, string $type, int $entityId): bool
    {
        if ($user->alcancesAcademicos()->where('ambito_tipo', $type)->where('ambito_id', $entityId)->exists()) {
            return true;
        }

        $facultyId = match ($type) {
            'facultad' => $entityId,
            'carrera' => Carrera::query()->whereKey($entityId)->value('facultad_id'),
            'anno' => AnioAcademico::query()->whereKey($entityId)->with('carrera:id,facultad_id')->first()?->carrera?->facultad_id,
            default => null,
        };

        return $facultyId !== null && $user->alcancesAcademicos()
            ->where('ambito_tipo', 'facultad')
            ->where('ambito_id', $facultyId)
            ->exists();
    }
}
