<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Indicador</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>

<body class="min-h-screen bg-black">

    <!-- Navbar -->
    <nav class="bg-neutral-900 border-b border-orange-500 p-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold text-orange-500"><?php echo e($indicador->nombre); ?></h1>
            <a href="<?php echo e(route('indicadores')); ?>" class="text-gray-400 hover:text-orange-500">Volver</a>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto p-6">

        <!-- Información del Indicador -->
        <div class="bg-neutral-900 rounded-lg p-6 border border-orange-500 mb-6">
            <h2 class="text-xl font-bold text-orange-500 mb-4">Información del Indicador</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-gray-400 text-sm">Nombre</p>
                    <p class="text-white font-semibold"><?php echo e($indicador->nombre); ?></p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Tipo</p>
                    <p class="text-orange-500 font-semibold"><?php echo e(ucfirst(str_replace('_', ' ', $indicador->tipo))); ?></p>
                </div>
            </div>
        </div>

        <!-- Gestión de Categorías del Indicador -->
        <div class="bg-neutral-900 rounded-lg p-6 border border-orange-500">
            <h2 class="text-xl font-bold text-orange-500 mb-4">Categorías Asignadas</h2>

            <!-- Formulario para agregar categorías -->
            <div class="mb-6 p-4 bg-black rounded border border-orange-500">
                <h3 class="text-white font-semibold mb-3">Agregar Categoría</h3>
                <form action="<?php echo e(route('indicadores.addCategoria', $indicador->id)); ?>" method="POST" class="flex gap-2">
                    <?php echo csrf_field(); ?>
                    <select name="categoria_id" class="flex-1 p-2 rounded bg-black border border-orange-500 text-white" required>
                        <option value="">-- Selecciona una categoría --</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categoriasDisponibles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($categoria->id); ?>"><?php echo e($categoria->nombre); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                    <button type="submit" class="bg-orange-500 text-black px-4 py-2 rounded font-semibold hover:bg-orange-600">
                        Agregar
                    </button>
                </form>
            </div>

            <!-- Tabla de categorías asignadas -->
            <div class="rounded border border-orange-500 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-orange-500 text-black">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold">Nombre</th>
                            <th class="px-6 py-3 text-left font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $indicador->categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="border-t border-orange-500 hover:bg-neutral-800">
                            <td class="px-6 py-3 text-gray-300"><?php echo e($categoria->nombre); ?></td>
                            <td class="px-6 py-3">
                                <form action="<?php echo e(route('indicadores.removeCategoria', [$indicador->id, $categoria->id])); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-red-500 hover:text-red-400 text-sm" onclick="return confirm('¿Quitar esta categoría?')">
                                        Quitar
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr class="border-t border-orange-500">
                            <td colspan="2" class="px-6 py-3 text-gray-500 text-center">Sin categorías asignadas</td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>
</html>
<?php /**PATH D:\Trabajo\rachel1\backend\resources\views\indicadores\show.blade.php ENDPATH**/ ?>