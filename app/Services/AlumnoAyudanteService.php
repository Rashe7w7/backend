<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\AlumnoAyudante;
use App\Models\ProfesorAsignatura;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AlumnoAyudanteService
{
    public function __construct(
        private AcademicScopeService $scopeService,
        private AuditService $auditService,
    ) {}

    /** @param array<string, mixed> $data */
    public function create(User $actor, array $data): AlumnoAyudante
    {
        if (! $actor->isAdministrator() && ! $actor->hasRole('ppa')) {
            abort(403);
        }

        if (! $this->scopeService->canAccess($actor, 'grupo', (int) $data['grupo_id'])) {
            abort(403);
        }

        $student = Alumno::query()->whereKey($data['alumno_id'])->firstOrFail();
        if ((int) $student->grupo_id !== (int) $data['grupo_id']) {
            throw ValidationException::withMessages(['alumno_id' => 'El estudiante no pertenece al grupo seleccionado.']);
        }

        return DB::transaction(function () use ($actor, $data): AlumnoAyudante {
            $conflict = AlumnoAyudante::query()
                ->where('alumno_id', $data['alumno_id'])
                ->where('curso_academico_id', $data['curso_academico_id'])
                ->where('estado', 'activo')
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                throw ValidationException::withMessages([
                    'alumno_id' => 'El estudiante ya tiene una designación activa en este curso académico.',
                ]);
            }

            $profesorAsignatura = ProfesorAsignatura::query()->firstOrCreate([
                'profesor_id' => $data['profesor_id'],
                'asignatura_id' => $data['asignatura_id'],
                'curso_academico_id' => $data['curso_academico_id'],
            ]);

            $assignment = $actor->isAdministrator() ? null : $actor->asignacionesPpa()
                ->where('grupo_id', $data['grupo_id'])
                ->where('curso_academico_id', $data['curso_academico_id'])
                ->where('activo', true)
                ->first();

            if (! $actor->isAdministrator() && $assignment === null) {
                throw ValidationException::withMessages(['grupo_id' => 'No existe una asignación PPA activa para ese grupo y curso.']);
            }

            $assistant = AlumnoAyudante::query()->create([
                'alumno_id' => $data['alumno_id'],
                'profesor_asignatura_id' => $profesorAsignatura->id,
                'curso_academico_id' => $data['curso_academico_id'],
                'grupo_id' => $data['grupo_id'],
                'asignacion_ppa_id' => $assignment?->id,
                'fecha_designacion' => $data['fecha_designacion'],
                'estado' => $data['estado'],
            ]);

            $this->auditService->recordModel('alumno_ayudante.creado', $assistant);

            return $assistant;
        });
    }
}
