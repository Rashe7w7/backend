

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['size' => null]));

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

foreach (array_filter((['size' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
// These variants match only a card's own bands. Parts inside a body remain bare.
$classes = Flux::classes()
    ->add('flux-card-divided:flux-card-muted:[:where(&)]:bg-zinc-900/4 flux-card-divided:flux-card-muted:dark:[:where(&)]:bg-black/20')
    ->add('flux-card-divided:flux-card-soft:[:where(&)]:bg-zinc-900/3 flux-card-divided:flux-card-soft:dark:[:where(&)]:bg-black/35')
    ->add('flux-card-separated:flux-card-muted:[:where(&)]:bg-zinc-900/4 flux-card-separated:flux-card-muted:dark:[:where(&)]:bg-black/20')
    ->add('flux-card-separated:flux-card-soft:[:where(&)]:bg-zinc-900/3 flux-card-separated:flux-card-soft:dark:[:where(&)]:bg-black/35')
    ->add('flux-card-separated:flux-card-standard:[:where(&)]:bg-zinc-900/2 flux-card-separated:flux-card-standard:dark:[:where(&)]:bg-black/15')
    ->add('flux-card-separated:flux-card-filled:[:where(&)]:bg-zinc-900/2 flux-card-separated:flux-card-filled:[:where(&)]:ring-zinc-900/2 flux-card-separated:flux-card-filled:dark:[:where(&)]:bg-black/15 flux-card-separated:flux-card-filled:dark:[:where(&)]:ring-black/15 flux-card-separated:flux-card-filled:ring flux-card-separated:flux-card-filled:[clip-path:inset(0_-1px_-1px_-1px)]')
    ->add('flux-card-divided:flux-after-content:border-t flux-card-divided:flux-after-content:[:where(&)]:border-zinc-900/5 flux-card-divided:flux-after-content:dark:[:where(&)]:border-white/10')
    ->add('flux-card-divided:flux-card-divider-inset:flux-after-content:border-transparent flux-card-divided:flux-card-divider-inset:flux-after-content:dark:border-transparent flux-card-divided:flux-card-divider-inset:flux-after-content:relative flux-card-divided:flux-card-divider-inset:flux-after-content:after:absolute flux-card-divided:flux-card-divider-inset:flux-after-content:after:inset-x-[var(--flux-card-part-px)] flux-card-divided:flux-card-divider-inset:flux-after-content:after:-top-px flux-card-divided:flux-card-divider-inset:flux-after-content:after:border-t flux-card-divided:flux-card-divider-inset:flux-after-content:[:where(&)]:after:border-zinc-900/5 flux-card-divided:flux-card-divider-inset:flux-after-content:dark:[:where(&)]:after:border-white/10')
    ;
?>

<div <?php echo e($attributes->class($classes)); ?> data-flux-card-footer data-flux-card-standalone <?php if($size): ?> data-flux-card-size="<?php echo e($size); ?>" <?php endif; ?>>
    <?php echo e($slot); ?>

</div>
<?php /**PATH D:\Trabajo\rachel1\backend\vendor\livewire\flux\stubs\resources\views\flux\card\footer.blade.php ENDPATH**/ ?>