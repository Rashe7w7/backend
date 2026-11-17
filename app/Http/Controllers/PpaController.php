<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePpaAssignmentRequest;
use App\Models\AsignacionPpa;
use App\Models\CursoAcademico;
use App\Models\Grupo;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PpaController extends Controller
{
    public function index(Request $request): View
    {
        $query = AsignacionPpa::query()->with([
            'user', 'cursoAcademico', 'grupo.anioAcademico.carrera.facultad',
        ]);

        if (! $request->user()->isAdministrator()) {
            abort_unless($request->user()->hasRole('ppa'), 403);
            $query->where('user_id', $request->user()->id);
        }

        return view('ppa.index', [
            'asignaciones' => $query->orderByDesc('activo')->orderByDesc('fecha_designacion')->get(),
            'usuariosPpa' => $request->user()->isAdministrator()
                ? User::query()->where('role', 'ppa')->orWhereHas('roles', fn ($role) => $role->where('slug', 'ppa'))->orderBy('name')->get()
                : collect(),
            'grupos' => $request->user()->isAdministrator()
                ? Grupo::query()->with('anioAcademico.carrera')->orderBy('nombre')->get()
                : collect(),
            'cursos' => CursoAcademico::query()->orderByDesc('fecha_inicio')->get(),
        ]);
    }

    public function store(StorePpaAssignmentRequest $request, AuditService $audit): RedirectResponse
    {
        $data = $request->validated();
        $user = User::query()->findOrFail($data['user_id']);
        abort_unless($user->hasRole('ppa'), 422, 'El usuario debe tener el rol PPA.');

        $group = Grupo::query()->findOrFail($data['grupo_id']);
        if ($group->curso_academico_id !== null && (int) $group->curso_academico_id !== (int) $data['curso_academico_id']) {
            return back()->withErrors(['curso_academico_id' => 'El curso no coincide con el curso institucional del grupo.']);
        }

        $assignment = AsignacionPpa::query()->updateOrCreate(
            ['user_id' => $data['user_id'], 'grupo_id' => $data['grupo_id'], 'curso_academico_id' => $data['curso_academico_id']],
            $data,
        );
        $audit->recordModel($assignment->wasRecentlyCreated ? 'ppa.asignado' : 'ppa.actualizado', $assignment);

        return back()->with('success', 'Asignación PPA guardada correctamente.');
    }

    public function students(Request $request, AsignacionPpa $asignacionPpa): View
    {
        abort_unless(
            $request->user()->isAdministrator()
            || ($request->user()->is($asignacionPpa->user) && $asignacionPpa->activo),
            403,
        );

        $asignacionPpa->load('cursoAcademico', 'grupo.anioAcademico.carrera.facultad', 'grupo.alumnos');

        return view('ppa.students', compact('asignacionPpa'));
    }
}
