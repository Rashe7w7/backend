<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indicadores</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="academic-ui min-h-screen bg-gray-50 text-gray-800">
    @include('partials.academic-nav')
    <main class="mx-auto max-w-7xl space-y-6 p-6">
        <header><p class="eyebrow">Gestión académica</p><h1 class="text-3xl font-bold">Indicadores</h1><p>Catálogo reutilizable, aplicaciones académicas y preparación temporal de CSV.</p></header>
        @if (session('success'))<div class="rounded-lg bg-green-50 p-3 text-green-700">{{ session('success') }}</div>@endif
        @if ($errors->any())<div class="rounded-lg bg-red-50 p-3 text-red-700">{{ $errors->first() }}</div>@endif

        @can('create-indicators')
            <section class="rounded-xl border bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold">Nueva definición general</h2>
                <p class="mb-4 text-sm text-gray-500">La entidad académica se selecciona al aplicar el indicador, no al crearlo.</p>
                <form method="POST" action="{{ route('indicadores.store') }}" class="grid gap-3 md:grid-cols-3">
                    @csrf
                    <input name="nombre" class="rounded-lg border p-2" placeholder="Nombre del indicador" required>
                    <select name="categoria_indicador_id" class="rounded-lg border p-2" required><option value="">Categoría</option>@foreach (\App\Models\CategoriaIndicador::query()->orderBy('nombre')->get() as $category)<option value="{{ $category->id }}">{{ $category->nombre }}</option>@endforeach</select>
                    <button class="rounded-lg bg-orange-500 px-4 py-2 font-semibold text-white">Crear definición</button>
                </form>
            </section>
        @endcan

        <section class="rounded-xl border bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-xl font-semibold">Catálogo general</h2>
            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-3">@forelse ($indicadores as $indicator)<article class="rounded-lg border p-4"><strong>{{ $indicator->nombre }}</strong><p class="text-sm text-gray-500">{{ $indicator->categoria?->nombre ?? 'Sin categoría' }}</p></article>@empty<p class="text-gray-500">No hay indicadores definidos.</p>@endforelse</div>
        </section>

        @if(app(\App\Services\AcademicScopeService::class)->allowedApplicationTypes(auth()->user()) !== [])
            <section class="rounded-xl border bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-xl font-semibold">Aplicar indicador</h2>
                <form method="POST" action="{{ route('indicadores.apply') }}" class="grid gap-3 md:grid-cols-4">
                    @csrf
                    <select name="indicador_id" class="rounded-lg border p-2" required><option value="">Indicador</option>@foreach($indicadores as $indicator)<option value="{{ $indicator->id }}">{{ $indicator->nombre }}</option>@endforeach</select>
                    <select name="especificacion" class="rounded-lg border p-2" required>@foreach(app(\App\Services\AcademicScopeService::class)->allowedApplicationTypes(auth()->user()) as $type)<option value="{{ $type }}">{{ str($type)->replace('anno', 'año académico')->title() }}</option>@endforeach</select>
                    <select name="entidad_id" class="rounded-lg border p-2" required><option value="">Entidad autorizada</option>@foreach($applicationEntities as $entity)<option value="{{ $entity['id'] }}" data-type="{{ $entity['type'] }}">{{ str($entity['type'])->replace('anno', 'año')->title() }} — {{ $entity['name'] }}</option>@endforeach</select>
                    <select name="curso_academico_id" class="rounded-lg border p-2"><option value="">Sin curso (facultad)</option>@foreach($cursosAcademicos as $course)<option value="{{ $course->id }}">{{ $course->nombre }}</option>@endforeach</select>
                    <button class="rounded-lg bg-orange-500 px-4 py-2 font-semibold text-white">Aplicar</button>
                </form>
            </section>
        @endif

        <section class="overflow-x-auto rounded-xl border bg-white shadow-sm">
            <div class="p-6"><h2 class="text-xl font-semibold">Indicadores aplicados</h2><p class="text-sm text-gray-500">Cada fila representa una aplicación; la definición se mantiene única.</p></div>
            <table class="min-w-full divide-y"><thead class="bg-gray-50 text-left text-xs uppercase text-gray-500"><tr><th class="p-3">Indicador</th><th class="p-3">Categoría</th><th class="p-3">Especificación</th><th class="p-3">Entidad</th><th class="p-3">Acción</th></tr></thead><tbody class="divide-y">
                @forelse ($applications as $application)
                    @php
                        $type = match (true) { $application instanceof \App\Models\IndicadorGrupo => 'Grupo', $application instanceof \App\Models\IndicadorDepartamento => 'Departamento', $application instanceof \App\Models\IndicadorCarrera => 'Carrera', $application instanceof \App\Models\IndicadorAnio => 'Año académico', default => 'Facultad' };
                        $typeSlug = match ($type) { 'Grupo' => 'grupo', 'Departamento' => 'departamento', 'Carrera' => 'carrera', 'Año académico' => 'anno', default => 'facultad' };
                        $entityId = match ($typeSlug) { 'grupo' => $application->grupo_id, 'departamento' => $application->departamento_id, 'carrera' => $application->carrera_id, 'anno' => $application->anio_academico_id, default => $application->facultad_id };
                        $entity = match ($type) { 'Grupo' => $application->grupo?->nombre_academico, 'Departamento' => $application->departamento?->nombre, 'Carrera' => $application->carrera?->nombre, 'Año académico' => ($application->anioAcademico?->carrera?->nombre.' — '.($application->anioAcademico?->nivel ?? $application->anioAcademico?->nombre)), default => $application->facultad?->nombre };
                    @endphp
                    <tr><td class="p-3">{{ $application->indicador?->nombre }}</td><td class="p-3">{{ $application->indicador?->categoria?->nombre ?? 'Sin categoría' }}</td><td class="p-3">{{ $type }}</td><td class="p-3">{{ $entity ?: 'Sin entidad' }}</td><td class="p-3">@if(app(\App\Services\AcademicScopeService::class)->canAccess(auth()->user(), $typeSlug, (int) $entityId))<form method="POST" action="{{ route('indicadores.applications.destroy', [$typeSlug, $application->id]) }}">@csrf @method('DELETE')<button class="text-red-600 underline">Retirar</button></form>@endif</td></tr>
                @empty<tr><td colspan="5" class="p-6 text-center text-gray-500">No hay aplicaciones.</td></tr>@endforelse
            </tbody></table>
        </section>

        <section class="rounded-xl border bg-white p-6 shadow-sm">
            <h2 class="text-xl font-semibold">Preparar CSV</h2><p class="mb-4 text-sm text-gray-500">Los valores introducidos son temporales y no se guardan.</p>
            <form method="POST" action="{{ route('indicadores.csv') }}" class="space-y-4">
                @csrf
                <div class="grid gap-3 md:grid-cols-3"><select name="especificacion" class="rounded-lg border p-2" required>@foreach(app(\App\Services\AcademicScopeService::class)->allowedApplicationTypes(auth()->user(), true) as $type)<option value="{{ $type }}">{{ str($type)->replace('anno', 'año académico')->title() }}</option>@endforeach</select><select name="entidad_id" class="rounded-lg border p-2" required><option value="">Entidad autorizada</option>@foreach($exportEntities as $entity)<option value="{{ $entity['id'] }}" data-type="{{ $entity['type'] }}">{{ str($entity['type'])->replace('anno', 'año')->title() }} — {{ $entity['name'] }}</option>@endforeach</select><select name="curso_academico_id" class="rounded-lg border p-2"><option value="">Sin curso</option>@foreach($cursosAcademicos as $course)<option value="{{ $course->id }}">{{ $course->nombre }}</option>@endforeach</select></div>
                <div class="grid gap-3 md:grid-cols-2">@foreach($indicadores as $indicator)<label class="flex items-center gap-3 rounded-lg border p-3"><input type="checkbox" name="indicadores[]" value="{{ $indicator->id }}"><span class="grow">{{ $indicator->nombre }}</span><input name="valores[{{ $indicator->id }}]" type="number" step="any" class="w-28 rounded border p-2" placeholder="Valor"></label>@endforeach</div>
                <button class="rounded-lg bg-gray-900 px-5 py-2 font-semibold text-white">Descargar CSV</button>
            </form>
        </section>
    </main>
</body>
</html>
