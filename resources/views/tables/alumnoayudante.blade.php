<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumnos ayudantes | Agenda Semestral</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="academic-ui min-h-screen">
    @include('partials.academic-nav')

    <main class="mx-auto max-w-7xl space-y-8 px-6">
        <header>
            <p class="eyebrow">Sistema PPA</p>
            <h1 class="text-3xl font-semibold">Alumnos ayudantes</h1>
            <p class="mt-2 max-w-3xl text-sm text-gray-500">Consulta las designaciones y registra al estudiante en la asignatura específica que apoya.</p>
        </header>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800" role="status">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
                <p class="font-semibold">No se pudo registrar la designación.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @can('manage-student-helpers')
            <section class="bg-white">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold">Nueva designación</h2>
                    <p class="mt-1 text-sm text-gray-500">El curso académico se toma de la asignación docente seleccionada.</p>
                </div>

                <form method="POST" action="{{ route('alumnoayudante.store') }}" class="grid gap-5 lg:grid-cols-[1fr_1.4fr_auto] lg:items-end">
                    @csrf
                    <div class="grid gap-2">
                        <label for="alumno_busqueda">Estudiante</label>
                        <input
                            id="alumno_busqueda"
                            class="px-4"
                            type="text"
                            list="alumnos_disponibles"
                            placeholder="Escriba un nombre, correo o carrera"
                            autocomplete="off"
                            role="combobox"
                            aria-controls="alumnos_disponibles"
                            required
                        >
                        <input id="alumno_id" name="alumno_id" type="hidden" value="{{ old('alumno_id') }}">
                        <datalist id="alumnos_disponibles">
                            @foreach ($alumnos as $alumno)
                                <option value="{{ $alumno->nombre }} · {{ $alumno->email ?: 'Sin correo' }} · {{ $alumno->grupo?->anioAcademico?->carrera?->nombre ?? 'Sin carrera' }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    <div class="grid gap-2">
                        <label for="profesor_asignatura_id">Asignatura de la designación</label>
                        <select id="profesor_asignatura_id" class="px-4" name="profesor_asignatura_id" required>
                            <option value="">Seleccione una asignatura</option>
                            @foreach ($profesorAsignaturas as $profesorAsignatura)
                                <option value="{{ $profesorAsignatura->id }}" @selected((int) old('profesor_asignatura_id') === $profesorAsignatura->id)>
                                    {{ $profesorAsignatura->asignatura->nombre }} · {{ $profesorAsignatura->profesor->nombre }} · {{ $profesorAsignatura->cursoAcademico->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="bg-orange-500 px-5 py-3 font-semibold text-white">Registrar designación</button>
                </form>
            </section>
        @endcan

        <section class="overflow-hidden bg-white p-0!">
            <div class="flex flex-wrap items-end justify-between gap-4 border-b p-6">
                <div>
                    <h2 class="text-xl font-semibold">Designaciones registradas</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ $alumnosAyudantes->total() }} registros en total.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table>
                    <thead>
                        <tr>
                            <th class="text-left">Estudiante</th>
                            <th class="text-left">Carrera y año</th>
                            <th class="text-left">Asignatura</th>
                            <th class="text-left">Profesor</th>
                            <th class="text-left">Curso</th>
                            @can('delete-student-helpers')
                                <th class="text-right">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($alumnosAyudantes as $designacion)
                            <tr>
                                <td>
                                    <strong>{{ $designacion->alumno->nombre }}</strong>
                                    <div class="text-xs text-gray-500">{{ $designacion->alumno->email ?: 'Sin correo' }}</div>
                                </td>
                                <td>
                                    {{ $designacion->alumno->grupo?->anioAcademico?->carrera?->nombre ?? 'Sin carrera' }}
                                    <div class="text-xs text-gray-500">{{ $designacion->alumno->grupo?->anioAcademico?->nombre ?? 'Sin año académico' }}</div>
                                </td>
                                <td>
                                    {{ $designacion->profesorAsignatura->asignatura->nombre }}
                                    @if ($designacion->profesorAsignatura->asignatura->codigo)
                                        <div class="text-xs text-gray-500">{{ $designacion->profesorAsignatura->asignatura->codigo }}</div>
                                    @endif
                                </td>
                                <td>{{ $designacion->profesorAsignatura->profesor->nombre }}</td>
                                <td>{{ $designacion->cursoAcademico->nombre }}</td>
                                @can('delete-student-helpers')
                                    <td class="text-right">
                                        <form method="POST" action="{{ route('alumnoayudante.destroy', $designacion->id) }}" onsubmit="return confirm('¿Eliminar esta designación de alumno ayudante?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Eliminar</button>
                                        </form>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-gray-500">No hay alumnos ayudantes registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($alumnosAyudantes->hasPages())
                <div class="border-t p-6">{{ $alumnosAyudantes->links() }}</div>
            @endif
        </section>

        <section class="space-y-6 bg-white">
            <div>
                <p class="eyebrow">Reportes</p>
                <h2 class="text-xl font-semibold">Distribución de alumnos ayudantes</h2>
                <p class="mt-1 text-sm text-gray-500">El porcentaje compara estudiantes ayudantes únicos con el total de estudiantes de cada ámbito académico.</p>
            </div>

            <div class="grid gap-6 xl:grid-cols-3">
                @foreach ([
                    'Carrera' => $reportesCarrera,
                    'Facultad' => $reportesFacultad,
                    'Año académico' => $reportesAnio,
                ] as $titulo => $reporte)
                    <div class="overflow-hidden rounded-xl border border-gray-200">
                        <h3 class="border-b bg-gray-50 px-4 py-3 font-semibold">Por {{ Str::lower($titulo) }}</h3>
                        <div class="max-h-96 overflow-auto">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="text-left">{{ $titulo }}</th>
                                        <th class="text-right">Total</th>
                                        <th class="text-right">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($reporte as $fila)
                                        <tr>
                                            <td>{{ $fila['nombre'] }}</td>
                                            <td class="text-right font-semibold">{{ $fila['total'] }}</td>
                                            <td class="text-right">{{ number_format($fila['porcentaje'], 2, ',', '.') }}%</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-gray-500">Sin datos académicos.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </main>

    @can('manage-student-helpers')
        <script>
            const studentSearch = document.getElementById('alumno_busqueda');
            const studentId = document.getElementById('alumno_id');
            const studentOptions = @js($alumnos->mapWithKeys(fn ($alumno) => [
                $alumno->nombre.' · '.($alumno->email ?: 'Sin correo').' · '.($alumno->grupo?->anioAcademico?->carrera?->nombre ?? 'Sin carrera') => $alumno->id,
            ]));
            const selectedStudent = Object.entries(studentOptions).find(([, id]) => Number(id) === Number(studentId.value));

            if (selectedStudent) {
                studentSearch.value = selectedStudent[0];
            }

            const syncSelectedStudent = () => {
                const selectedId = studentOptions[studentSearch.value] ?? '';

                studentId.value = selectedId;
                studentSearch.setCustomValidity(selectedId ? '' : 'Seleccione un estudiante de la lista.');
            };

            studentSearch.addEventListener('input', syncSelectedStudent);
            studentSearch.form.addEventListener('submit', syncSelectedStudent);
        </script>
    @endcan
</body>
</html>
