

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'container' => null,
    'inset' => false,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'container' => null,
    'inset' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$classes = Flux::classes('[grid-area:main]')
    ->add($inset ? 'p-6 lg:p-10' : 'p-6 lg:p-8')
    ->add($inset ? 'min-w-0 min-h-0 [:where(&)]:bg-white dark:[:where(&)]:bg-zinc-800 lg:m-2 lg:rounded-xl lg:border lg:[:where(&)]:border-zinc-200 lg:shadow-xs dark:lg:[:where(&)]:border-zinc-700' : '')
    ->add($inset ? 'lg:overflow-y-auto lg:overscroll-contain' : '')
    ->add('[[data-flux-container]_&]:px-0') // If there is a wrapping container, let IT handle the x padding...
    ->add($container && ! $inset ? 'mx-auto w-full [:where(&)]:max-w-7xl' : '')
    ;
?>

<div <?php echo e($attributes->class($classes)); ?> data-flux-main <?php if($inset): ?> data-flux-main-inset <?php endif; ?>>
    <?php if ($container && $inset): ?>
        <div class="mx-auto w-full [:where(&)]:max-w-7xl">
            <?php echo e($slot); ?>

        </div>
    <?php else: ?>
        <?php echo e($slot); ?>

    <?php endif; ?>
</div>
<?php /**PATH D:\Trabajo\rachel1\backend\vendor\livewire\flux\stubs\resources\views\flux\main.blade.php ENDPATH**/ ?>