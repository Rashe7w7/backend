

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'body' => 'seamless',
    'variant' => 'default',
    'divider' => null,
    'size' => null,
    'highlight' => true,
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
    'body' => 'seamless',
    'variant' => 'default',
    'divider' => null,
    'size' => null,
    'highlight' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
// Divided and separated cards have a white body. Rather than painting the body (which would poke out of this
// card's corners), the card itself is white and its header and footer carry the variant's color instead.
// Filled cards are the exception: with no edge to frame a white body, their fill has to stay whole...
$coloredBands = in_array($body, ['divided', 'separated']) && $variant !== 'filled';

$classes = Flux::classes()
    // Every card has a 1px edge inside its box. In light mode the fill is clipped to the inside of it, so the edge
    // sits over the page and keeps its contrast on any background (see flux.css); muted and soft edges include
    // their fill's tint for that reason. In dark mode the edge lies over the fill instead. Filled has no edge to
    // show. A divided or separated card's white body needs a real edge whatever the variant, so those match the
    // outline variant's weight...
    ->add($variant === 'filled' ? 'border [:where(&)]:border-transparent' : 'border bg-clip-padding dark:bg-clip-border')
    ->add($coloredBands ? '[:where(&)]:border-zinc-900/10 dark:[:where(&)]:border-white/10' : match ($variant) {
        'filled' => '',
        'muted' => '[:where(&)]:border-zinc-900/10 dark:[:where(&)]:border-white/12',
        'soft' => '[:where(&)]:border-zinc-900/7 dark:[:where(&)]:border-white/7',
        'outline' => '[:where(&)]:border-zinc-900/10 dark:[:where(&)]:border-white/15',
        default => '[:where(&)]:border-zinc-900/10 dark:[:where(&)]:border-white/10',
    })
    ->add(in_array($variant, [null, 'default'], true) ? '[:where(&)]:shadow-xs dark:[:where(&)]:shadow-none' : '')
    // A faint highlight just inside the edge, drawn over the card's parts so bands and panels that
    // reach the edge don't cover it. Its corners follow the inside of the edge rather than the outside, and it
    // fades out toward the bottom, like light falling from above. Filled has no edge for it to follow, and dark
    // mode goes without. :highlight="false" turns it off...
    ->add(! $highlight || $variant === 'filled' ? '' : 'relative after:pointer-events-none after:absolute after:inset-0 after:rounded-[calc(var(--flux-card-radius)-1px)] after:inset-ring [:where(&)]:after:inset-ring-white/25 dark:after:hidden after:[mask-image:linear-gradient(to_bottom,black,transparent)]')
    ->add($coloredBands ? '[:where(&)]:bg-white dark:[:where(&)]:bg-white/10' : match ($variant) {
        'filled' => '[:where(&)]:bg-zinc-900/3 dark:[:where(&)]:bg-white/6',
        'muted' => '[:where(&)]:bg-zinc-900/4 dark:[:where(&)]:bg-white/7',
        'soft' => '[:where(&)]:bg-zinc-900/2 dark:[:where(&)]:bg-white/5',
        'outline' => '[:where(&)]:bg-transparent',
        default => '[:where(&)]:bg-white dark:[:where(&)]:bg-white/10',
    })
    ;

// Sizing lives in flux.css, keyed by these. A card with no size is md there. "body-variant" because a plain
// [data-flux-card-body] already means <flux:card.body>...
$attributes = $attributes->merge([
    'data-flux-card-body-variant' => $body,
    'data-flux-card-variant' => $variant ?? 'default',
    'data-flux-card-divider' => $divider,
]);

if ($size) {
    $attributes = $attributes->merge([
        'data-flux-card-size' => $size,
    ]);
}
?>

<div
    <?php echo e($attributes->class($classes)); ?>

    data-flux-card
>
    <?php echo e($slot); ?>

</div>
<?php /**PATH D:\Trabajo\rachel1\backend\vendor\livewire\flux\stubs\resources\views\flux\card\index.blade.php ENDPATH**/ ?>