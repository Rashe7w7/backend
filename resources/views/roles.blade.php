<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de roles</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="academic-ui min-h-screen bg-gray-50 text-gray-800">
    @include('partials.academic-nav')
    <main class="mx-auto max-w-7xl space-y-6 p-6">
        <header class="flex items-center justify-between">
            <div><h1 class="text-3xl font-bold">Gestión de roles</h1><p class="text-sm text-gray-500">Administra usuarios y permisos.</p></div>
            <a href="{{ route('dashboard') }}" class="rounded-lg border bg-white px-4 py-2">Volver</a>
        </header>

        @if (session('success'))<div class="rounded-lg bg-green-50 p-3 text-green-700">{{ session('success') }}</div>@endif
        @if ($errors->any())<div class="rounded-lg bg-red-50 p-3 text-red-700">{{ $errors->first() }}</div>@endif

        <section class="rounded-xl border bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold">Crear usuario</h2>
            <form method="POST" action="{{ route('roles.store') }}" class="grid gap-3 md:grid-cols-3">
                @csrf
                <input class="rounded-lg border p-2" name="name" placeholder="Nombre" value="{{ old('name') }}" required>
                <input class="rounded-lg border p-2" name="email" type="email" placeholder="Correo" value="{{ old('email') }}" required>
                <select class="rounded-lg border p-2" name="role" required>@foreach ($roles as $role)<option value="{{ $role }}">{{ str($role)->replace('_', ' ')->title() }}</option>@endforeach</select>
                <select class="rounded-lg border p-2" name="profesor_id"><option value="">Sin profesor asociado</option>@foreach ($profesores as $profesor)<option value="{{ $profesor->id }}">{{ $profesor->nombre }}</option>@endforeach</select>
                <input class="rounded-lg border p-2" name="password" type="password" placeholder="Contraseña (mínimo 8)" required>
                <input class="rounded-lg border p-2" name="password_confirmation" type="password" placeholder="Confirmar contraseña" required>
                <button class="rounded-lg bg-orange-500 px-4 py-2 font-semibold text-white hover:bg-orange-600">Crear usuario</button>
            </form>
        </section>

        <section class="overflow-x-auto rounded-xl border bg-white shadow-sm">
            <table class="min-w-full divide-y">
                <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500"><tr><th class="p-3">Usuario</th><th class="p-3">Profesor</th><th class="p-3">Rol</th><th class="p-3">Acción</th></tr></thead>
                <tbody class="divide-y">
                    @foreach ($users as $user)
                        <tr>
                            <td class="p-3"><strong>{{ $user->name }}</strong><div class="text-sm text-gray-500">{{ $user->email }}</div></td>
                            <form method="POST" action="{{ route('roles.update', $user) }}">
                                @csrf @method('PUT')
                                <td class="p-3"><select class="rounded border p-2" name="profesor_id"><option value="">Sin asociación</option>@foreach ($profesores as $profesor)<option value="{{ $profesor->id }}" @selected($user->profesor_id === $profesor->id)>{{ $profesor->nombre }}</option>@endforeach</select></td>
                                <td class="p-3"><select class="rounded border p-2" name="role">@foreach ($roles as $role)<option value="{{ $role }}" @selected($user->role === $role)>{{ str($role)->replace('_', ' ')->title() }}</option>@endforeach</select></td>
                                <td class="p-3"><button class="rounded bg-gray-900 px-3 py-2 text-sm text-white">Guardar</button></td>
                            </form>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <section class="rounded-xl border bg-white p-6 shadow-sm">
            <h2 class="mb-2 text-lg font-semibold">Alcances académicos explícitos</h2>
            <p class="mb-4 text-sm text-gray-500">Asigna el ámbito de Decano, Rector, Vicerrector o Secretaria. Para Secretaria, habilita expresamente la exportación.</p>
            @foreach($users as $scopeUser)
                @if(in_array($scopeUser->role, ['decano', 'rector', 'vicerrector', 'secretaria'], true))
                    <div class="mb-4 rounded-lg border p-4">
                        <strong>{{ $scopeUser->name }} · {{ str($scopeUser->role)->replace('_', ' ')->title() }}</strong>
                        <form method="POST" action="{{ route('roles.scopes.store', $scopeUser) }}" class="mt-3 grid gap-2 md:grid-cols-4">
                            @csrf
                            <select name="ambito_tipo" class="rounded border p-2"><option value="facultad">Facultad</option><option value="departamento">Departamento</option><option value="carrera">Carrera</option><option value="anno">Año académico</option><option value="grupo">Grupo</option></select>
                            <input name="ambito_id" type="number" min="1" class="rounded border p-2" placeholder="ID de la entidad" required>
                            <select name="puede_exportar" class="rounded border p-2"><option value="0">Sin exportación</option><option value="1">Puede preparar CSV</option></select>
                            <button class="rounded bg-gray-900 px-3 py-2 text-white">Asignar alcance</button>
                        </form>
                        <div class="mt-3 flex flex-wrap gap-2">@foreach($scopeUser->alcancesAcademicos as $scope)<form method="POST" action="{{ route('roles.scopes.destroy', [$scopeUser, $scope]) }}">@csrf @method('DELETE')<button class="rounded-full bg-gray-100 px-3 py-1 text-sm">{{ $scope->ambito_tipo }} #{{ $scope->ambito_id }}{{ $scope->puede_exportar ? ' · CSV' : '' }} ×</button></form>@endforeach</div>
                    </div>
                @endif
            @endforeach
        </section>
    </main>
</body>
</html>
