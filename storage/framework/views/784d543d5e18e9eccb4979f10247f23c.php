<div class="min-h-screen flex flex-col items-center justify-center bg-zinc-100 dark:bg-zinc-900">

    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl p-10 shadow-xl text-center max-w-md">

        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'building-office','class' => 'size-12 mx-auto text-brand-600 mb-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'building-office','class' => 'size-12 mx-auto text-brand-600 mb-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>

        <h1 class="text-2xl font-black uppercase tracking-tight dark:text-white">
            A carregar workspace...
        </h1>

        <p class="text-zinc-500 dark:text-zinc-400 mt-2">
            Estamos a preparar o teu ambiente de trabalho.
        </p>

        <p class="text-zinc-500 dark:text-zinc-400 mt-1">
            Se isto demorar, escolhe um workspace manualmente.
        </p>

        <a
            href="<?php echo e(route('hub.business.my-profile')); ?>"
            class="mt-6 inline-block px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-black uppercase tracking-widest transition"
        >
            Selecionar Workspace
        </a>
    </div>

</div>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/livewire/dashboard-loading.blade.php ENDPATH**/ ?>