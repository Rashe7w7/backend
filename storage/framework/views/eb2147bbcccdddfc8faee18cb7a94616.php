

<?php
// Placement (leading or trailing) and the hang into the header's padding live in flux.css...
$classes = Flux::classes()
    ->add('flex items-center gap-2')
    ;
?>

<div <?php echo e($attributes->class($classes)); ?> data-flux-card-actions>
    <?php echo e($slot); ?>

</div>
<?php /**PATH D:\Trabajo\rachel1\backend\vendor\livewire\flux\stubs\resources\views\flux\card\actions.blade.php ENDPATH**/ ?>