<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profesores Principales de Año</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="academic-ui min-h-screen bg-gray-50 text-gray-800">
    @include('partials.academic-nav')
    <main class="mx-auto max-w-7xl space-y-6 p-6">
        <header><p class="eyebrow">Gestión académica</p><h1 class="text-3xl font-bold">Profesores Principales de Año</h1><p>Asignaciones explícitas por grupo y curso académico.</p></header>
        @if (session('success'))<div class="rounded-lg bg-green-50 p-3 text-green-700">{{ session('success') }}</div>@endif
        @if ($errors->any())<div class="rounded-lg bg-red-50 p-3 text-red-700">{{ $errors->first() }}</div>@endif

        @can('manage-users')
            <section class="rounded-xl border bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold">Asignar PPA</h2>
                <form method="POST" action="{{ route('ppa.store') }}" class="grid gap-3 md:grid-cols-3">
                    @csrf
                    <select name="user_id" class="rounded-lg border p-2" required><option value="">Profesor o usuario PPA</option>@foreach ($usuariosPpa as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select>
                    <select name="grupo_id" class="rounded-lg border p-2" required><option value="">Grupo</option>@foreach ($grupos as $grupo)<option value="{{ $grupo->id }}">{{ $grupo->nombre_academico }}</option>@endforeach</select>
                    <select name="curso_academico_id" class="rounded-lg border p-2" required><option value="">Curso académico</option>@foreach ($cursos as $curso)<option value="{{ $curso->id }}">{{ $curso->nombre }}</option>@endforeach</select>
                    <input name="resolucion_decanal" class="rounded-lg border p-2" placeholder="Resolución decanal (opcional)">
                    <input name="fecha_designacion" type="date" value="{{ now()->toDateString() }}" class="rounded-lg border p-2" required>
                    <select name="activo" class="rounded-lg border p-2"><option value="1">Activa</option><option value="0">Inactiva</option></select>
                    <button class="rounded-lg bg-orange-500 px-4 py-2 font-semibold text-white">Guardar asignación</button>
                </form>
            </section>
        @endcan

        <section class="overflow-x-auto rounded-xl border bg-white shadow-sm">
            <table class="min-w-full divide-y"><thead class="bg-gray-50 text-left text-xs uppercase text-gray-500"><tr><th class="p-3">PPA</th><th class="p-3">Grupo</th><th class="p-3">Curso</th><th class="p-3">Resolución</th><th class="p-3">Estado</th><th class="p-3">Acción</th></tr></thead>
                <tbody class="divide-y">@forelse ($asignaciones as $assignment)<tr><td class="p-3">{{ $assignment->user->name }}</td><td class="p-3">{{ $assignment->grupo->nombre_academico }}</td><td class="p-3">{{ $assignment->cursoAcademico->nombre }}</td><td class="p-3">{{ $assignment->resolucion_decanal ?: '—' }}</td><td class="p-3">{{ $assignment->activo ? 'Activa' : 'Inactiva' }}</td><td class="p-3"><a class="text-orange-600 underline" href="{{ route('ppa.students', $assignment) }}">Ver estudiantes</a></td></tr>@empty<tr><td colspan="6" class="p-6 text-center text-gray-500">No hay asignaciones PPA.</td></tr>@endforelse</tbody>
            </table>
        </section>
    </main>
</body>
</html>
