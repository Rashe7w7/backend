<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveUserRequest;
use App\Models\Profesor;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('roles', [
            'users' => User::with('profesor')->orderBy('name')->get(),
            'profesores' => Profesor::orderBy('nombre')->get(),
            'roles' => User::ROLES,
        ]);
    }

    public function store(SaveUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        User::create($validated);

        return back()->with('success', 'Usuario creado correctamente.');
    }

    public function update(SaveUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        if ($user->is($request->user()) && $validated['role'] !== 'administrador') {
            return back()->withErrors(['role' => 'No puedes quitarte tu propio rol de administrador.']);
        }

        $user->update($validated);

        return back()->with('success', 'Rol actualizado correctamente.');
    }
}
