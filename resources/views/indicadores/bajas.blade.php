<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indicadores de bajas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="academic-ui min-h-screen bg-gray-50 text-gray-900">
    @include('partials.academic-nav')
    <main class="mx-auto max-w-7xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Indicadores de bajas</h1>
                <p class="text-sm text-gray-500">Resumen y detalle por año académico.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="rounded-lg border px-4 py-2 hover:bg-white">Volver</a>
        </div>

        @isset($error)
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-amber-800">{{ $error }}</div>
        @else
            <form method="GET" class="grid gap-3 rounded-xl bg-white p-4 shadow-sm md:grid-cols-4">
                <select name="anio_academico_id" class="rounded-lg border p-2">
                    @foreach ($aniosAcademicos as $anio)
                        <option value="{{ $anio->id }}" @selected($anioAcademicoActual?->id === $anio->id)>{{ $anio->nombre }}</option>
                    @endforeach
                </select>
                <select name="facultad_id" class="rounded-lg border p-2">
                    <option value="">Todas las facultades</option>
                    @foreach ($facultades as $facultad)
                        <option value="{{ $facultad->id }}" @selected(($selectedFacultad ?? null) == $facultad->id)>{{ $facultad->nombre }}</option>
                    @endforeach
                </select>
                <select name="carrera_id" class="rounded-lg border p-2">
                    <option value="">Todas las carreras</option>
                    @foreach ($carreras as $carrera)
                        <option value="{{ $carrera->id }}" @selected(($selectedCarrera ?? null) == $carrera->id)>{{ $carrera->nombre }}</option>
                    @endforeach
                </select>
                <button class="rounded-lg bg-orange-500 px-4 py-2 font-semibold text-white hover:bg-orange-600">Filtrar</button>
            </form>

            <section class="grid gap-4 md:grid-cols-3">
                <article class="rounded-xl bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">Matrícula inicial</p><strong class="text-3xl">{{ $matriculaInicial }}</strong></article>
                <article class="rounded-xl bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">Total de bajas</p><strong class="text-3xl">{{ $totalBajas }}</strong></article>
                <article class="rounded-xl bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">Porcentaje</p><strong class="text-3xl">{{ $porcentajeBajas }}%</strong></article>
            </section>

            <section class="overflow-hidden rounded-xl bg-white shadow-sm">
                <div class="border-b p-4"><h2 class="font-semibold">Detalle</h2></div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y">
                        <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                            <tr><th class="p-3">Estudiante</th><th class="p-3">Grupo</th><th class="p-3">Causa</th><th class="p-3">Fecha</th></tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($dataBajasDetalle as $baja)
                                <tr><td class="p-3">{{ $baja['alumno_nombre'] }}</td><td class="p-3">{{ $baja['grupo'] }}</td><td class="p-3">{{ $baja['causa'] }}</td><td class="p-3">{{ $baja['fecha'] }}</td></tr>
                            @empty
                                <tr><td colspan="4" class="p-6 text-center text-gray-500">No hay bajas para los filtros seleccionados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endisset
    </main>
</body>
</html>
