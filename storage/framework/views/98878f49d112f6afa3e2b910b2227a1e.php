<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'mode' => null,
    'label' => null,
    'color' => 'brand',
    'amount' => null,
    'text' => null,
    'value' => null,
    'bg' => 'zinc-950',
    'textColor' => 'white'
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
    'mode' => null,
    'label' => null,
    'color' => 'brand',
    'amount' => null,
    'text' => null,
    'value' => null,
    'bg' => 'zinc-950',
    'textColor' => 'white'
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>





<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php switch($mode):

    
    case ('title'): ?>
        <p class="text-[10px] font-black uppercase tracking-[0.4em] text-<?php echo e($color); ?>-200 dark:text-<?php echo e($color); ?>-300 mb-6">
            <?php echo e($label); ?>

        </p>
    <?php break; ?>

    
    <?php case ('value'): ?>
        <p class="text-7xl md:text-8xl font-black italic tabular-nums tracking-tighter">
            <?php echo e(number_format($amount, 0, ',', ' ')); ?>€
        </p>
    <?php break; ?>

    
    <?php case ('subtext'): ?>
        <p class="text-zinc-500 dark:text-zinc-400 mt-6 text-xs font-black uppercase tracking-widest opacity-70">
            <?php echo e($text); ?>

        </p>
    <?php break; ?>

    
    <?php case ('badge'): ?>
        <div class="mt-8">
            <span class="bg-black/10 dark:bg-white/10 px-6 py-2 rounded-full text-sm font-black backdrop-blur-sm">
                <?php echo e(number_format($amount, 0, ',', ' ')); ?>€
            </span>
        </div>
    <?php break; ?>

    
    <?php case ('record'): ?>
        <div class="flex justify-between items-center mt-3">
            <span class="font-black uppercase"><?php echo e($label); ?></span>
            <span class="font-black"><?php echo e(number_format($value, 0, ',', ' ')); ?>€</span>
        </div>
    <?php break; ?>

    
    <?php case ('card'): ?>
        <div class="p-12 rounded-[2.5rem] min-h-[350px] flex flex-col justify-center text-center shadow-2xl border border-white/5
                    bg-<?php echo e($bg); ?> text-<?php echo e($textColor); ?> transition-all duration-500 backdrop-blur-xl">
            <?php echo e($slot); ?>

        </div>
    <?php break; ?>

<?php endswitch; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views\components\wrapped.blade.php ENDPATH**/ ?>