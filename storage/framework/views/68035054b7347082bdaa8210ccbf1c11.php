<div class="space-y-10 pb-20">

    
    
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">

        
        <div class="flex items-center gap-6">
            <div class="relative group">
                <div class="absolute inset-0 bg-emerald-500/20 blur-2xl rounded-full group-hover:bg-emerald-500/40 transition-all duration-700"></div>
                <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl shadow-emerald-500/10">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'arrow-trending-up','class' => 'w-10 h-10 text-emerald-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-trending-up','class' => 'w-10 h-10 text-emerald-600']); ?>
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
                </div>
            </div>

            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-3xl sm:text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">
                        Gestão de Receitas
                    </h1>

                    <?php if (isset($component)) { $__componentOriginal4cc377eda9b63b796b6668ee7832d023 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4cc377eda9b63b796b6668ee7832d023 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::badge.index','data' => ['variant' => 'success','class' => 'bg-emerald-500/10 text-emerald-600 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'success','class' => 'bg-emerald-500/10 text-emerald-600 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        Cash-In
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4cc377eda9b63b796b6668ee7832d023)): ?>
<?php $attributes = $__attributesOriginal4cc377eda9b63b796b6668ee7832d023; ?>
<?php unset($__attributesOriginal4cc377eda9b63b796b6668ee7832d023); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4cc377eda9b63b796b6668ee7832d023)): ?>
<?php $component = $__componentOriginal4cc377eda9b63b796b6668ee7832d023; ?>
<?php unset($__componentOriginal4cc377eda9b63b796b6668ee7832d023); ?>
<?php endif; ?>
                </div>

                <p class="text-sm text-zinc-500 font-medium italic mt-2">
                    Controlo estratégico de fluxos e rendimentos do grupo
                </p>
            </div>
        </div>

        
        <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">

            

<?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['@click' => '$dispatch(\'modal-show-salario\')','variant' => 'primary','class' => 'rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click' => '$dispatch(\'modal-show-salario\')','variant' => 'primary','class' => 'rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'calendar-days','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'calendar-days','class' => 'size-4']); ?>
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
    Configurar Salário
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $attributes = $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $component = $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>

<div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>


<?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['wire:click' => 'openExtraModal','class' => 'rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'openExtraModal','class' => 'rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    Receita Extra
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $attributes = $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $component = $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>






        </div>
    </div>

    
    
    
    <div class="relative overflow-hidden bg-emerald-600 p-8 sm:p-10 rounded-[2.5rem] shadow-2xl border-none">

        
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 50 Q 25 40 50 50 T 100 50" fill="none" stroke="white" stroke-width="0.5"/>
                <path d="M0 30 Q 25 20 50 30 T 100 30" fill="none" stroke="white" stroke-width="0.5"/>
                <path d="M0 70 Q 25 60 50 70 T 100 70" fill="none" stroke="white" stroke-width="0.5"/>
            </svg>
        </div>

        
        <div class="relative z-10 flex flex-col lg:flex-row justify-between items-center gap-8">

            <div class="text-center lg:text-left space-y-2">
                <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-emerald-100 opacity-80">
                    Total Projetado para <?php echo e(now()->translatedFormat('F')); ?>

                </h3>

                <div class="flex items-baseline justify-center lg:justify-start gap-4">
                    <span class="text-6xl sm:text-7xl font-black text-white tracking-tighter italic">
                        <?php echo e(number_format($totalMonthly, 2, ',', ' ')); ?>

                        <span class="text-3xl">€</span>
                    </span>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taxEstimated > 0): ?>
                    <p class="text-[10px] text-emerald-100/70 font-bold uppercase tracking-widest">
                        Imposto estimado: ~<?php echo e(number_format($taxEstimated, 2, ',', ' ')); ?>€
                        · Líquido: ~<?php echo e(number_format($totalMonthly - $taxEstimated, 2, ',', ' ')); ?>€
                    </p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="hidden lg:block">
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'banknotes','class' => 'size-32 text-white/10 rotate-12']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'banknotes','class' => 'size-32 text-white/10 rotate-12']); ?>
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
            </div>
        </div>
    </div>

    
    
    
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] p-6 shadow-sm">
            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">Média Mensal</p>
            <p class="text-2xl font-black text-emerald-600 tracking-tighter italic">
                <?php echo e(number_format($avgMonthly, 0, ',', ' ')); ?>€
            </p>
            <p class="text-[10px] text-zinc-400 font-medium mt-1">Últimos 6 meses</p>
        </div>

        
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] p-6 shadow-sm">
            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">Melhor Mês</p>
            <p class="text-2xl font-black text-emerald-600 tracking-tighter italic">
                <?php echo e(number_format($bestMonth['total'], 0, ',', ' ')); ?>€
            </p>
            <p class="text-[10px] text-zinc-400 font-medium mt-1"><?php echo e($bestMonth['label']); ?></p>
        </div>

        
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] p-6 shadow-sm">
            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">Total <?php echo e(now()->year); ?></p>
            <p class="text-2xl font-black text-emerald-600 tracking-tighter italic">
                <?php echo e(number_format($totalYear, 0, ',', ' ')); ?>€
            </p>
            <p class="text-[10px] text-zinc-400 font-medium mt-1">Entradas este ano</p>
        </div>

        
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] p-6 shadow-sm">
            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-3">Por Fonte</p>

            <div class="space-y-1.5">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['emprego'=>'💼','freelance'=>'💻','investimento'=>'📈','outro'=>'✨']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $src => $emoji): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($bySource[$src]) && $bySource[$src] > 0): ?>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-zinc-500"><?php echo e($emoji); ?> <?php echo e(ucfirst($src)); ?></span>
                            <span class="text-[10px] font-black text-emerald-600">
                                <?php echo e(number_format($bySource[$src], 0, ',', ' ')); ?>€
                            </span>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bySource->isEmpty()): ?>
                    <p class="text-[10px] text-zinc-400 italic">Sem dados ainda</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    
    
    
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-8 shadow-sm">

        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400 mb-6">
            Evolução dos Últimos 6 Meses
        </p>

        <?php $maxVal = $monthlyTotals->max('total') ?: 1; ?>

        <div class="flex items-end gap-3 h-32">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $monthlyTotals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $height = $maxVal > 0 ? max(4, ($month['total'] / $maxVal) * 100) : 4;
                ?>

                <div class="flex-1 flex flex-col items-center gap-2">
                    <span class="text-[9px] font-black text-zinc-400">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($month['total'] > 0): ?>
                            <?php echo e(number_format($month['total'], 0)); ?>€
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>

                    <div
                        class="w-full rounded-t-xl transition-all
                        <?php echo e($month['label'] === now()->translatedFormat('M')
                            ? 'bg-emerald-600'
                            : 'bg-emerald-200 dark:bg-emerald-900/40'); ?>"
                        style="height: <?php echo e($height); ?>%"
                    ></div>

                    <span class="text-[9px] font-black uppercase text-zinc-400">
                        <?php echo e($month['label']); ?>

                    </span>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

        </div>
    </div>


































    
    
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">






<div class="space-y-6">

    
    <div class="flex items-center justify-between px-2">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'calendar-days','variant' => 'outline','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'calendar-days','variant' => 'outline','class' => 'size-4']); ?>
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
            </div>
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">
                Rendimentos Fixos
            </h2>
        </div>

        <?php if (isset($component)) { $__componentOriginal4cc377eda9b63b796b6668ee7832d023 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4cc377eda9b63b796b6668ee7832d023 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::badge.index','data' => ['variant' => 'neutral','class' => 'bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase px-2 py-0.5 border-none']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'neutral','class' => 'bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase px-2 py-0.5 border-none']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php echo e($fixedIncomes->count()); ?> Ativos
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4cc377eda9b63b796b6668ee7832d023)): ?>
<?php $attributes = $__attributesOriginal4cc377eda9b63b796b6668ee7832d023; ?>
<?php unset($__attributesOriginal4cc377eda9b63b796b6668ee7832d023); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4cc377eda9b63b796b6668ee7832d023)): ?>
<?php $component = $__componentOriginal4cc377eda9b63b796b6668ee7832d023; ?>
<?php unset($__componentOriginal4cc377eda9b63b796b6668ee7832d023); ?>
<?php endif; ?>
    </div>

    
    <div class="space-y-4">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $fixedIncomes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fixed): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] p-6 flex justify-between items-start group transition-all hover:border-emerald-500/40 shadow-sm relative overflow-hidden">

                
                <div class="absolute -right-10 -top-10 size-24 bg-emerald-500/5 blur-2xl rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>

                
                <div class="flex items-start gap-5 relative z-10">
                    
                    <div class="flex flex-col items-center justify-center size-14 rounded-2xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-800 shadow-inner shrink-0">
                        <span class="text-[8px] font-black text-zinc-400 uppercase leading-none mb-1">DIA</span>
                        <span class="text-xl font-black text-emerald-600 leading-none">
                            <?php echo e(sprintf('%02d', $fixed->day_of_month)); ?>

                        </span>
                    </div>

                    
                    <div>
                        <p class="text-sm font-black dark:text-white uppercase tracking-tight">
                            <?php echo e($fixed->description); ?>

                        </p>

                        <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                            <div class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                            <p class="text-[10px] text-zinc-500 uppercase font-black tracking-widest">
                                <?php echo e(ucfirst($fixed->frequency ?? 'mensal')); ?>

                            </p>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fixed->source): ?>
                                <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-500">
                                    <?php echo e(['emprego'=>'💼','freelance'=>'💻','investimento'=>'📈','outro'=>'✨'][$fixed->source] ?? ''); ?>

                                    <?php echo e(ucfirst($fixed->source)); ?>

                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>

                
                <div class="flex items-center gap-3 shrink-0 relative z-10">
                    <span class="text-xl font-black text-emerald-600 tracking-tighter italic mr-2">
                        <?php echo e(number_format($fixed->amount, 2, ',', ' ')); ?>€
                    </span>

                    
                    <button
                        wire:click="openRaiseModal(<?php echo e($fixed->id); ?>)"
                        class="p-2 rounded-xl bg-emerald-500/10 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all opacity-0 group-hover:opacity-100 shadow-sm"
                        title="Recebi um aumento"
                    >
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'rocket-launch','variant' => 'micro','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'rocket-launch','variant' => 'micro','class' => 'size-4']); ?>
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
                    </button>

                    <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['wire:click' => 'editFixed('.e($fixed->id).')','variant' => 'ghost','icon' => 'pencil-square','size' => 'xs','class' => 'opacity-0 group-hover:opacity-100 transition-opacity']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'editFixed('.e($fixed->id).')','variant' => 'ghost','icon' => 'pencil-square','size' => 'xs','class' => 'opacity-0 group-hover:opacity-100 transition-opacity']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $attributes = $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $component = $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['wire:click' => 'deleteFixed('.e($fixed->id).')','wire:confirm' => 'Eliminar rendimento fixo?','variant' => 'ghost','icon' => 'trash','size' => 'xs','color' => 'red','class' => 'opacity-0 group-hover:opacity-100 transition-opacity']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'deleteFixed('.e($fixed->id).')','wire:confirm' => 'Eliminar rendimento fixo?','variant' => 'ghost','icon' => 'trash','size' => 'xs','color' => 'red','class' => 'opacity-0 group-hover:opacity-100 transition-opacity']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $attributes = $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $component = $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="p-16 border-2 border-dashed border-zinc-100 dark:border-zinc-800 rounded-[2.5rem] text-center">
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'clock','class' => 'size-8 text-zinc-200 dark:text-zinc-800 mx-auto mb-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'clock','class' => 'size-8 text-zinc-200 dark:text-zinc-800 mx-auto mb-4']); ?>
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
                <p class="text-zinc-400 font-black uppercase text-[10px] tracking-widest">Sem salários configurados</p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>



<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showRaiseModal): ?>
<div
    x-data="{
        open: false,
        show() { this.open = true; document.documentElement.classList.add('overflow-hidden'); },
        close() { this.open = false; document.documentElement.classList.remove('overflow-hidden'); }
    }"
    x-init="setTimeout(() => show(), 50)" 
    x-on:modal-show-upgrade.window="show()"
    x-on:modal-close-upgrade.window="close()"
    x-on:keydown.escape.window="close()"
>

    
    <div
        x-show="open"
        x-cloak
        x-transition.opacity.duration.120ms
        @click="close()"
        class="fixed inset-0 z-50 bg-zinc-950/80"
    ></div>

    
    <div
        x-show="open"
        x-cloak
        @click.self="close()"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6"
    >

        
        <div
            x-show="open"
            x-transition.scale.duration.120ms
            class="relative z-10 w-full max-w-2xl rounded-[2rem] shadow-xl overflow-hidden transform-gpu
                   bg-emerald-500/10 backdrop-blur-sm border border-emerald-500/20"
            @click.stop
        >

           <form wire:submit.prevent="applyRaise" class="flex max-h-[86vh] flex-col" autocomplete="off">

                
                <div class="text-center mb-8 pt-8 px-6">
                    <div class="size-20 bg-emerald-500/20 rounded-[2rem] flex items-center justify-center mx-auto mb-4 border border-emerald-500/30">
                        <span class="text-4xl animate-bounce">🚀</span>
                    </div>

                    <h2 class="text-2xl font-black uppercase italic tracking-tighter text-white leading-none">
                        Upgrade de Salário!
                    </h2>

                    <p class="text-[10px] text-emerald-300 font-bold uppercase tracking-widest mt-2">
                        Parabéns pelo teu progresso financeiro
                    </p>
                </div>

                
                <div class="space-y-6 px-6 pb-8">

                    
                    <div class="flex bg-white/10 dark:bg-zinc-800/40 p-1 rounded-2xl border border-white/10 backdrop-blur-sm">
                        <button wire:click="$set('raiseMode', 'total')"
                            class="flex-1 py-2 text-[9px] font-black uppercase rounded-xl transition-all
                            <?php echo e($raiseMode === 'total'
                                ? 'bg-white/20 dark:bg-zinc-600 text-emerald-400 shadow-md'
                                : 'text-zinc-400'); ?>">
                            Definir Novo Total
                        </button>

                        <button wire:click="$set('raiseMode', 'addition')"
                            class="flex-1 py-2 text-[9px] font-black uppercase rounded-xl transition-all
                            <?php echo e($raiseMode === 'addition'
                                ? 'bg-white/20 dark:bg-zinc-600 text-emerald-400 shadow-md'
                                : 'text-zinc-400'); ?>">
                            Somar Aumento
                        </button>
                    </div>

                    
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-emerald-300 ml-1">
                            <?php echo e($raiseMode === 'total' ? 'Qual o novo valor líquido?' : 'Quanto vais receber a mais?'); ?>

                        </label>

                        <div class="relative">
                            <input
                                type="number"
                                step="0.01"
                                wire:model="raiseValue"
                                class="w-full h-20 bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-[1.5rem]
                                       px-8 text-3xl font-black text-emerald-400 shadow-inner backdrop-blur-sm
                                       focus:ring-4 focus:ring-emerald-500/20 transition-all text-center"
                                placeholder="0,00"
                                autofocus
                            >
                            <span class="absolute right-6 top-1/2 -translate-y-1/2 text-emerald-300 font-black text-xl">€</span>
                        </div>
                    </div>

                    
                    <div class="flex gap-4 pt-2">
                         <button wire:click="closeRaiseModal"
                         @click="close()"
                            class="flex-1 h-14 rounded-2xl font-black uppercase text-[10px]
                                   text-zinc-400 hover:text-white transition-colors">
                            Cancelar
                        </button>

                        <button wire:click="applyRaise"
                            class="flex-[2] h-14 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-black uppercase
                                   text-[11px] tracking-widest shadow-xl shadow-emerald-500/30 transition-all
                                   hover:scale-[1.02] active:scale-95">
                            Confirmar Upgrade 💪
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
























<br>
        
        
        
        <div class="space-y-6">

            
            <div class="flex items-center justify-between px-2">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-500">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'sparkles','variant' => 'outline','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'sparkles','variant' => 'outline','class' => 'size-4']); ?>
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
                    </div>
                    <h2 class="text-sm font-black uppercase tracking-widest text-zinc-400">
                        Entradas Extras (<?php echo e(now()->translatedFormat('M')); ?>)
                    </h2>
                </div>

                <?php if (isset($component)) { $__componentOriginal4cc377eda9b63b796b6668ee7832d023 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4cc377eda9b63b796b6668ee7832d023 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::badge.index','data' => ['variant' => 'success','class' => 'bg-emerald-500/10 text-emerald-600 text-[10px] font-black uppercase px-2 py-0.5 border-none']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'success','class' => 'bg-emerald-500/10 text-emerald-600 text-[10px] font-black uppercase px-2 py-0.5 border-none']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <?php echo e($extraIncomes->count()); ?> Lançamentos
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4cc377eda9b63b796b6668ee7832d023)): ?>
<?php $attributes = $__attributesOriginal4cc377eda9b63b796b6668ee7832d023; ?>
<?php unset($__attributesOriginal4cc377eda9b63b796b6668ee7832d023); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4cc377eda9b63b796b6668ee7832d023)): ?>
<?php $component = $__componentOriginal4cc377eda9b63b796b6668ee7832d023; ?>
<?php unset($__componentOriginal4cc377eda9b63b796b6668ee7832d023); ?>
<?php endif; ?>
            </div>

            
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden">

                <div class="overflow-x-auto">
                                       <table class="w-full text-left border-collapse">
                        <thead class="bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800">
                            <tr class="text-[9px] uppercase text-zinc-400 font-black tracking-widest">
                                <th class="p-5">Data</th>
                                <th class="p-5">Descrição</th>
                                <th class="p-5">Fonte</th>
                                <th class="p-5 text-right px-8">Valor</th>
                                <th class="p-5 w-10"></th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $extraIncomes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $extra): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <tr class="hover:bg-emerald-50/30 dark:hover:bg-emerald-500/5 transition-all group">
                                    <td class="p-5">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black dark:text-white leading-none">
                                                <?php echo e(\Carbon\Carbon::parse($extra->received_at)->format('d')); ?>

                                            </span>
                                            <span class="text-[9px] font-black text-zinc-400 uppercase mt-1">
                                                <?php echo e(\Carbon\Carbon::parse($extra->received_at)->translatedFormat('M')); ?>

                                            </span>
                                        </div>
                                    </td>

                                    <td class="p-5">
                                        <p class="text-sm font-bold dark:text-white uppercase tracking-tight">
                                            <?php echo e($extra->description); ?>

                                        </p>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($extra->notes): ?>
                                            <p class="text-[10px] text-zinc-400 italic mt-0.5 truncate max-w-[140px] sm:max-w-[180px]">
                                                <?php echo e($extra->notes); ?>

                                            </p>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($extra->tax_estimate): ?>
                                            <p class="text-[9px] text-amber-500 font-bold mt-0.5">
                                                ~<?php echo e($extra->tax_estimate); ?>% imposto
                                            </p>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>

                                    <td class="p-5">
                                        <?php
                                            $sourceMap = [
                                                'emprego'     => ['icon' => '💼', 'color' => 'text-blue-600'],
                                                'freelance'   => ['icon' => '💻', 'color' => 'text-purple-600'],
                                                'investimento'=> ['icon' => '📈', 'color' => 'text-emerald-600'],
                                                'outro'       => ['icon' => '✨', 'color' => 'text-zinc-500'],
                                            ];
                                        ?>

                                        <span class="text-[10px] font-black uppercase px-2 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">
                                            <?php echo e($sourceMap[$extra->source ?? 'outro']['icon'] ?? '✨'); ?>

                                            <?php echo e(ucfirst($extra->source ?? 'outro')); ?>

                                        </span>
                                    </td>

                                    <td class="p-5 text-right px-8">
                                        <span class="text-lg font-black text-emerald-600 tracking-tighter">
                                            +<?php echo e(number_format($extra->amount, 2, ',', ' ')); ?>€
                                        </span>
                                    </td>

                                    <td class="p-5">
                                        <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['wire:click' => 'deleteExtra('.e($extra->id).')','variant' => 'ghost','icon' => 'trash','size' => 'xs','color' => 'red','class' => 'opacity-0 group-hover:opacity-100 transition-opacity']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'deleteExtra('.e($extra->id).')','variant' => 'ghost','icon' => 'trash','size' => 'xs','color' => 'red','class' => 'opacity-0 group-hover:opacity-100 transition-opacity']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $attributes = $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $component = $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>
                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr>
                                    <td colspan="5" class="p-16 sm:p-20 text-center">
                                        <p class="text-zinc-400 font-black uppercase text-[10px] tracking-widest">
                                            Sem ganhos extra este mês
                                        </p>
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>














    </div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showExtraModal): ?>

<div
    x-data="{
        open: false,
        show() {
            requestAnimationFrame(() => {
                this.open = true;
                document.documentElement.classList.add('overflow-hidden');
            });
        },
        close() {
            this.open = false;
            document.documentElement.classList.remove('overflow-hidden');
        }
    }"
    x-on:modal-show-receita-extra.window="show()"
    x-on:modal-close-receita-extra.window="close()"
    x-on:keydown.escape.window="close()"
>

    
    <div
        x-show="open"
        x-cloak
        x-transition.opacity.duration.60ms
        @click="close()"
        class="fixed inset-0 z-50 bg-zinc-950/70 backdrop-blur-md will-change-opacity will-change-transform"
    ></div>

    
    <div
        x-show="open"
        x-cloak
        @click.self="close()"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6"
    >

        
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-90 transform-gpu"
            x-transition:enter-start="opacity-0 scale-[0.92] translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-70 transform-gpu"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-[0.92] translate-y-2"
            class="relative z-10 w-full max-w-2xl rounded-[2rem] shadow-xl overflow-hidden
                   bg-emerald-500/10 backdrop-blur-xl border border-emerald-500/20
                   will-change-transform will-change-opacity"
            @click.stop
        >

            <form wire:submit.prevent="saveExtra" class="flex max-h-[86vh] flex-col" autocomplete="off">

                
                <div class="shrink-0 p-6 pb-4 flex items-center gap-4 border-b border-white/10 bg-white/10 backdrop-blur-sm">
                    <div class="p-3 bg-emerald-600 rounded-2xl text-white shadow-md shadow-emerald-500/20
                                transition-transform duration-150 group-hover:scale-105">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'sparkles','class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'sparkles','class' => 'size-5']); ?>
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
                    </div>

                    <div class="flex-1 min-w-0">
                        <h2 class="font-black uppercase italic tracking-tight leading-none text-white">
                            Receita Extra
                        </h2>
                        <p class="text-[10px] text-emerald-300 font-black uppercase tracking-widest mt-1.5 italic">
                            Regista uma entrada pontual ou recorrente
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="close(); $wire.closeExtraModal()"
                         class="rounded-full p-2 hover:bg-white/10 text-zinc-300 hover:text-white transition-all">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'x-mark','class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'x-mark','class' => 'size-5']); ?>
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
                    </button>
                </div>

                
                <div class="min-h-0 flex-1 overflow-y-auto custom-scrollbar p-6 space-y-6
                            transition-all duration-150 ease-out will-change-scroll">

                    
                    <div class="relative transition-all duration-150 ease-out">
                        <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm
                                       text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                            Origem do Ganho
                        </label>
                        <input
                            type="text"
                            wire:model="description"
                            placeholder="Ex: Freelance, Venda, Bónus..."
                            class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                   text-sm font-bold text-white placeholder-white/40 outline-none
                                   transition-all duration-150 ease-out
                                   focus:ring-2 focus:ring-emerald-500/40 focus:bg-white/20"
                        >
                    </div>

                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div class="relative transition-all duration-150 ease-out">
                            <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm
                                           text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                                Valor (€)
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                wire:model="amount"
                                placeholder="0,00"
                                class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                       text-lg font-black text-emerald-400 placeholder-white/40 outline-none
                                       transition-all duration-150 ease-out
                                       focus:ring-2 focus:ring-emerald-500/40 focus:bg-white/20"
                            >
                        </div>

                        <div class="relative transition-all duration-150 ease-out">
                            <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm
                                           text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                                Data
                            </label>
                            <input
                                type="date"
                                wire:model="received_at"
                                class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                       text-sm font-bold text-white outline-none
                                       transition-all duration-150 ease-out
                                       focus:ring-2 focus:ring-emerald-500/40 focus:bg-white/20"
                            >
                        </div>

                    </div>

                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div class="relative transition-all duration-150 ease-out">
                            <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm
                                           text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                                Fonte
                            </label>
                            <select
                                wire:model="source"
                                class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                       text-sm font-bold text-white outline-none appearance-none
                                       transition-all duration-150 ease-out
                                       focus:ring-2 focus:ring-emerald-500/40 focus:bg-white/20"
                            >
                                <option value="emprego">💼 Emprego</option>
                                <option value="freelance">💻 Freelance</option>
                                <option value="investimento">📈 Investimento</option>
                                <option value="outro">✨ Outro</option>
                            </select>
                        </div>

                        <div class="relative transition-all duration-150 ease-out">
                            <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm
                                           text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                                Frequência
                            </label>
                            <select
                                wire:model="frequency"
                                class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                       text-sm font-bold text-white outline-none appearance-none
                                       transition-all duration-150 ease-out
                                       focus:ring-2 focus:ring-emerald-500/40 focus:bg-white/20"
                            >
                                <option value="pontual">📌 Pontual</option>
                                <option value="semanal">📅 Semanal</option>
                                <option value="mensal">🔁 Mensal</option>
                                <option value="anual">📆 Anual</option>
                            </select>
                        </div>

                    </div>

                    
                    <div class="relative transition-all duration-150 ease-out">
                        <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm
                                       text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                            Imposto Estimado (%)
                        </label>
                        <div class="relative">
                            <input
                                type="number"
                                step="0.1"
                                min="0"
                                max="100"
                                wire:model="tax_estimate"
                                placeholder="Ex: 25 (IRS, IVA...)"
                                class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                       text-sm font-bold text-white placeholder-white/40 outline-none
                                       transition-all duration-150 ease-out
                                       focus:ring-2 focus:ring-amber-500/40 focus:bg-white/20"
                            >
                            <span class="absolute right-5 top-1/2 -translate-y-1/2 text-emerald-300 font-black text-sm">%</span>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tax_estimate && $amount): ?>
                            <p class="text-[10px] text-amber-400 font-bold mt-1.5 transition-all duration-150 ease-out">
                                Imposto estimado: ~<?php echo e(number_format($amount * $tax_estimate / 100, 2, ',', '.')); ?>€
                                · Líquido: ~<?php echo e(number_format($amount - ($amount * $tax_estimate / 100), 2, ',', '.')); ?>€
                            </p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div class="relative transition-all duration-150 ease-out">
                        <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm
                                       text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                            Notas (opcional)
                        </label>
                        <textarea
                            wire:model="notes"
                            rows="2"
                            placeholder="Observações, cliente, referência..."
                            class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                   text-sm font-medium text-white placeholder-white/40 resize-none outline-none
                                   transition-all duration-150 ease-out
                                   focus:ring-2 focus:ring-emerald-500/40 focus:bg-white/20"
                        ></textarea>
                    </div>

                </div>

                
                <div class="shrink-0 p-6 pt-4 flex flex-col sm:flex-row gap-3 border-t border-white/10 bg-white/10 backdrop-blur-sm">
                    <button
                        type="button"
                        @click="close(); $wire.closeExtraModal()"
                        class="w-full h-14 rounded-2xl text-zinc-300 hover:text-white hover:bg-white/10
                               font-bold uppercase text-xs tracking-widest transition-all duration-150 active:scale-95"
                    >
                        Cancelar
                    </button>

                    <button
                        wire:click="saveExtra"
                        @click="close()"
                        wire:loading.attr="disabled"
                        wire:target="saveExtra"
                        class="w-full h-14 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white
                               font-black uppercase tracking-widest shadow-xl shadow-emerald-500/20
                               transition-all duration-150 active:scale-95 disabled:opacity-60"
                    >
                        Confirmar Ganho
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>




<div
    x-data="{
        open: false,
        show() {
            this.open = true;
            document.documentElement.classList.add('overflow-hidden');
        },
        close() {
            this.open = false;
            document.documentElement.classList.remove('overflow-hidden');
        }
    }"
    x-on:modal-show-salario.window="show()"
    x-on:modal-close-salario.window="close()"
    x-on:keydown.escape.window="close()"
>

    
    <div
        x-show="open"
        x-cloak
        x-transition.opacity.duration.120ms
        @click="close()"
        class="fixed inset-0 z-50 bg-zinc-950/80 backdrop-blur-sm"
    ></div>

    
    <div
        x-show="open"
        x-cloak
        @click.self="close()"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6"
    >

        
        <div
            x-show="open"
            x-transition.scale.duration.120ms
            class="relative z-10 w-full max-w-2xl rounded-[2rem] shadow-xl overflow-hidden transform-gpu
                   bg-emerald-500/10 backdrop-blur-sm border border-emerald-500/20"
            @click.stop
        >

            <form wire:submit.prevent="<?php echo e($editingFixedId ? 'updateFixed' : 'saveFixed'); ?>" class="flex max-h-[86vh] flex-col" autocomplete="off">

                
                <div class="shrink-0 p-6 pb-4 flex items-center gap-4 border-b border-white/10 bg-white/10 backdrop-blur-sm">
                    <div class="p-3 bg-emerald-600 rounded-2xl text-white shadow-md shadow-emerald-500/20">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'calendar-days','class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'calendar-days','class' => 'size-5']); ?>
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
                    </div>

                    <div class="flex-1 min-w-0">
                        <h2 class="font-black uppercase italic tracking-tight leading-none text-white">
                            <?php echo e($editingFixedId ? 'Editar Salário' : 'Configurar Salário'); ?>

                        </h2>
                        <p class="text-[10px] text-emerald-300 font-black uppercase tracking-widest mt-1.5 italic">
                            Rendimento que se repete automaticamente
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="close()"
                        class="rounded-full p-2 hover:bg-white/10 text-zinc-300 hover:text-white transition-colors"
                    >
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'x-mark','class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'x-mark','class' => 'size-5']); ?>
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
                    </button>
                </div>

                
                <div class="min-h-0 flex-1 overflow-y-auto custom-scrollbar p-6 space-y-6">

<div class="relative mb-8 text-left">
    <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-md text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
        Vincular a uma Empresa
    </label>
    <div class="relative">
        <select
            wire:model.live="recWorkspaceId"
            class="w-full bg-white/10 dark:bg-zinc-900/40 border border-white/10 rounded-2xl py-4 px-5
                   text-sm font-bold text-white outline-none appearance-none transition-all focus:ring-2 focus:ring-emerald-500/30"
        >
            <option value="" class="bg-white text-zinc-900">Entrada Manual (Individual)</option>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $collabBusinesses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $biz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                
                <option value="<?php echo e($biz->id); ?>" class="bg-white text-zinc-900">
                    🏢 <?php echo e($biz->name); ?>

                </option>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </select>

        <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-emerald-300/50">
            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'chevron-down','variant' => 'micro','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-down','variant' => 'micro','class' => 'size-4']); ?>
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
        </div>
    </div>
    <p class="text-[8px] text-zinc-400 uppercase font-black mt-2 ml-1 tracking-[0.2em]">
        Selecione a empresa onde trabalha para importar os dados do contrato
    </p>
</div>

                    
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                            Identificação
                        </label>
                        <input
                            type="text"
                            wire:model="recDescription"
                            placeholder="Ex: Salário Mensal - Empresa X"
                            class="w-full bg-white/10 dark:bg-zinc-900/20 border <?php $__errorArgs = ['recDescription'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500/60 <?php else: ?> border-white/10 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> rounded-2xl py-4 px-5
                                   text-sm font-bold text-white placeholder-white/40 outline-none transition-all focus:ring-2 focus:ring-emerald-500/30"
                        >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['recDescription'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-400 text-[10px] font-bold mt-1 ml-2"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                                Valor Líquido (€)
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                wire:model="recAmount"
                                placeholder="0,00"
                                class="w-full bg-white/10 dark:bg-zinc-900/20 border <?php $__errorArgs = ['recAmount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500/60 <?php else: ?> border-white/10 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> rounded-2xl py-4 px-5
                                       text-lg font-black text-emerald-400 placeholder-white/40 outline-none transition-all focus:ring-2 focus:ring-emerald-500/30"
                            >
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['recAmount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-400 text-[10px] font-bold mt-1 ml-2"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                                Dia de Recebimento
                            </label>
                            <input
                                type="number"
                                min="1"
                                max="31"
                                wire:model="recDay"
                                placeholder="Ex: 25"
                                class="w-full bg-white/10 dark:bg-zinc-900/20 border <?php $__errorArgs = ['recDay'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500/60 <?php else: ?> border-white/10 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> rounded-2xl py-4 px-5
                                       text-lg font-black text-white text-center outline-none transition-all focus:ring-2 focus:ring-emerald-500/30"
                            >
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['recDay'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-400 text-[10px] font-bold mt-1 ml-2"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                    </div>

                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                                Fonte
                            </label>
                            <select
                                wire:model="recSource"
                                class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                       text-sm font-bold text-white outline-none transition-all focus:ring-2 focus:ring-emerald-500/30 appearance-none"
                            >
                                <option value="emprego">💼 Emprego</option>
                                <option value="freelance">💻 Freelance</option>
                                <option value="investimento">📈 Investimento</option>
                                <option value="outro">✨ Outro</option>
                            </select>
                        </div>

                        <div class="relative">
                            <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                                Frequência
                            </label>
                            <select
                                wire:model="recFrequency"
                                class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                       text-sm font-bold text-white outline-none transition-all focus:ring-2 focus:ring-emerald-500/30 appearance-none"
                            >
                                <option value="semanal">📅 Semanal</option>
                                <option value="mensal">🔁 Mensal</option>
                                <option value="anual">📆 Anual</option>
                            </select>
                        </div>

                    </div>

                    
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                            Imposto Estimado (%)
                        </label>
                        <div class="relative">
                            <input
                                type="number"
                                step="0.1"
                                min="0"
                                max="100"
                                wire:model="recTaxEstimate"
                                placeholder="Ex: 28.5 (IRS)"
                                class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                       text-sm font-bold text-white placeholder-white/40 outline-none transition-all focus:ring-2 focus:ring-amber-500/30"
                            >
                            <span class="absolute right-5 top-1/2 -translate-y-1/2 text-emerald-300 font-black text-sm">%</span>
                        </div>
                    </div>

                    
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-emerald-500/10 backdrop-blur-sm text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                            Notas (opcional)
                        </label>
                        <textarea
                            wire:model="recNotes"
                            rows="2"
                            placeholder="Empresa, contrato, observações..."
                            class="w-full bg-white/10 dark:bg-zinc-900/20 border border-white/10 rounded-2xl py-4 px-5
                                   text-sm font-medium text-white placeholder-white/40 resize-none outline-none transition-all focus:ring-2 focus:ring-emerald-500/30"
                        ></textarea>
                    </div>

                </div>

                <div class="shrink-0 p-6 pt-4 flex flex-col sm:flex-row gap-3 border-t border-white/10 bg-white/10 backdrop-blur-sm">
                    <button
                        type="button"
                        @click="close(); $wire.set('editingFixedId', null)"
                        class="w-full h-14 rounded-2xl text-zinc-300 hover:text-white hover:bg-white/10
                               font-bold uppercase text-xs tracking-widest transition-all active:scale-95"
                    >
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="saveFixed, updateFixed"
                        class="w-full h-14 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white
                               font-black uppercase tracking-widest shadow-xl shadow-emerald-500/20
                               transition-all text-xs active:scale-95 disabled:opacity-60"
                    >
                        
                        <span wire:loading.remove wire:target="saveFixed, updateFixed">
                            <?php echo e($editingFixedId ? 'Guardar Alterações' : 'Confirmar Salário'); ?>

                        </span>

                        <span wire:loading wire:target="saveFixed, updateFixed" class="flex items-center justify-center gap-2">
                            <div class="size-3 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
                            A processar...
                        </span>
                    </button>
                </div>

            </form> 
        </div> 
    </div> 
</div> 

    
    
    
    <footer class="pt-16 sm:pt-20 pb-6 text-center border-t border-zinc-100 dark:border-zinc-800 mt-16 sm:mt-20">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?> · Terminal de Receitas e Fluxo
        </p>
    </footer>

</div> 
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/livewire/income-hub.blade.php ENDPATH**/ ?>