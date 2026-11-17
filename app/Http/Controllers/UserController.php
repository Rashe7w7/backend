<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveUserRequest;
use App\Http\Requests\StoreUserScopeRequest;
use App\Models\AlcanceUsuario;
use App\Models\Profesor;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('roles', [
            'users' => User::with(['profesor', 'alcancesAcademicos'])->orderBy('name')->get(),
            'profesores' => Profesor::orderBy('nombre')->get(),
            'roles' => User::ROLES,
        ]);
    }

    public function storeScope(StoreUserScopeRequest $request, User $user, AuditService $audit): RedirectResponse
    {
        $scope = $user->alcancesAcademicos()->updateOrCreate(
            ['ambito_tipo' => $request->validated('ambito_tipo'), 'ambito_id' => $request->validated('ambito_id')],
            ['puede_exportar' => $request->boolean('puede_exportar')],
        );
        $audit->recordModel($scope->wasRecentlyCreated ? 'alcance.creado' : 'alcance.actualizado', $scope);

        return back()->with('success', 'Alcance académico guardado.');
    }

    public function destroyScope(User $user, AlcanceUsuario $scope, AuditService $audit): RedirectResponse
    {
        abort_unless(auth()->user()?->isAdministrator() && $scope->user_id === $user->id, 403);
        $before = $scope->attributesToArray();
        $scope->delete();
        $audit->record('alcance.eliminado', AlcanceUsuario::class, $scope->id, $before);

        return back()->with('success', 'Alcance académico eliminado.');
    }

    public function store(SaveUserRequest $request, AuditService $audit): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create($validated);
        $role = Role::query()->where('slug', $validated['role'])->firstOrFail();
        $user->roles()->sync([$role->id]);
        $audit->recordModel('usuario.creado', $user);

        return back()->with('success', 'Usuario creado correctamente.');
    }

    public function update(SaveUserRequest $request, User $user, AuditService $audit): RedirectResponse
    {
        $validated = $request->validated();

        if ($user->is($request->user()) && $validated['role'] !== 'administrador') {
            return back()->withErrors(['role' => 'No puedes quitarte tu propio rol de administrador.']);
        }

        $before = $user->attributesToArray();
        $user->update($validated);
        $role = Role::query()->where('slug', $validated['role'])->firstOrFail();
        $user->roles()->sync([$role->id]);
        $audit->recordModel('usuario.actualizado', $user, $before);

        return back()->with('success', 'Rol actualizado correctamente.');
    }
}
