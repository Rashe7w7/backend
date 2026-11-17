<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumnos ayudantes | Agenda Semestral</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="academic-ui min-h-screen">
    <?php echo $__env->make('partials.academic-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="mx-auto max-w-7xl space-y-8 px-6">
        <header>
            <p class="eyebrow">Sistema PPA</p>
            <h1 class="text-3xl font-semibold">Alumnos ayudantes</h1>
            <p class="mt-2 max-w-3xl text-sm text-gray-500">Consulta las designaciones y registra al estudiante en la asignatura específica que apoya.</p>
        </header>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800" role="status"><?php echo e(session('success')); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
                <p class="font-semibold">No se pudo registrar la designación.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <li><?php echo e($error); ?></li>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </ul>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-student-helpers')): ?>
            <section class="bg-white">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold">Nueva designación</h2>
                    <p class="mt-1 text-sm text-gray-500">El curso académico se toma de la asignación docente seleccionada.</p>
                </div>

                <form method="POST" action="<?php echo e(route('alumnoayudante.store')); ?>" class="grid gap-5 lg:grid-cols-[1fr_1.4fr_auto] lg:items-end">
                    <?php echo csrf_field(); ?>
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
                        <input id="alumno_id" name="alumno_id" type="hidden" value="<?php echo e(old('alumno_id')); ?>">
                        <datalist id="alumnos_disponibles">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $alumnos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alumno): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($alumno->nombre); ?> · <?php echo e($alumno->email ?: 'Sin correo'); ?> · <?php echo e($alumno->grupo?->anioAcademico?->carrera?->nombre ?? 'Sin carrera'); ?>"></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </datalist>
                    </div>

                    <div class="grid gap-2">
                        <label for="profesor_asignatura_id">Asignatura de la designación</label>
                        <select id="profesor_asignatura_id" class="px-4" name="profesor_asignatura_id" required>
                            <option value="">Seleccione una asignatura</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $profesorAsignaturas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profesorAsignatura): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($profesorAsignatura->id); ?>" <?php if((int) old('profesor_asignatura_id') === $profesorAsignatura->id): echo 'selected'; endif; ?>>
                                    <?php echo e($profesorAsignatura->asignatura->nombre); ?> · <?php echo e($profesorAsignatura->profesor->nombre); ?> · <?php echo e($profesorAsignatura->cursoAcademico->nombre); ?>

                                </option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                    </div>

                    <button type="submit" class="bg-orange-500 px-5 py-3 font-semibold text-white">Registrar designación</button>
                </form>
            </section>
        <?php endif; ?>

        <section class="overflow-hidden bg-white p-0!">
            <div class="flex flex-wrap items-end justify-between gap-4 border-b p-6">
                <div>
                    <h2 class="text-xl font-semibold">Designaciones registradas</h2>
                    <p class="mt-1 text-sm text-gray-500"><?php echo e($alumnosAyudantes->total()); ?> registros en total.</p>
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
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete-student-helpers')): ?>
                                <th class="text-right">Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $alumnosAyudantes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $designacion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr>
                                <td>
                                    <strong><?php echo e($designacion->alumno->nombre); ?></strong>
                                    <div class="text-xs text-gray-500"><?php echo e($designacion->alumno->email ?: 'Sin correo'); ?></div>
                                </td>
                                <td>
                                    <?php echo e($designacion->alumno->grupo?->anioAcademico?->carrera?->nombre ?? 'Sin carrera'); ?>

                                    <div class="text-xs text-gray-500"><?php echo e($designacion->alumno->grupo?->anioAcademico?->nombre ?? 'Sin año académico'); ?></div>
                                </td>
                                <td>
                                    <?php echo e($designacion->profesorAsignatura->asignatura->nombre); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($designacion->profesorAsignatura->asignatura->codigo): ?>
                                        <div class="text-xs text-gray-500"><?php echo e($designacion->profesorAsignatura->asignatura->codigo); ?></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td><?php echo e($designacion->profesorAsignatura->profesor->nombre); ?></td>
                                <td><?php echo e($designacion->cursoAcademico->nombre); ?></td>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete-student-helpers')): ?>
                                    <td class="text-right">
                                        <form method="POST" action="<?php echo e(route('alumnoayudante.destroy', $designacion->id)); ?>" onsubmit="return confirm('¿Eliminar esta designación de alumno ayudante?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Eliminar</button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr><td colspan="6" class="text-center text-gray-500">No hay alumnos ayudantes registrados.</td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($alumnosAyudantes->hasPages()): ?>
                <div class="border-t p-6"><?php echo e($alumnosAyudantes->links()); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>

        <section class="space-y-6 bg-white">
            <div>
                <p class="eyebrow">Reportes</p>
                <h2 class="text-xl font-semibold">Distribución de alumnos ayudantes</h2>
                <p class="mt-1 text-sm text-gray-500">El porcentaje compara estudiantes ayudantes únicos con el total de estudiantes de cada ámbito académico.</p>
            </div>

            <div class="grid gap-6 xl:grid-cols-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                    'Carrera' => $reportesCarrera,
                    'Facultad' => $reportesFacultad,
                    'Año académico' => $reportesAnio,
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $titulo => $reporte): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="overflow-hidden rounded-xl border border-gray-200">
                        <h3 class="border-b bg-gray-50 px-4 py-3 font-semibold">Por <?php echo e(Str::lower($titulo)); ?></h3>
                        <div class="max-h-96 overflow-auto">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="text-left"><?php echo e($titulo); ?></th>
                                        <th class="text-right">Total</th>
                                        <th class="text-right">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $reporte; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fila): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <tr>
                                            <td><?php echo e($fila['nombre']); ?></td>
                                            <td class="text-right font-semibold"><?php echo e($fila['total']); ?></td>
                                            <td class="text-right"><?php echo e(number_format($fila['porcentaje'], 2, ',', '.')); ?>%</td>
                                        </tr>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        <tr><td colspan="3" class="text-center text-gray-500">Sin datos académicos.</td></tr>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </section>
    </main>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-student-helpers')): ?>
        <script>
            const studentSearch = document.getElementById('alumno_busqueda');
            const studentId = document.getElementById('alumno_id');
            const studentOptions = <?php echo \Illuminate\Support\Js::from($alumnos->mapWithKeys(fn ($alumno) => [
                $alumno->nombre.' · '.($alumno->email ?: 'Sin correo').' · '.($alumno->grupo?->anioAcademico?->carrera?->nombre ?? 'Sin carrera') => $alumno->id,
            ]))->toHtml() ?>;
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
    <?php endif; ?>
</body>
</html>
<?php /**PATH D:\Trabajo\rachel1\backend\resources\views/tables/alumnoayudante.blade.php ENDPATH**/ ?>