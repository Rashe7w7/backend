<nav class="academic-nav" aria-label="Navegación principal">
    <div class="academic-nav-inner">
        <a class="academic-brand" href="<?php echo e(route('dashboard')); ?>"><span class="brand-mark" aria-hidden="true">A<span>·</span></span><span>Agenda Semestral<small>GESTIÓN ACADÉMICA</small></span></a>
        <div class="academic-links">
            <a href="<?php echo e(route('dashboard')); ?>" <?php if(request()->routeIs('dashboard')): ?> aria-current="page" <?php endif; ?>>Inicio</a>
            <a href="<?php echo e(route('indicadores')); ?>" <?php if(request()->routeIs('indicadores*')): ?> aria-current="page" <?php endif; ?>>Indicadores</a>
            <a href="<?php echo e(route('planestudio')); ?>" <?php if(request()->routeIs('planestudio')): ?> aria-current="page" <?php endif; ?>>Plan de estudio</a>
            <a href="<?php echo e(route('alumnoayudante')); ?>" <?php if(request()->routeIs('alumnoayudante*')): ?> aria-current="page" <?php endif; ?>>Alumnos ayudantes</a>
        </div>
        <form method="POST" action="<?php echo e(route('logout')); ?>"><?php echo csrf_field(); ?><button class="academic-logout" type="submit">Salir <span aria-hidden="true">↗</span></button></form>
    </div>
</nav>
<?php /**PATH D:\Trabajo\rachel1\backend\resources\views/partials/academic-nav.blade.php ENDPATH**/ ?>