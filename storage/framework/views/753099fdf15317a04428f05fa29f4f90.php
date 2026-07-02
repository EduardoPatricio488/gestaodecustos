<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    <?php echo e(filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel')); ?>

</title>

<!-- SÍMBOLO NOVO (SACO DE DINHEIRO) -->
<link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>💰</text></svg>">

<?php echo app('Illuminate\Foundation\Vite')->fonts(); ?>

<?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
<?php echo app('flux')->fluxAppearance(); ?>

<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/partials/head.blade.php ENDPATH**/ ?>