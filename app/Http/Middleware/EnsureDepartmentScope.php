<?php

namespace App\Http\Middleware;

use App\Models\Alumno;
use App\Models\AlumnoAyudante;
use App\Models\AnioAcademico;
use App\Models\Asignatura;
use App\Models\Carrera;
use App\Models\Disciplina;
use App\Models\Grupo;
use App\Models\PlanEstudio;
use App\Models\Profesor;
use App\Models\ProfesorAsignatura;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

class EnsureDepartmentScope
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->isAdministrator()) {
            return $next($request);
        }

        if ($user?->role !== 'jefe_departamento') {
            abort(403);
        }

        $allowed = $user->departmentIds()->map(fn ($id) => (int) $id);
        $resourceDepartments = $this->resourceDepartments($request);

        if ($resourceDepartments->isEmpty() || $resourceDepartments->intersect($allowed)->count() !== $resourceDepartments->count()) {
            abort(403, 'La operación no pertenece a un departamento bajo su responsabilidad.');
        }

        return $next($request);
    }

    private function resourceDepartments(Request $request): Collection
    {
        $uri = $request->route()->uri();

        if ($uri === 'api/planes-estudio') {
            return $this->forCarrera($request->integer('carrera_id'));
        }

        if ($uri === 'api/disciplinas') {
            return $this->forPlan($request->integer('plan_estudio_id'));
        }

        if ($uri === 'api/asignaturas') {
            return $this->forDisciplina($request->integer('disciplina_id'));
        }

        if ($uri === 'api/curriculos') {
            $assignment = Asignatura::find($request->integer('asignatura_id'));

            return $assignment ? $this->forDisciplina((int) $assignment->disciplina_id) : collect();
        }

        if ($uri === 'api/anios-academicos') {
            return $this->forCarrera($request->integer('carrera_id'));
        }

        if ($uri === 'api/grupos') {
            return $this->forAcademicYear($request->integer('anio_academico_id'));
        }

        if ($uri === 'api/alumnos') {
            return $this->forGroup($request->integer('grupo_id'));
        }

        if ($uri === 'api/alumnoayudante' && $request->isMethod('post')) {
            $profesorAsignatura = ProfesorAsignatura::find($request->integer('profesor_asignatura_id'));

            if (! $profesorAsignatura) {
                return collect();
            }

            return $this->commonDepartments(
                $this->forProfessor((int) $profesorAsignatura->profesor_id),
                $this->forStudent($request->integer('alumno_id')),
            );
        }

        if ($uri === 'api/alumnoayudante/{id}' && $request->isMethod('delete')) {
            $designation = AlumnoAyudante::with('profesorAsignatura')->find($request->route('id'));
            if (! $designation) {
                return collect();
            }

            return $this->commonDepartments(
                $this->forProfessor((int) $designation->profesorAsignatura->profesor_id),
                $this->forStudent((int) $designation->alumno_id),
            );
        }

        // Global resources and parent indicator deletions are administrator-only.
        return collect();
    }

    private function forCarrera(int $id): Collection
    {
        return Carrera::find($id)?->departamentos()->pluck('departamento.id')->map(fn ($value) => (int) $value) ?? collect();
    }

    private function forPlan(int $id): Collection
    {
        $plan = PlanEstudio::find($id);

        return $plan ? $this->forCarrera((int) $plan->carrera_id) : collect();
    }

    private function forDisciplina(int $id): Collection
    {
        $discipline = Disciplina::find($id);

        return $discipline ? $this->forPlan((int) $discipline->plan_estudio_id) : collect();
    }

    private function forAcademicYear(int $id): Collection
    {
        $year = AnioAcademico::find($id);

        return $year ? $this->forCarrera((int) $year->carrera_id) : collect();
    }

    private function forGroup(int $id): Collection
    {
        $group = Grupo::find($id);

        return $group ? $this->forAcademicYear((int) $group->anio_academico_id) : collect();
    }

    private function forStudent(int $id): Collection
    {
        $student = Alumno::find($id);

        return $student?->grupo_id ? $this->forGroup((int) $student->grupo_id) : collect();
    }

    private function forProfessor(int $id): Collection
    {
        return Profesor::find($id)?->departamentos()->pluck('departamento.id')->map(fn ($value) => (int) $value) ?? collect();
    }

    private function commonDepartments(Collection $left, Collection $right): Collection
    {
        return $left->intersect($right)->values();
    }
}
