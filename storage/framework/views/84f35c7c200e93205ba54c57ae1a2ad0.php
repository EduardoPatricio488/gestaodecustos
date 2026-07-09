<div class="space-y-8 pb-20 p-4 lg:p-10">

    
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 px-2">
        <div class="flex items-center gap-4 sm:gap-6">
            <div class="p-4 sm:p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-xl shadow-indigo-500/10">
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'chart-bar-square','class' => 'w-8 h-8 sm:w-10 sm:h-10 text-indigo-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chart-bar-square','class' => 'w-8 h-8 sm:w-10 sm:h-10 text-indigo-600']); ?>
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
            <div class="min-w-0">
                <h1 class="text-3xl sm:text-4xl font-black uppercase italic tracking-tighter leading-none text-zinc-900 dark:text-white">
                    Investimentos
                </h1>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lastUpdated): ?>
                    <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mt-1 italic">
                        Update: <?php echo e($lastUpdated); ?>

                    </p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 w-full lg:w-auto justify-end">
            <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['wire:click' => 'refreshPrices','variant' => 'ghost','class' => 'w-full sm:w-auto rounded-xl border border-zinc-200 dark:border-zinc-800 h-11 sm:h-12 text-[10px] font-black uppercase tracking-widest']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'refreshPrices','variant' => 'ghost','class' => 'w-full sm:w-auto rounded-xl border border-zinc-200 dark:border-zinc-800 h-11 sm:h-12 text-[10px] font-black uppercase tracking-widest']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'arrow-path','class' => 'size-4 mr-2 '.e($isRefreshing ? 'animate-spin' : '').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-path','class' => 'size-4 mr-2 '.e($isRefreshing ? 'animate-spin' : '').'']); ?>
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
                Atualizar Preços
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['@click' => 'open = true; $wire.createAsset()','variant' => 'primary','icon' => 'plus','class' => 'w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 rounded-2xl px-6 sm:px-8 font-black uppercase shadow-lg shadow-indigo-500/20 h-11 sm:h-12 transition-all hover:scale-[1.02]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click' => 'open = true; $wire.createAsset()','variant' => 'primary','icon' => 'plus','class' => 'w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 rounded-2xl px-6 sm:px-8 font-black uppercase shadow-lg shadow-indigo-500/20 h-11 sm:h-12 transition-all hover:scale-[1.02]']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Novo Ativo
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

            <button
                wire:click="toggleNetValues"
                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                    'w-full sm:w-auto flex items-center justify-center sm:justify-start gap-2 px-4 h-11 sm:h-12 rounded-2xl border transition-all font-black uppercase text-[9px] tracking-widest',
                    'bg-emerald-500/10 border-emerald-500 text-emerald-600 shadow-lg shadow-emerald-500/10' => $showNetValues,
                    'bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 text-zinc-400' => !$showNetValues
                ]); ?>">
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => ''.e($showNetValues ? 'eye' : 'eye-slash').'','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($showNetValues ? 'eye' : 'eye-slash').'','class' => 'size-4']); ?>
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
                <?php echo e($showNetValues ? 'Ver Valor Líquido (Pós-IRS)' : 'Simular Saída (IRS 28%)'); ?>

            </button>
        </div>
    </div>

    
    <div class="relative overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 py-3">

        
        <div class="pointer-events-none absolute inset-y-0 left-0 w-12 bg-gradient-to-r from-white dark:from-zinc-900 to-transparent z-10"></div>
        <div class="pointer-events-none absolute inset-y-0 right-0 w-12 bg-gradient-to-l from-white dark:from-zinc-900 to-transparent z-10"></div>

        
        <div class="absolute top-1/2 -translate-y-1/2 left-3 z-20 flex items-center gap-1.5 bg-white/90 dark:bg-zinc-900/90 backdrop-blur-sm pr-2 rounded-full pl-2 py-1">
            <span class="relative flex size-2">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-500 opacity-75"></span>
                <span class="relative inline-flex size-2 rounded-full bg-emerald-500"></span>
            </span>
            <span class="text-[9px] font-black uppercase tracking-widest text-emerald-500">Live</span>
        </div>

        
        <div class="flex w-max animate-[ticker_40s_linear_infinite] gap-4 pl-24 hover:[animation-play-state:paused]">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < 2; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $marketData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticker => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php $isUp = str_contains($data['change'], '+'); ?>
                    <div
                        <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'ticker-'.e($ticker).'-'.e($i).''; ?>wire:key="ticker-<?php echo e($ticker); ?>-<?php echo e($i); ?>"
                        class="flex-shrink-0 flex items-center gap-3 px-3 sm:px-4 border-r border-zinc-100 dark:border-zinc-800 last:border-0">
                        <div class="flex items-center gap-2">
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => ''.e($isUp ? 'arrow-trending-up' : 'arrow-trending-down').'','class' => 'size-4 '.e($isUp ? 'text-emerald-500' : 'text-red-500').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($isUp ? 'arrow-trending-up' : 'arrow-trending-down').'','class' => 'size-4 '.e($isUp ? 'text-emerald-500' : 'text-red-500').'']); ?>
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
                            <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">
                                <?php echo e($ticker); ?>

                            </span>
                        </div>
                        <p class="text-xs sm:text-sm font-black dark:text-white italic tracking-tighter">
                            <?php echo e($data['price']); ?> €
                        </p>
                        <span class="text-[10px] font-black <?php echo e($isUp ? 'text-emerald-500' : 'text-red-500'); ?>">
                            <?php echo e($data['change']); ?>

                        </span>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </div>

    <?php if (! $__env->hasRenderedOnce('2f9b587a-99a1-4ed7-aa27-390ea8913653')): $__env->markAsRenderedOnce('2f9b587a-99a1-4ed7-aa27-390ea8913653'); ?>
        <style>
            @keyframes ticker {
                from { transform: translateX(0); }
                to { transform: translateX(-50%); }
            }
        </style>
    <?php endif; ?>

<div class="flex justify-center mt-12 mb-6">
    <div class="inline-flex bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-lg shadow-indigo-500/10 p-1">

        <button wire:click="switchTab('portfolio')"
            class="px-6 py-3 text-sm font-black uppercase tracking-widest rounded-xl transition-all
                <?php echo e($tab === 'portfolio'
                    ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/30 scale-[1.03]'
                    : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300'); ?>">
            Portefólio
        </button>

        <button wire:click="switchTab('analysis')"
            class="px-6 py-3 text-sm font-black uppercase tracking-widest rounded-xl transition-all
                <?php echo e($tab === 'analysis'
                    ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/30 scale-[1.03]'
                    : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300'); ?>">
            Análise IA
        </button>

    </div>
</div>





<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'portfolio'): ?>

    
    
    

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>



        


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'analysis'): ?>
    <div class="space-y-6 mt-6 min-h-[600px]">

        
        <div class="relative flex-1" x-data @keydown.arrow-down="$wire.moveHighlight('down')" @keydown.arrow-up="$wire.moveHighlight('up')" @keydown.enter.prevent="$wire.confirmSelection()">

            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-1 block">
                Empresa / Ticker
            </label>

            <div class="relative flex flex-col md:flex-row gap-4">
                <div class="relative flex-1">
                    <input
                        wire:model.live.debounce.300ms="companyQuery"
                        type="text"
                        placeholder="Ex: AAPL, TSLA, MSFT, NVDA..."
                        class="w-full rounded-2xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all"
                        wire:loading.class="opacity-50 cursor-wait"
                        wire:target="selectSuggestion, analyzeCompany">

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($suggestions)): ?>
                        <div class="absolute left-0 right-0 mt-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-xl z-50 overflow-hidden">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $suggestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <button
                                    type="button"
                                    wire:click="selectSuggestion('<?php echo e($item['ticker']); ?>')"
                                    class="w-full flex items-center gap-3 px-4 py-3 text-sm transition-all
                                        <?php echo e($highlightIndex === $index ? 'bg-indigo-600 text-white' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'); ?>">

                                    <img src="<?php echo e($item['logo']); ?>" class="w-6 h-6 rounded-md object-contain bg-white" />

                                    <div class="flex flex-col text-left">
                                        <span class="font-black"><?php echo e($item['ticker']); ?></span>
                                        <span class="text-[10px] <?php echo e($highlightIndex === $index ? 'text-indigo-100' : 'text-zinc-500'); ?>"><?php echo e($item['name']); ?></span>
                                    </div>
                                </button>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($companyQuery) && empty($suggestions) && !empty($recentCompanies)): ?>
                        <div class="absolute left-0 right-0 mt-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-xl z-50">
                            <p class="px-4 py-2 text-[10px] uppercase tracking-widest text-zinc-400 font-black">Últimas analisadas</p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $recentCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticker): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php $item = $this->getCompanyData($ticker); ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item): ?>
                                    <button
                                        type="button"
                                        wire:click="selectSuggestion('<?php echo e($item['ticker']); ?>')"
                                        class="w-full flex items-center gap-3 px-4 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800">
                                        <img src="<?php echo e($item['logo']); ?>" class="w-5 h-5 rounded-md" />
                                        <span class="font-black text-xs"><?php echo e($item['ticker']); ?></span>
                                        <span class="text-zinc-500 text-[10px] ml-2"><?php echo e($item['name']); ?></span>
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="flex gap-2">
                    <select
                        wire:model="aiProvider"
                        class="rounded-2xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-[11px] font-black uppercase tracking-widest outline-none">
                        <option value="openrouter">GPT‑4o</option>
                        <option value="gemini">Gemini</option>
                    </select>

                    <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['wire:click' => 'analyzeCompany','variant' => 'primary','wire:loading.attr' => 'disabled','class' => 'relative rounded-2xl px-8 py-2 h-12 font-black uppercase tracking-widest bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-500/20 min-w-[140px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'analyzeCompany','variant' => 'primary','wire:loading.attr' => 'disabled','class' => 'relative rounded-2xl px-8 py-2 h-12 font-black uppercase tracking-widest bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-500/20 min-w-[140px]']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


                        <span wire:loading.remove wire:target="analyzeCompany, selectSuggestion">Analisar</span>

                        <span wire:loading wire:target="analyzeCompany, selectSuggestion" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            ...
                        </span>
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
        </div>

        
        <div class="relative min-h-[400px]">

            
            <div wire:loading wire:target="analyzeCompany, selectSuggestion" class="absolute inset-0 bg-white/60 dark:bg-zinc-950/60 backdrop-blur-[2px] z-20 flex items-center justify-center rounded-[2.5rem]">
                <div class="flex flex-col items-center gap-4">
                    <div class="p-5 bg-white dark:bg-zinc-900 rounded-3xl shadow-2xl border border-zinc-200 dark:border-zinc-800">
                        <svg class="animate-spin h-10 w-10 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-indigo-600 animate-pulse">Consultando Inteligência Artificial</p>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($companyAnalysis): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($companyAnalysis['error'])): ?>
                    <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500 text-red-600 font-black text-sm">
                        <?php echo e($companyAnalysis['error']); ?>

                    </div>
                <?php else: ?>
                    <div class="space-y-6 animate-[fadeIn_0.3s_ease-out]">
                        
                        <div class="p-6 rounded-[2.5rem] bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-xl space-y-4">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="p-2 bg-emerald-500/10 rounded-lg">
                                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'chart-bar','class' => 'size-5 text-emerald-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chart-bar','class' => 'size-5 text-emerald-500']); ?>
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
                                <h2 class="text-xl font-black italic uppercase tracking-tighter dark:text-white">Dados de Mercado</h2>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-6">
                                <div>
                                    <p class="text-[9px] font-black uppercase text-zinc-400 tracking-widest">Preço Atual</p>
                                    <p class="text-lg font-black dark:text-white italic"><?php echo e($companyAnalysis['market_data']['price']); ?> €</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black uppercase text-zinc-400 tracking-widest">Variação %</p>
                                    <p class="text-lg font-black <?php echo e(str_contains($companyAnalysis['market_data']['change'], '+') ? 'text-emerald-500' : 'text-red-500'); ?> italic">
                                        <?php echo e($companyAnalysis['market_data']['change']); ?>

                                    </p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black uppercase text-zinc-400 tracking-widest">Volume</p>
                                    <p class="text-lg font-black dark:text-white italic"><?php echo e($companyAnalysis['market_data']['volume']); ?></p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-[9px] font-black uppercase text-zinc-400 tracking-widest">52 Semanas (Range)</p>
                                    <p class="text-lg font-black dark:text-white italic"><?php echo e($companyAnalysis['market_data']['52w_range']); ?></p>
                                </div>
                            </div>
                        </div>

                        
                        <div class="p-6 rounded-[2.5rem] bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-xl space-y-4">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="p-2 bg-indigo-500/10 rounded-lg">
                                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'cpu-chip','class' => 'size-5 text-indigo-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'cpu-chip','class' => 'size-5 text-indigo-500']); ?>
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
                                <h2 class="text-xl font-black italic uppercase tracking-tighter dark:text-white">Relatório Deep Analysis</h2>
                            </div>

                            <div class="bg-zinc-50 dark:bg-zinc-950 p-6 rounded-3xl border border-zinc-100 dark:border-zinc-800">
                                <pre class="text-xs font-mono text-zinc-600 dark:text-zinc-400 whitespace-pre-wrap leading-relaxed">
<?php echo e(is_array($companyAnalysis) ? json_encode($companyAnalysis, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $companyAnalysis); ?>

                                </pre>
                            </div>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php else: ?>
                
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="size-20 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mb-4">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'magnifying-glass','class' => 'size-10 text-zinc-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'magnifying-glass','class' => 'size-10 text-zinc-400']); ?>
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
                    <p class="text-sm font-black uppercase tracking-widest text-zinc-400 italic">
                        Pesquisa um ticker acima para iniciar a análise IA
                    </p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>














<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'portfolio'): ?>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 px-2">
        
        <div class="stat-card bg-zinc-950 text-white p-6 sm:p-8 lg:p-10 rounded-[2.5rem] shadow-2xl relative overflow-hidden border border-zinc-800 lg:col-span-2 group">
            <div class="absolute top-0 right-0 w-64 sm:w-80 h-64 sm:h-80 bg-indigo-500/10 blur-[100px] rounded-full -mr-16 sm:-mr-20 -mt-16 sm:-mt-20 group-hover:bg-indigo-500/20 transition-all duration-1000"></div>
            <div class="absolute bottom-0 left-0 w-52 sm:w-64 h-52 sm:h-64 <?php echo e($totalProfit >= 0 ? 'bg-emerald-500/5' : 'bg-red-500/5'); ?> blur-[100px] rounded-full -ml-16 sm:-ml-20 -mb-16 sm:-mb-20 transition-all duration-1000"></div>

            <div class="relative z-10 flex flex-col justify-between h-full">
                <div>
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
                        <h2 class="text-[9px] sm:text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500 italic">
                            Avaliação do Portefólio
                        </h2>

                        <div class="flex items-center gap-1.5 px-3 py-1 rounded-full <?php echo e($totalProfit >= 0 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20'); ?>">
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => ''.e($totalProfit >= 0 ? 'arrow-trending-up' : 'arrow-trending-down').'','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($totalProfit >= 0 ? 'arrow-trending-up' : 'arrow-trending-down').'','class' => 'size-3.5']); ?>
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
                            <span class="text-[10px] font-black tracking-tighter">
                                <?php echo e($totalProfit >= 0 ? '+' : ''); ?><?php echo e(number_format($totalPnlPct, 2)); ?>%
                            </span>
                        </div>
                    </div>

                    <p class="text-5xl sm:text-6xl lg:text-7xl font-black text-white tracking-tighter italic leading-none break-words">
                        <?php echo e(number_format($currentValue, 2, ',', ' ')); ?>

                        <span class="text-2xl sm:text-3xl text-indigo-500 font-bold uppercase not-italic">€</span>
                    </p>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showNetValues): ?>
                        <p class="text-[9px] sm:text-[10px] font-black text-emerald-400 uppercase tracking-[0.2em] mt-4 flex flex-wrap items-center gap-2">
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'shield-check','class' => 'size-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shield-check','class' => 'size-3']); ?>
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
                            Já descontado o IRS estimado de <?php echo e(number_format($estimatedTax, 2, ',', ' ')); ?> €
                        </p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="mt-10 sm:mt-12 flex flex-wrap gap-4 items-stretch">
                    <div class="flex-1 min-w-[140px] px-5 sm:px-6 py-4 bg-white/5 rounded-2xl border border-white/10 backdrop-blur-md">
                        <div class="flex items-center gap-2 mb-1">
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'banknotes','class' => 'size-3.5 text-zinc-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'banknotes','class' => 'size-3.5 text-zinc-500']); ?>
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
                            <p class="text-[9px] uppercase font-black text-zinc-500 tracking-widest italic">
                                Custo Aquisição
                            </p>
                        </div>
                        <p class="text-lg sm:text-xl font-black text-zinc-200 tracking-tighter">
                            <?php echo e(number_format($totalInvested, 2, ',', ' ')); ?> €
                        </p>
                    </div>

                    <div class="flex-1 min-w-[140px] px-5 sm:px-6 py-4 bg-white/5 rounded-2xl border border-white/10 backdrop-blur-md">
                        <div class="flex items-center gap-2 mb-1">
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => ''.e($totalProfit >= 0 ? 'plus-circle' : 'minus-circle').'','class' => 'size-3.5 '.e($totalProfit >= 0 ? 'text-emerald-500' : 'text-red-500').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($totalProfit >= 0 ? 'plus-circle' : 'minus-circle').'','class' => 'size-3.5 '.e($totalProfit >= 0 ? 'text-emerald-500' : 'text-red-500').'']); ?>
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
                            <p class="text-[9px] uppercase font-black text-zinc-500 tracking-widest italic">
                                Lucro / Prejuízo
                            </p>
                        </div>
                        <p class="text-lg sm:text-xl font-black tracking-tighter <?php echo e($totalProfit >= 0 ? 'text-emerald-400' : 'text-red-400'); ?>">
                            <?php echo e($totalProfit >= 0 ? '+' : ''); ?><?php echo e(number_format($totalProfit, 2, ',', ' ')); ?> €
                        </p>
                    </div>

                    <div class="flex-1 min-w-[140px] px-5 sm:px-6 py-4 rounded-2xl border backdrop-blur-md <?php echo e($totalProfit >= 0 ? 'bg-emerald-500/10 border-emerald-500/20' : 'bg-red-500/10 border-red-500/20'); ?>">
                        <div class="flex items-center gap-2 mb-1">
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'chart-bar-square','class' => 'size-3.5 '.e($totalProfit >= 0 ? 'text-emerald-500' : 'text-red-500').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chart-bar-square','class' => 'size-3.5 '.e($totalProfit >= 0 ? 'text-emerald-500' : 'text-red-500').'']); ?>
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
                            <p class="text-[9px] uppercase font-black tracking-widest italic <?php echo e($totalProfit >= 0 ? 'text-emerald-400/80' : 'text-red-400/80'); ?>">
                                Rentabilidade Global
                            </p>
                        </div>
                        <p class="text-lg sm:text-xl font-black tracking-tighter <?php echo e($totalProfit >= 0 ? 'text-emerald-400' : 'text-red-400'); ?>">
                            <?php echo e($totalProfit >= 0 ? '+' : ''); ?><?php echo e(number_format($totalPnlPct, 2)); ?>%
                        </p>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-6 sm:p-8 flex flex-col justify-center text-center shadow-sm group">
            <div class="relative inline-block mx-auto mb-6">
                <div class="absolute inset-0 bg-indigo-500/20 blur-xl rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                <div class="relative p-5 sm:p-6 bg-indigo-50 dark:bg-indigo-950/40 rounded-[2rem] text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800 shadow-inner">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'shield-check','variant' => 'outline','class' => 'size-9 sm:size-10']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shield-check','variant' => 'outline','class' => 'size-9 sm:size-10']); ?>
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
            <h3 class="font-black text-[9px] sm:text-[10px] uppercase tracking-[0.2em] text-zinc-400 italic">
                Posições Ativas
            </h3>
            <p class="text-4xl sm:text-5xl font-black dark:text-white uppercase italic tracking-tighter mt-1">
                <?php echo e($myAssets->count()); ?>

            </p>
            <p class="mt-4 text-[10px] text-zinc-400 font-medium italic leading-relaxed px-2 sm:px-4">
                Capital distribuído por <?php echo e($myAssets->count()); ?> frentes.
            </p>
        </div>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 px-2 mt-8">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bestPerformer): ?>
            <button
                type="button"
                wire:click="editAsset(<?php echo e($bestPerformer->id); ?>)"
                class="p-5 sm:p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm flex items-center gap-4 group text-left transition-all hover:border-emerald-500/50 hover:shadow-lg hover:-translate-y-0.5">
                <div class="size-10 sm:size-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'arrow-trending-up','class' => 'size-5 sm:size-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-trending-up','class' => 'size-5 sm:size-6']); ?>
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
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-widest italic">
                            Melhor Performance
                        </p>
                        <span class="text-[8px] font-black uppercase text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded shrink-0">
                            <?php echo e($bestPerformer->type); ?>

                        </span>
                    </div>
                    <p class="text-sm sm:text-base font-black dark:text-white uppercase italic tracking-tighter mt-0.5 truncate">
                        <?php echo e($bestPerformer->symbol); ?>

                        <span class="text-emerald-500 ml-1">
                            +<?php echo e(number_format($bestPerformer->pnl_percent, 1)); ?>%
                        </span>
                    </p>
                    <p class="text-[10px] font-bold text-zinc-400 truncate mt-0.5">
                        <?php echo e($bestPerformer->name); ?>

                    </p>
                </div>
            </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($worstPerformer): ?>
            <button
                type="button"
                wire:click="editAsset(<?php echo e($worstPerformer->id); ?>)"
                class="p-5 sm:p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm flex items-center gap-4 group text-left transition-all hover:border-red-500/50 hover:shadow-lg hover:-translate-y-0.5">
                <div class="size-10 sm:size-12 rounded-2xl bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'arrow-trending-down','class' => 'size-5 sm:size-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-trending-down','class' => 'size-5 sm:size-6']); ?>
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
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-widest italic">
                            Pior Performance
                        </p>
                        <span class="text-[8px] font-black uppercase text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded shrink-0">
                            <?php echo e($worstPerformer->type); ?>

                        </span>
                    </div>
                    <p class="text-sm sm:text-base font-black dark:text-white uppercase italic tracking-tighter mt-0.5 truncate">
                        <?php echo e($worstPerformer->symbol); ?>

                        <span class="text-red-500 ml-1">
                            <?php echo e(number_format($worstPerformer->pnl_percent, 1)); ?>%
                        </span>
                    </p>
                    <p class="text-[10px] font-bold text-zinc-400 truncate mt-0.5">
                        <?php echo e($worstPerformer->name); ?>

                    </p>
                </div>
            </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($highestExposure): ?>
            <button
                type="button"
                wire:click="editAsset(<?php echo e($highestExposure->id); ?>)"
                class="p-5 sm:p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm flex items-center gap-4 group text-left transition-all hover:border-indigo-500/50 hover:shadow-lg hover:-translate-y-0.5">
                <div class="size-10 sm:size-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'chart-pie','class' => 'size-5 sm:size-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chart-pie','class' => 'size-5 sm:size-6']); ?>
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
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-widest italic">
                            Maior Exposição
                        </p>
                        <span class="text-[8px] font-black uppercase text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded shrink-0">
                            <?php echo e($highestExposure->type); ?>

                        </span>
                    </div>
                    <p class="text-sm sm:text-base font-black dark:text-white uppercase italic tracking-tighter mt-0.5 truncate">
                        <?php echo e($highestExposure->symbol); ?>

                        <span class="text-zinc-500 ml-1">
                            <?php echo e(number_format($highestExposure->current_value, 0, ',', ' ')); ?>€
                        </span>
                    </p>
                    <p class="text-[10px] font-bold text-zinc-400 truncate mt-0.5">
                        <?php echo e($highestExposure->name); ?>

                    </p>
                </div>
            </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="px-2 mt-8">
        <div class="p-6 sm:p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm">

            
            <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-3">
                    <div class="size-9 sm:size-10 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shadow-inner">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'chart-pie','class' => 'size-4 sm:size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chart-pie','class' => 'size-4 sm:size-5']); ?>
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
                    <div>
                        <h3 class="text-sm font-black uppercase italic tracking-tighter dark:text-white">
                            Distribuição por Classe
                        </h3>
                        <p class="text-[9px] font-black uppercase text-zinc-400 tracking-widest">
                            Composição do Portefólio
                        </p>
                    </div>
                </div>
                <p class="text-lg sm:text-xl font-black dark:text-white italic tracking-tighter">
                    <?php echo e(number_format($currentValue, 2, ',', ' ')); ?> €
                </p>
            </div>

            
            <div class="h-3 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden shadow-inner flex mb-8 sm:mb-10">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['Acao', 'Cripto', 'ETF', 'Fundo', 'Divida']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $pct = $composition->get($key)['percent'] ?? 0;
                        $barClass = match($key) {
                            'Acao'   => 'bg-emerald-500',
                            'Cripto' => 'bg-indigo-500',
                            'ETF'    => 'bg-amber-500',
                            'Fundo'  => 'bg-pink-500',
                            'Divida' => 'bg-blue-500',
                            default  => 'bg-zinc-500',
                        };
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pct > 0): ?>
                        <div
                            class="h-full <?php echo e($barClass); ?> transition-all duration-1000 first:rounded-l-full last:rounded-r-full"
                            style="width: <?php echo e($pct); ?>%">
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>

            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 sm:gap-8">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                    'Acao'   => ['Ações',  'chart-bar-square'],
                    'Cripto' => ['Cripto', 'cube'],
                    'ETF'    => ['ETF',    'squares-2x2'],
                    'Fundo'  => ['Fundo',  'briefcase'],
                    'Divida' => ['Dívida', 'building-library'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => [$label, $icon]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $data  = $composition->get($key) ?? ['percent' => 0, 'total' => 0];
                        $classes = match($key) {
                            'Acao'   => [
                                'hover'  => 'hover:bg-emerald-50/50 dark:hover:bg-emerald-900/10',
                                'iconBg' => 'bg-emerald-50 dark:bg-emerald-900/30',
                                'iconFg' => 'text-emerald-600 dark:text-emerald-400',
                                'bar'    => 'bg-emerald-500',
                            ],
                            'Cripto' => [
                                'hover'  => 'hover:bg-indigo-50/50 dark:hover:bg-indigo-900/10',
                                'iconBg' => 'bg-indigo-50 dark:bg-indigo-900/30',
                                'iconFg' => 'text-indigo-600 dark:text-indigo-400',
                                'bar'    => 'bg-indigo-500',
                            ],
                            'ETF'    => [
                                'hover'  => 'hover:bg-amber-50/50 dark:hover:bg-amber-900/10',
                                'iconBg' => 'bg-amber-50 dark:bg-amber-900/30',
                                'iconFg' => 'text-amber-600 dark:text-amber-400',
                                'bar'    => 'bg-amber-500',
                            ],
                            'Fundo'  => [
                                'hover'  => 'hover:bg-pink-50/50 dark:hover:bg-pink-900/10',
                                'iconBg' => 'bg-pink-50 dark:bg-pink-900/30',
                                'iconFg' => 'text-pink-600 dark:text-pink-400',
                                'bar'    => 'bg-pink-500',
                            ],
                            'Divida' => [
                                'hover'  => 'hover:bg-blue-50/50 dark:hover:bg-blue-900/10',
                                'iconBg' => 'bg-blue-50 dark:bg-blue-900/30',
                                'iconFg' => 'text-blue-600 dark:text-blue-400',
                                'bar'    => 'bg-blue-500',
                            ],
                            default  => [
                                'hover'  => 'hover:bg-zinc-50/50 dark:hover:bg-zinc-900/10',
                                'iconBg' => 'bg-zinc-50 dark:bg-zinc-900/30',
                                'iconFg' => 'text-zinc-600 dark:text-zinc-400',
                                'bar'    => 'bg-zinc-500',
                            ],
                        };
                    ?>
                    <button
                        type="button"
                        wire:click="setFilter('<?php echo e($key); ?>')"
                        <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'comp-'.e($key).''; ?>wire:key="comp-<?php echo e($key); ?>"
                        class="space-y-3 text-left p-4 -m-2 sm:-m-4 rounded-2xl transition-all <?php echo e($classes['hover']); ?> group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="size-7 sm:size-8 rounded-xl <?php echo e($classes['iconBg']); ?> <?php echo e($classes['iconFg']); ?> flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => ''.e($icon).'','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($icon).'','class' => 'size-4']); ?>
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
                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-500 tracking-widest">
                                    <?php echo e($label); ?>

                                </span>
                            </div>
                            <span class="text-xs sm:text-sm font-black dark:text-white italic tracking-tighter">
                                <?php echo e($data['percent']); ?>%
                            </span>
                        </div>

                        <div class="h-2 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden shadow-inner">
                            <div
                                class="h-full <?php echo e($classes['bar']); ?> rounded-full transition-all duration-1000"
                                style="width: <?php echo e($data['percent']); ?>%">
                            </div>
                        </div>

                        <p class="text-[10px] font-bold text-zinc-400 italic">
                            <?php echo e(number_format($data['total'], 2, ',', ' ')); ?> €
                        </p>
                    </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>

        </div>
    </div>

    
    <div class="flex flex-col md:flex-row gap-4 md:gap-6 items-center justify-between px-2 mt-10 sm:mt-12">
        <div class="relative w-full md:w-96 group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-zinc-400 group-focus-within:text-indigo-500">
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'magnifying-glass','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'magnifying-glass','class' => 'size-4']); ?>
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
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="PESQUISAR TICKER..."
                class="w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl h-11 sm:h-12 pl-12 pr-4 text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none" />
        </div>

        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar w-full md:w-auto">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['Todos', 'Acao', 'Cripto', 'ETF', 'Fundo', 'Divida']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <button
                    type="button"
                    wire:click="setFilter('<?php echo e($tab); ?>')"
                    class="px-4 sm:px-5 py-2.5 rounded-xl text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-all whitespace-nowrap
                        <?php echo e($filterType === $tab
                            ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-500/20 ring-1 ring-indigo-500'
                            : 'bg-white dark:bg-zinc-900 text-zinc-400 border border-zinc-200 dark:border-zinc-800 hover:border-indigo-500/50'); ?>">
                    <?php echo e(match($tab) { 'Acao' => 'Ações', 'Divida' => 'Dívida', default => $tab }); ?>

                </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </div>

    
    <div
        class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] overflow-hidden shadow-sm mx-2 mt-6"
        x-data="{ open: null }">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[720px]">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800 text-[9px] font-black uppercase text-zinc-400 tracking-widest">
                    <tr>
                        <th class="p-4 sm:p-6">Ativo</th>
                        <th class="p-4 sm:p-6 text-center">Qtd</th>
                        <th class="p-4 sm:p-6 text-right">Preço Compra</th>
                        <th class="p-4 sm:p-6 text-right">Atual</th>
                        <th class="p-4 sm:p-6 text-right">Valor Investido</th>
                        <th class="p-4 sm:p-6 text-right sm:px-10">Lucro ou Prejuízo</th>
                        <th class="p-4 sm:p-6 text-center w-24">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $myAssets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php $isGain = $asset->pnl >= 0; ?>

                        
                        <tr
                            <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'asset-row-'.e($asset->id).''; ?>wire:key="asset-row-<?php echo e($asset->id); ?>"
                            @click="open = open === <?php echo e($asset->id); ?> ? null : <?php echo e($asset->id); ?>"
                            class="hover:bg-indigo-50/30 dark:hover:bg-indigo-500/5 transition-all cursor-pointer select-none"
                            :class="open === <?php echo e($asset->id); ?> ? 'bg-indigo-50/40 dark:bg-indigo-500/5' : ''">

                            
                            <td class="p-4 sm:p-6">
                                <div class="flex items-center gap-3 sm:gap-4">

                                    
                                    <div class="size-4 sm:size-5 text-zinc-400 transition-transform duration-300 shrink-0"
                                         :class="open === <?php echo e($asset->id); ?> ? 'rotate-90' : ''">
                                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'chevron-right','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-right','class' => 'size-4']); ?>
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

                                    
                                    <div class="size-10 sm:size-11 rounded-2xl bg-white dark:bg-zinc-800 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-black text-[10px] sm:text-[11px] shadow-sm border border-zinc-100 dark:border-zinc-700 shrink-0">
                                        <?php echo e(substr($asset->symbol, 0, 3)); ?>

                                    </div>

                                    
                                    <div class="min-w-0">
                                        <p class="text-sm sm:text-base font-black dark:text-white uppercase leading-none tracking-tight truncate">
                                            <?php echo e($asset->name); ?>

                                        </p>

                                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mt-1">
                                            <span class="text-[8px] sm:text-[9px] font-bold text-zinc-400 uppercase tracking-widest">
                                                <?php echo e($asset->symbol); ?>

                                            </span>

                                            
                                            <span class="text-[7px] sm:text-[8px] font-black uppercase px-1.5 py-0.5 rounded-md
                                                <?php echo e(match($asset->type) {
                                                    'ETF'    => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
                                                    'Cripto' => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400',
                                                    'Acao'   => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
                                                    'Fundo'  => 'bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-400',
                                                    'Divida' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                                                    default  => 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500',
                                                }); ?>">
                                                <?php echo e(match($asset->type) {
                                                    'Acao' => 'Ação',
                                                    'Divida' => 'Dívida',
                                                    default => $asset->type,
                                                }); ?>

                                            </span>

                                            
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($asset->broker): ?>
                                                <span class="text-[8px] sm:text-[9px] text-zinc-400 font-medium">
                                                    · <?php echo e($asset->broker); ?>

                                                </span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            
                            <td class="p-4 sm:p-6 text-center font-black dark:text-zinc-200 text-xs sm:text-sm italic">
                                <?php echo e(number_format($asset->quantity, 4, ',', ' ')); ?>

                            </td>

                            
                            <td class="p-4 sm:p-6 text-right font-bold text-zinc-500 text-[10px] sm:text-xs italic">
                                <?php echo e(number_format($asset->average_price, 2, ',', ' ')); ?> €
                            </td>

                            
                            <td class="p-4 sm:p-6 text-right font-black dark:text-white italic tracking-tighter text-xs sm:text-sm">
                                <?php echo e(number_format($asset->current_price ?: $asset->average_price, 2, ',', ' ')); ?> €
                            </td>

                            
                            <td class="p-4 sm:p-6 text-right font-black dark:text-white text-sm sm:text-base italic tracking-tighter">
                                <?php echo e(number_format($asset->current_value, 2, ',', ' ')); ?> €
                            </td>

                            
                            <td class="p-4 sm:p-6 text-right sm:px-10">
                                <div class="inline-flex flex-col items-end">
                                    <span class="text-sm font-black <?php echo e($isGain ? 'text-emerald-500' : 'text-red-500'); ?> italic tracking-tighter">
                                        <?php echo e($isGain ? '+' : ''); ?><?php echo e(number_format($asset->pnl, 2, ',', ' ')); ?> €
                                    </span>
                                    <span class="text-[8px] sm:text-[9px] font-black <?php echo e($isGain ? 'bg-emerald-500/10 text-emerald-500' : 'bg-red-500/10 text-red-500'); ?> px-1.5 py-0.5 rounded uppercase mt-1">
                                        <?php echo e($isGain ? '+' : ''); ?><?php echo e(number_format($asset->pnl_percent, 2)); ?>%
                                    </span>
                                </div>
                            </td>

                            
                            <td class="p-4 sm:p-6 text-center" @click.stop>
                                <div class="flex items-center justify-center gap-2">

                                    <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['wire:click' => 'editAsset('.e($asset->id).')','variant' => 'ghost','icon' => 'pencil-square','size' => 'sm','class' => 'text-zinc-400 hover:text-indigo-600 rounded-xl']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'editAsset('.e($asset->id).')','variant' => 'ghost','icon' => 'pencil-square','size' => 'sm','class' => 'text-zinc-400 hover:text-indigo-600 rounded-xl']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['wire:click' => 'deleteAsset('.e($asset->id).')','wire:confirm' => 'Remover ativo?','variant' => 'ghost','icon' => 'trash','size' => 'sm','class' => 'text-zinc-400 hover:text-red-600 rounded-xl']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'deleteAsset('.e($asset->id).')','wire:confirm' => 'Remover ativo?','variant' => 'ghost','icon' => 'trash','size' => 'sm','class' => 'text-zinc-400 hover:text-red-600 rounded-xl']); ?>
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
                            </td>
                        </tr>

                        
                        <tr
                            <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'asset-detail-'.e($asset->id).''; ?>wire:key="asset-detail-<?php echo e($asset->id); ?>"
                            x-show="open === <?php echo e($asset->id); ?>"
                            x-collapse
                            class="bg-indigo-50/30 dark:bg-indigo-950/10">
                            <td colspan="7" class="px-6 sm:px-10 py-6">

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                                    
                                    <div class="p-5 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-4">
                                        <p class="text-[9px] font-black uppercase tracking-widest text-indigo-500 italic">
                                            Identificação
                                        </p>

                                        <div class="space-y-3">

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">Ticker</span>
                                                <span class="text-xs font-black dark:text-white uppercase"><?php echo e($asset->symbol); ?></span>
                                            </div>

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">ISIN</span>
                                                <span class="text-xs font-mono font-bold dark:text-white"><?php echo e($asset->isin ?: '—'); ?></span>
                                            </div>

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">Tipo</span>
                                                <span class="text-xs font-black dark:text-white">
                                                    <?php echo e($asset->type === 'Acao' ? 'Ação' : $asset->type); ?>

                                                </span>
                                            </div>

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">Corretora</span>
                                                <span class="text-xs font-black dark:text-white"><?php echo e($asset->broker ?: '—'); ?></span>
                                            </div>

                                            
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($asset->type === 'Divida'): ?>

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($asset->series): ?>
                                                    <div class="flex justify-between items-center gap-2">
                                                        <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">Série</span>
                                                        <span class="text-xs font-black dark:text-white"><?php echo e($asset->series); ?></span>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($asset->interest_rate): ?>
                                                    <div class="flex justify-between items-center gap-2">
                                                        <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">Taxa Anual Bruta</span>
                                                        <span class="text-xs font-black text-blue-600 dark:text-blue-400">
                                                            <?php echo e(number_format($asset->interest_rate, 3, ',', '')); ?>%
                                                        </span>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($asset->loyalty_bonus): ?>
                                                    <div class="flex justify-between items-center gap-2">
                                                        <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">Prémio Permanência</span>
                                                        <span class="text-xs font-black text-blue-500">
                                                            +<?php echo e(number_format($asset->loyalty_bonus, 3, ',', '')); ?>%
                                                        </span>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($asset->capitalization_date): ?>
                                                    <div class="flex justify-between items-center gap-2">
                                                        <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">Próx. Capitalização</span>
                                                        <span class="text-xs font-black dark:text-white">
                                                            <?php echo e(\Carbon\Carbon::parse($asset->capitalization_date)->format('d/m/Y')); ?>

                                                        </span>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($asset->exchange ?? $asset->network ?? $asset->provider ?? null): ?>
                                                <div class="flex justify-between items-center gap-2">
                                                    <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                        <?php echo e($asset->type === 'Acao' ? 'Bolsa' : ($asset->type === 'Cripto' ? 'Rede' : 'Gestora')); ?>

                                                    </span>
                                                    <span class="text-xs font-black dark:text-white">
                                                        <?php echo e($asset->exchange ?? $asset->network ?? $asset->provider ?? '—'); ?>

                                                    </span>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            
                                            <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                    Data de Compra
                                                </span>
                                                <span class="text-xs font-black dark:text-white">
                                                    <?php echo e($asset->operation_date ? \Carbon\Carbon::parse($asset->operation_date)->format('d/m/Y') : '—'); ?>

                                                </span>
                                            </div>

                                        </div>
                                    </div>

                                                                        
                                    <div class="p-5 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-4">
                                        <p class="text-[9px] font-black uppercase tracking-widest text-indigo-500 italic">
                                            Transação
                                        </p>

                                        <div class="space-y-3">

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($asset->operation_date): ?>
                                                <div class="flex justify-between items-center gap-2">
                                                    <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                        Data
                                                    </span>
                                                    <span class="text-xs font-black dark:text-white">
                                                        <?php echo e(\Carbon\Carbon::parse($asset->operation_date)->format('d/m/Y')); ?>

                                                    </span>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                    Quantidade
                                                </span>
                                                <span class="text-xs font-black dark:text-white">
                                                    <?php echo e(number_format($asset->quantity, 4, ',', ' ')); ?>

                                                </span>
                                            </div>

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                    Preço / Unidade
                                                </span>
                                                <span class="text-xs font-black dark:text-white">
                                                    <?php echo e(number_format($asset->average_price, 2, ',', ' ')); ?> €
                                                </span>
                                            </div>

                                            <div class="flex justify-between items-center gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                    Taxas
                                                </span>
                                                <span class="text-xs font-black <?php echo e(($asset->fees ?? 0) > 0 ? 'text-amber-500' : 'text-zinc-400'); ?>">
                                                    <?php echo e(number_format($asset->fees ?? 0, 2, ',', ' ')); ?> €
                                                </span>
                                            </div>

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                    Custo Total
                                                </span>
                                                <span class="text-xs font-black dark:text-white">
                                                    <?php echo e(number_format($asset->cost, 2, ',', ' ')); ?> €
                                                </span>
                                            </div>

                                        </div>
                                    </div>

                                    
                                    <div class="p-5 rounded-2xl border space-y-4
                                        <?php echo e($isGain
                                            ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-100 dark:border-emerald-900/30'
                                            : 'bg-red-50/50 dark:bg-red-950/20 border-red-100 dark:border-red-900/30'); ?>">

                                        <p class="text-[9px] font-black uppercase tracking-widest <?php echo e($isGain ? 'text-emerald-500' : 'text-red-500'); ?> italic">
                                            Performance
                                        </p>

                                        <div class="space-y-3">

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                    Preço Atual
                                                </span>
                                                <span class="text-xs font-black dark:text-white">
                                                    <?php echo e(number_format($asset->current_price ?: $asset->average_price, 2, ',', ' ')); ?> €
                                                </span>
                                            </div>

                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                    Avaliação
                                                </span>
                                                <span class="text-xs font-black dark:text-white">
                                                    <?php echo e(number_format($asset->current_value, 2, ',', ' ')); ?> €
                                                </span>
                                            </div>

                                            <div class="pt-2 border-t <?php echo e($isGain ? 'border-emerald-100 dark:border-emerald-900/30' : 'border-red-100 dark:border-red-900/30'); ?>">

                                                <div class="flex justify-between items-center gap-2">
                                                    <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                        P&L €
                                                    </span>
                                                    <span class="text-sm font-black <?php echo e($isGain ? 'text-emerald-500' : 'text-red-500'); ?> italic tracking-tighter">
                                                        <?php echo e($isGain ? '+' : ''); ?><?php echo e(number_format($asset->pnl, 2, ',', ' ')); ?> €
                                                    </span>
                                                </div>

                                                <div class="flex justify-between items-center gap-2 mt-2">
                                                    <span class="text-[9px] sm:text-[10px] font-black uppercase text-zinc-400 tracking-wider">
                                                        P&L %
                                                    </span>
                                                    <span class="text-sm font-black <?php echo e($isGain ? 'text-emerald-500' : 'text-red-500'); ?> italic tracking-tighter">
                                                        <?php echo e($isGain ? '+' : ''); ?><?php echo e(number_format($asset->pnl_percent, 2)); ?>%
                                                    </span>
                                                </div>

                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </td>
                        </tr>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="7" class="p-16 sm:p-20 text-center text-zinc-400 italic">
                                Sem ativos registados.
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
























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
    x-on:modal-show-add-investment.window="show()"
    x-on:modal-close-add-investment.window="close()"
    x-on:keydown.escape.window="close()">

    
    <div
        x-show="open"
        x-cloak
        x-transition.opacity
        @click="close()"
        class="fixed inset-0 z-50 bg-zinc-950/50 backdrop-blur-sm will-change-opacity">
    </div>

    
    <div
        x-show="open"
        x-cloak
        @click.self="close()"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6">

        
        <div
            x-show="open"
            @click.stop
            x-transition:enter="transition ease-out duration-100 transform-gpu"
            x-transition:enter-start="opacity-0 scale-[0.97] translate-y-1"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100 transform-gpu"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-[0.97] translate-y-1"
            class="relative w-full max-w-2xl bg-white dark:bg-zinc-950 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden max-h-[90vh] flex flex-col">

            
            <form
                wire:submit.prevent="save"
                x-data="{ selected: <?php if ((object) ('type') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('type'->value()); ?>')<?php echo e('type'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('type'); ?>')<?php endif; ?>, mode: 'unit' }"
                class="flex flex-col max-h-[90vh] overflow-hidden"
                autocomplete="off">

                
                <div class="min-h-0 flex-1 overflow-y-auto custom-scrollbar p-5 sm:p-6 space-y-6">

                    
                    <div class="flex items-center gap-4 pb-4 border-b border-zinc-200 dark:border-zinc-800">
                        <div class="p-3 bg-indigo-600 rounded-2xl text-white shadow-md shadow-indigo-500/20">
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => ''.e($editingId ? 'pencil-square' : 'plus').'','class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($editingId ? 'pencil-square' : 'plus').'','class' => 'size-5']); ?>
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
                            <h2 class="text-lg font-black uppercase italic tracking-tight leading-none text-zinc-900 dark:text-white">
                                <?php echo e($editingId ? 'Editar Ativo' : 'Novo Investimento'); ?>

                            </h2>
                            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1.5 italic">
                                Terminal de Registo de Capital
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="close()"
                            class="rounded-full p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition">
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

                    
                    <div class="flex items-center gap-1.5 px-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($s = 1; $s <= 4; $s++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="flex-1 h-0.5 rounded-full bg-indigo-600 <?php echo e($s > 1 ? 'opacity-20' : ''); ?>"></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>

                    
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="size-6 rounded-full bg-indigo-600 text-white text-[10px] font-black flex items-center justify-center shadow-lg">
                                1
                            </div>
                            <?php if (isset($component)) { $__componentOriginal8a84eac5abb8af1e2274971f8640b38f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8a84eac5abb8af1e2274971f8640b38f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::label','data' => ['class' => 'text-[11px] font-black uppercase tracking-widest text-zinc-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-[11px] font-black uppercase tracking-widest text-zinc-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                Seleciona a Classe
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8a84eac5abb8af1e2274971f8640b38f)): ?>
<?php $attributes = $__attributesOriginal8a84eac5abb8af1e2274971f8640b38f; ?>
<?php unset($__attributesOriginal8a84eac5abb8af1e2274971f8640b38f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8a84eac5abb8af1e2274971f8640b38f)): ?>
<?php $component = $__componentOriginal8a84eac5abb8af1e2274971f8640b38f; ?>
<?php unset($__componentOriginal8a84eac5abb8af1e2274971f8640b38f); ?>
<?php endif; ?>
                        </div>

                        <div class="grid grid-cols-3 sm:grid-cols-5 gap-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                                'Acao'   => ['chart-bar-square', 'Ação'],
                                'Cripto' => ['cube',             'Cripto'],
                                'ETF'    => ['squares-2x2',      'ETF'],
                                'Fundo'  => ['briefcase',        'Fundo'],
                                'Divida' => ['building-library', 'Dívida'],
                            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => [$icon, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>

                                <button type="button"
                                    @click="selected = '<?php echo e($key); ?>'; $wire.setType('<?php echo e($key); ?>')"
                                    :class="selected === '<?php echo e($key); ?>'
                                        ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 ring-2 ring-indigo-500/20'
                                        : 'border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50'"
                                    class="relative p-4 border-2 rounded-[2rem] flex flex-col items-center gap-2 transition-all duration-300 group">

                                    
                                    <div x-show="selected === '<?php echo e($key); ?>'" x-transition
                                         class="absolute top-2 right-2 size-4 rounded-full bg-indigo-600 flex items-center justify-center">
                                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'check','class' => 'size-2.5 text-white']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check','class' => 'size-2.5 text-white']); ?>
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

                                    
                                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => ''.e($icon).'',':class' => 'selected === \''.e($key).'\'
                                            ? \'text-indigo-600 dark:text-indigo-400\'
                                            : \'text-zinc-400\'','class' => 'size-6 group-hover:scale-110 transition-transform']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($icon).'',':class' => 'selected === \''.e($key).'\'
                                            ? \'text-indigo-600 dark:text-indigo-400\'
                                            : \'text-zinc-400\'','class' => 'size-6 group-hover:scale-110 transition-transform']); ?>
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

                                    
                                    <span class="text-[10px] font-black uppercase tracking-tight"
                                          ::class="selected === '<?php echo e($key); ?>'
                                            ? 'text-indigo-600 dark:text-indigo-400'
                                            : 'text-zinc-500'">
                                        <?php echo e($label); ?>

                                    </span>
                                </button>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="size-6 rounded-full bg-indigo-600 text-white text-[10px] font-black flex items-center justify-center shadow-lg">
                                2
                            </div>
                            <?php if (isset($component)) { $__componentOriginal8a84eac5abb8af1e2274971f8640b38f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8a84eac5abb8af1e2274971f8640b38f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::label','data' => ['class' => 'text-[11px] font-black uppercase tracking-widest text-zinc-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-[11px] font-black uppercase tracking-widest text-zinc-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                Identificação do Ativo
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8a84eac5abb8af1e2274971f8640b38f)): ?>
<?php $attributes = $__attributesOriginal8a84eac5abb8af1e2274971f8640b38f; ?>
<?php unset($__attributesOriginal8a84eac5abb8af1e2274971f8640b38f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8a84eac5abb8af1e2274971f8640b38f)): ?>
<?php $component = $__componentOriginal8a84eac5abb8af1e2274971f8640b38f; ?>
<?php unset($__componentOriginal8a84eac5abb8af1e2274971f8640b38f); ?>
<?php endif; ?>
                        </div>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'Divida'): ?>

                            
                            <div class="flex items-center gap-1 p-1 bg-zinc-100 dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-700">
                                <button type="button" wire:click="$set('product_type', 'CA')"
                                    class="flex-1 flex items-center justify-center gap-2 h-11 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all
                                        <?php echo e($product_type === 'CA' ? 'bg-blue-600 text-white shadow-lg' : 'text-zinc-400 hover:text-zinc-600'); ?>">
                                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'building-library','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'building-library','class' => 'size-3.5']); ?>
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
                                    Certificados de Aforro
                                </button>

                                <button type="button" wire:click="$set('product_type', 'CT')"
                                    class="flex-1 flex items-center justify-center gap-2 h-11 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all
                                        <?php echo e($product_type === 'CT' ? 'bg-blue-600 text-white shadow-lg' : 'text-zinc-400 hover:text-zinc-600'); ?>">
                                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'document-text','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'document-text','class' => 'size-3.5']); ?>
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
                                    Certificados do Tesouro
                                </button>
                            </div>

                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <?php if (isset($component)) { $__componentOriginal26c546557cdc09040c8dd00b2090afd0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal26c546557cdc09040c8dd00b2090afd0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::input.index','data' => ['wire:model' => 'name','label' => 'Nome / Descrição','placeholder' => ''.e($product_type === 'CA' ? 'Ex: Certificados de Aforro Série F' : 'Ex: Certificados do Tesouro Poupança Crescimento').'','class' => 'font-medium !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'name','label' => 'Nome / Descrição','placeholder' => ''.e($product_type === 'CA' ? 'Ex: Certificados de Aforro Série F' : 'Ex: Certificados do Tesouro Poupança Crescimento').'','class' => 'font-medium !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $attributes = $__attributesOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $component = $__componentOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__componentOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>

                                <?php if (isset($component)) { $__componentOriginal26c546557cdc09040c8dd00b2090afd0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal26c546557cdc09040c8dd00b2090afd0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::input.index','data' => ['wire:model' => 'series','label' => 'Série','placeholder' => 'Ex: Série F','class' => 'font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'series','label' => 'Série','placeholder' => 'Ex: Série F','class' => 'font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $attributes = $__attributesOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $component = $__componentOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__componentOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <?php if (isset($component)) { $__componentOriginal26c546557cdc09040c8dd00b2090afd0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal26c546557cdc09040c8dd00b2090afd0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::input.index','data' => ['wire:model' => 'issuer','label' => 'Emitente','placeholder' => 'IGCP / Estado Português','class' => 'font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'issuer','label' => 'Emitente','placeholder' => 'IGCP / Estado Português','class' => 'font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $attributes = $__attributesOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $component = $__componentOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__componentOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>

                                <?php if (isset($component)) { $__componentOriginal26c546557cdc09040c8dd00b2090afd0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal26c546557cdc09040c8dd00b2090afd0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::input.index','data' => ['wire:model' => 'broker','label' => 'Plataforma','placeholder' => 'AforroNet, CTT...','class' => 'font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'broker','label' => 'Plataforma','placeholder' => 'AforroNet, CTT...','class' => 'font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $attributes = $__attributesOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $component = $__componentOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__componentOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
                            </div>

                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <?php if (isset($component)) { $__componentOriginal26c546557cdc09040c8dd00b2090afd0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal26c546557cdc09040c8dd00b2090afd0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::input.index','data' => ['wire:model' => 'interest_rate','type' => 'number','step' => '0.001','label' => 'Taxa Base Atual — Euribor 3M (%)','placeholder' => 'Ex: 2.500','class' => 'font-black !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'interest_rate','type' => 'number','step' => '0.001','label' => 'Taxa Base Atual — Euribor 3M (%)','placeholder' => 'Ex: 2.500','class' => 'font-black !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $attributes = $__attributesOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $component = $__componentOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__componentOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
                                    <p class="text-[10px] text-zinc-400 italic px-1">
                                        Máx. 2,5% · Mín. 0% · Prémio calculado automaticamente.
                                    </p>
                                </div>

                                <div class="space-y-1.5">
                                    <?php if (isset($component)) { $__componentOriginal26c546557cdc09040c8dd00b2090afd0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal26c546557cdc09040c8dd00b2090afd0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::input.index','data' => ['wire:model' => 'symbol','label' => 'Referência / Código','placeholder' => 'Ex: CA-F ou CT-PC','class' => 'uppercase font-black !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'symbol','label' => 'Referência / Código','placeholder' => 'Ex: CA-F ou CT-PC','class' => 'uppercase font-black !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $attributes = $__attributesOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $component = $__componentOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__componentOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
                                    <p class="text-[10px] text-zinc-400 italic px-1">
                                        Código interno da subscrição.
                                    </p>
                                </div>
                            </div>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product_type === 'CA'): ?>
                                <div class="flex items-start gap-3 p-4 bg-blue-50 dark:bg-blue-950/30 rounded-2xl border border-blue-100 dark:border-blue-900/30">
                                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'information-circle','class' => 'size-4 text-blue-500 shrink-0 mt-0.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'information-circle','class' => 'size-4 text-blue-500 shrink-0 mt-0.5']); ?>
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
                                    <div class="text-[10px] text-blue-700 dark:text-blue-300 leading-relaxed space-y-1">
                                        <p><strong>Capitalização trimestral</strong> — juros somados ao saldo.</p>
                                        <p><strong>IRS 28%</strong> aplicado antes de capitalizar.</p>
                                        <p><strong>Prémio de permanência</strong> calculado automaticamente.</p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="flex items-start gap-3 p-4 bg-indigo-50 dark:bg-indigo-950/30 rounded-2xl border border-indigo-100 dark:border-indigo-900/30">
                                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'information-circle','class' => 'size-4 text-indigo-500 shrink-0 mt-0.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'information-circle','class' => 'size-4 text-indigo-500 shrink-0 mt-0.5']); ?>
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
                                    <div class="text-[10px] text-indigo-700 dark:text-indigo-300 leading-relaxed space-y-1">
                                        <p><strong>Juro anual não capitalizável</strong>.</p>
                                        <p><strong>IRS 28%</strong> sobre o juro bruto.</p>
                                        <p>Juro líquido registado em <strong>Rendimentos Recebidos</strong>.</p>
                                    </div>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php else: ?>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <?php if (isset($component)) { $__componentOriginal26c546557cdc09040c8dd00b2090afd0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal26c546557cdc09040c8dd00b2090afd0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::input.index','data' => ['wire:model' => 'symbol','label' => 'Ticker / Símbolo','placeholder' => 'Ex: VUAA.DE','class' => 'uppercase font-black !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'symbol','label' => 'Ticker / Símbolo','placeholder' => 'Ex: VUAA.DE','class' => 'uppercase font-black !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $attributes = $__attributesOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $component = $__componentOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__componentOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>

                                <?php if (isset($component)) { $__componentOriginal26c546557cdc09040c8dd00b2090afd0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal26c546557cdc09040c8dd00b2090afd0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::input.index','data' => ['wire:model' => 'isin','label' => 'Código ISIN','placeholder' => 'Ex: IE00BFMXXD54','class' => 'uppercase font-black !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'isin','label' => 'Código ISIN','placeholder' => 'Ex: IE00BFMXXD54','class' => 'uppercase font-black !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $attributes = $__attributesOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $component = $__componentOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__componentOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
                            </div>

                            <?php if (isset($component)) { $__componentOriginal26c546557cdc09040c8dd00b2090afd0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal26c546557cdc09040c8dd00b2090afd0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::input.index','data' => ['wire:model' => 'name','label' => 'Nome do Ativo','placeholder' => 'Ex: Vanguard S&P 500 UCITS ETF','class' => 'font-medium !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14 shadow-inner']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'name','label' => 'Nome do Ativo','placeholder' => 'Ex: Vanguard S&P 500 UCITS ETF','class' => 'font-medium !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14 shadow-inner']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $attributes = $__attributesOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $component = $__componentOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__componentOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'Acao'): ?>
                                        <?php if (isset($component)) { $__componentOriginal26c546557cdc09040c8dd00b2090afd0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal26c546557cdc09040c8dd00b2090afd0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::input.index','data' => ['wire:model' => 'exchange','label' => 'Bolsa (Exchange)','placeholder' => 'NYSE, NASDAQ...','class' => 'font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'exchange','label' => 'Bolsa (Exchange)','placeholder' => 'NYSE, NASDAQ...','class' => 'font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $attributes = $__attributesOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $component = $__componentOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__componentOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
                                    <?php elseif($type === 'Cripto'): ?>
                                        <?php if (isset($component)) { $__componentOriginal26c546557cdc09040c8dd00b2090afd0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal26c546557cdc09040c8dd00b2090afd0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::input.index','data' => ['wire:model' => 'network','label' => 'Rede (Blockchain)','placeholder' => 'ERC-20, Solana...','class' => 'font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'network','label' => 'Rede (Blockchain)','placeholder' => 'ERC-20, Solana...','class' => 'font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $attributes = $__attributesOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $component = $__componentOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__componentOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
                                    <?php else: ?>
                                        <?php if (isset($component)) { $__componentOriginal26c546557cdc09040c8dd00b2090afd0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal26c546557cdc09040c8dd00b2090afd0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::input.index','data' => ['wire:model' => 'provider','label' => 'Gestora / Provider','placeholder' => 'Vanguard, iShares...','class' => 'font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'provider','label' => 'Gestora / Provider','placeholder' => 'Vanguard, iShares...','class' => 'font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $attributes = $__attributesOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $component = $__componentOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__componentOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                <?php if (isset($component)) { $__componentOriginal26c546557cdc09040c8dd00b2090afd0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal26c546557cdc09040c8dd00b2090afd0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::input.index','data' => ['wire:model' => 'broker','label' => 'Corretora','placeholder' => 'Ex: XTB, DEGIRO...','class' => 'font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'broker','label' => 'Corretora','placeholder' => 'Ex: XTB, DEGIRO...','class' => 'font-bold !bg-zinc-50 dark:!bg-zinc-900 rounded-2xl h-14']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $attributes = $__attributesOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $component = $__componentOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__componentOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="size-6 rounded-full bg-indigo-600 text-white text-[10px] font-black flex items-center justify-center shadow-lg">
                                3
                            </div>
                            <?php if (isset($component)) { $__componentOriginal8a84eac5abb8af1e2274971f8640b38f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8a84eac5abb8af1e2274971f8640b38f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::label','data' => ['class' => 'text-[11px] font-black uppercase tracking-widest text-zinc-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-[11px] font-black uppercase tracking-widest text-zinc-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                Dados da Transação
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8a84eac5abb8af1e2274971f8640b38f)): ?>
<?php $attributes = $__attributesOriginal8a84eac5abb8af1e2274971f8640b38f; ?>
<?php unset($__attributesOriginal8a84eac5abb8af1e2274971f8640b38f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8a84eac5abb8af1e2274971f8640b38f)): ?>
<?php $component = $__componentOriginal8a84eac5abb8af1e2274971f8640b38f; ?>
<?php unset($__componentOriginal8a84eac5abb8af1e2274971f8640b38f); ?>
<?php endif; ?>
                        </div>

                        <div class="p-6 bg-indigo-50/50 dark:bg-indigo-950/20 rounded-[2rem] border border-indigo-100 dark:border-indigo-900/30 space-y-5">

                            
                            <?php if (isset($component)) { $__componentOriginal26c546557cdc09040c8dd00b2090afd0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal26c546557cdc09040c8dd00b2090afd0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::input.index','data' => ['wire:model' => 'operation_date','type' => 'date','label' => 'Data de Subscrição','class' => 'font-black !bg-white dark:!bg-zinc-950']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'operation_date','type' => 'date','label' => 'Data de Subscrição','class' => 'font-black !bg-white dark:!bg-zinc-950']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $attributes = $__attributesOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $component = $__componentOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__componentOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type !== 'Divida'): ?>

                                <div class="flex items-center gap-1 p-1 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-700">
                                    <button type="button" @click="mode = 'unit'"
                                        :class="mode === 'unit'
                                            ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20'
                                            : 'text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300'"
                                        class="flex-1 flex items-center justify-center gap-2 h-10 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'calculator','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'calculator','class' => 'size-3.5']); ?>
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
                                        Preço por unidade
                                    </button>

                                    <button type="button" @click="mode = 'total'"
                                        :class="mode === 'total'
                                            ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20'
                                            : 'text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300'"
                                        class="flex-1 flex items-center justify-center gap-2 h-10 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'banknotes','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'banknotes','class' => 'size-3.5']); ?>
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
                                        Valor que investi
                                    </button>
                                </div>

                                
                                <div x-show="mode === 'unit'" x-transition class="space-y-4">

                                    <div class="p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-1.5">
                                        <?php if (isset($component)) { $__componentOriginal26c546557cdc09040c8dd00b2090afd0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal26c546557cdc09040c8dd00b2090afd0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::input.index','data' => ['wire:model.live' => 'quantity','type' => 'number','step' => '0.00001','label' => 'Quantidade de Títulos Comprados','placeholder' => 'Ex: 4','class' => 'font-black !bg-zinc-50 dark:!bg-zinc-950']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'quantity','type' => 'number','step' => '0.00001','label' => 'Quantidade de Títulos Comprados','placeholder' => 'Ex: 4','class' => 'font-black !bg-zinc-50 dark:!bg-zinc-950']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $attributes = $__attributesOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $component = $__componentOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__componentOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
                                        <p class="text-[10px] text-zinc-400 italic px-1">
                                            Número exato de unidades adquiridas.
                                        </p>
                                    </div>

                                    <div class="p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-1.5">
                                        <?php if (isset($component)) { $__componentOriginal26c546557cdc09040c8dd00b2090afd0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal26c546557cdc09040c8dd00b2090afd0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::input.index','data' => ['wire:model.live' => 'average_price','type' => 'number','step' => '0.00001','label' => 'Preço de Compra por Unidade (€)','placeholder' => 'Ex: 125,77','class' => 'font-black !bg-zinc-50 dark:!bg-zinc-950']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'average_price','type' => 'number','step' => '0.00001','label' => 'Preço de Compra por Unidade (€)','placeholder' => 'Ex: 125,77','class' => 'font-black !bg-zinc-50 dark:!bg-zinc-950']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $attributes = $__attributesOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $component = $__componentOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__componentOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
                                        <p class="text-[10px] text-zinc-400 italic px-1">
                                            Valor de 1 título no momento da compra.
                                        </p>
                                    </div>
                                </div>

                                
                                <div x-show="mode === 'total'" x-transition class="space-y-4">

                                    <div class="p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-1.5">

                                        <?php if (isset($component)) { $__componentOriginal26c546557cdc09040c8dd00b2090afd0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal26c546557cdc09040c8dd00b2090afd0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::input.index','data' => ['wire:model.live' => 'total_amount','type' => 'number','step' => '0.01','label' => 'Valor Total que Investiste (€)','placeholder' => 'Ex: 503,08','class' => 'font-black !bg-zinc-50 dark:!bg-zinc-950']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'total_amount','type' => 'number','step' => '0.01','label' => 'Valor Total que Investiste (€)','placeholder' => 'Ex: 503,08','class' => 'font-black !bg-zinc-50 dark:!bg-zinc-950']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $attributes = $__attributesOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $component = $__componentOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__componentOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>

                                        <p class="text-[10px] text-zinc-400 italic px-1">
                                            Montante total debitado na corretora antes de taxas.
                                        </p>
                                    </div>

                                    
                                    <div class="p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-1.5">
                                        <?php if (isset($component)) { $__componentOriginal26c546557cdc09040c8dd00b2090afd0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal26c546557cdc09040c8dd00b2090afd0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::input.index','data' => ['wire:model.live' => 'average_price','type' => 'number','step' => '0.00001','label' => 'Preço de Compra por Unidade (€)','placeholder' => 'Ex: 125,77','class' => 'font-black !bg-zinc-50 dark:!bg-zinc-950']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'average_price','type' => 'number','step' => '0.00001','label' => 'Preço de Compra por Unidade (€)','placeholder' => 'Ex: 125,77','class' => 'font-black !bg-zinc-50 dark:!bg-zinc-950']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $attributes = $__attributesOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $component = $__componentOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__componentOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
                                        <p class="text-[10px] text-zinc-400 italic px-1">
                                            Necessário para calcular a quantidade: Total ÷ Preço.
                                        </p>
                                    </div>

                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($total_amount && $average_price && (float)$average_price > 0): ?>
                                        <div class="flex items-center justify-between px-4 py-3 bg-indigo-600/10 dark:bg-indigo-500/10 rounded-2xl border border-indigo-200 dark:border-indigo-800">
                                            <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400">
                                                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'arrow-path','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-path','class' => 'size-3.5']); ?>
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
                                                <span class="text-[10px] font-black uppercase tracking-widest">Quantidade calculada</span>
                                            </div>
                                            <span class="text-sm font-black text-indigo-600 dark:text-indigo-400 italic tracking-tighter">
                                                ≈ <?php echo e(number_format(((float)$total_amount - (float)($fees ?? 0)) / (float)$average_price, 4, ',', ' ')); ?> unidades
                                            </span>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                </div>

                                
                                <div class="p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-1.5">
                                    <?php if (isset($component)) { $__componentOriginal26c546557cdc09040c8dd00b2090afd0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal26c546557cdc09040c8dd00b2090afd0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::input.index','data' => ['wire:model.live' => 'fees','type' => 'number','step' => '0.01','label' => 'Taxas / Comissões da Corretora (€)','placeholder' => 'Ex: 0,00','class' => 'font-black !bg-zinc-50 dark:!bg-zinc-950']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'fees','type' => 'number','step' => '0.01','label' => 'Taxas / Comissões da Corretora (€)','placeholder' => 'Ex: 0,00','class' => 'font-black !bg-zinc-50 dark:!bg-zinc-950']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $attributes = $__attributesOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $component = $__componentOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__componentOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
                                    <p class="text-[10px] text-zinc-400 italic px-1">
                                        Custo da execução da ordem. <span class="text-emerald-500 font-bold">XTB, Trading212 e DEGIRO Free: normalmente 0 €.</span>
                                    </p>
                                </div>

                            <?php else: ?>
                                
                                <div class="p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 space-y-1.5">
                                    <?php if (isset($component)) { $__componentOriginal26c546557cdc09040c8dd00b2090afd0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal26c546557cdc09040c8dd00b2090afd0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::input.index','data' => ['wire:model.live' => 'quantity','type' => 'number','step' => '1','label' => 'Capital Investido (€) = N.º de Títulos','placeholder' => 'Ex: 5000','class' => 'font-black !bg-zinc-50 dark:!bg-zinc-950']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'quantity','type' => 'number','step' => '1','label' => 'Capital Investido (€) = N.º de Títulos','placeholder' => 'Ex: 5000','class' => 'font-black !bg-zinc-50 dark:!bg-zinc-950']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $attributes = $__attributesOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__attributesOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal26c546557cdc09040c8dd00b2090afd0)): ?>
<?php $component = $__componentOriginal26c546557cdc09040c8dd00b2090afd0; ?>
<?php unset($__componentOriginal26c546557cdc09040c8dd00b2090afd0); ?>
<?php endif; ?>
                                    <p class="text-[10px] text-zinc-400 italic px-1">
                                        Cada título vale exatamente <strong>1 €</strong>.
                                    </p>
                                </div>

                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($quantity && (float)$quantity > 0): ?>
                                    <div class="flex items-center justify-between px-4 py-3 bg-blue-600/10 dark:bg-blue-500/10 rounded-2xl border border-blue-200 dark:border-blue-800">
                                        <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400">
                                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'building-library','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'building-library','class' => 'size-3.5']); ?>
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
                                            <span class="text-[10px] font-black uppercase tracking-widest">Capital subscrito</span>
                                        </div>
                                        <span class="text-sm font-black text-blue-600 dark:text-blue-400 italic tracking-tighter">
                                            <?php echo e(number_format((float)$quantity, 0, ',', ' ')); ?> títulos · <?php echo e(number_format((float)$quantity, 2, ',', ' ')); ?> €
                                        </span>
                                    </div>

                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($interest_rate && (float)$interest_rate > 0): ?>
                                        <?php
                                            $yearsComplete  = $operation_date ? (int)\Carbon\Carbon::parse($operation_date)->diffInYears(now()) : 0;
                                            $loyaltyPreview = match(true) {
                                                $yearsComplete >= 5 => 1.25,
                                                $yearsComplete >= 4 => 1.00,
                                                $yearsComplete >= 3 => 0.75,
                                                $yearsComplete >= 2 => 0.50,
                                                $yearsComplete >= 1 => 0.25,
                                                default             => 0.00,
                                            };
                                            $totalRateGross   = (float)$interest_rate + $loyaltyPreview;
                                            $annualNetPreview = (float)$quantity * $totalRateGross / 100 * 0.72;
                                        ?>

                                        <div class="p-4 bg-blue-50/50 dark:bg-blue-950/20 rounded-2xl border border-blue-100 dark:border-blue-900/30 space-y-3">
                                            <p class="text-[9px] font-black uppercase tracking-widest text-blue-500 italic">Simulação Atual</p>

                                            <div class="grid grid-cols-2 gap-3">
                                                <div class="text-center p-3 bg-white dark:bg-zinc-900 rounded-xl border border-blue-100 dark:border-blue-900/20">
                                                    <p class="text-[9px] font-black uppercase text-zinc-400 tracking-wider">Taxa Total Bruta</p>
                                                    <p class="text-lg font-black text-blue-600 dark:text-blue-400 italic tracking-tighter mt-1">
                                                        <?php echo e(number_format($totalRateGross, 3, ',', '')); ?>%
                                                    </p>
                                                </div>

                                                <div class="text-center p-3 bg-white dark:bg-zinc-900 rounded-xl border border-blue-100 dark:border-blue-900/20">
                                                    <p class="text-[9px] font-black uppercase text-zinc-400 tracking-wider">Juro Líquido / Ano</p>
                                                    <p class="text-lg font-black text-emerald-500 italic tracking-tighter mt-1">
                                                        +<?php echo e(number_format($annualNetPreview, 2, ',', ' ')); ?> €
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        </div>
                    </div>
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($quantity && $average_price && (float)$quantity > 0): ?>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="size-6 rounded-full bg-indigo-600 text-white text-[10px] font-black flex items-center justify-center shadow-lg">
                                    4
                                </div>
                                <?php if (isset($component)) { $__componentOriginal8a84eac5abb8af1e2274971f8640b38f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8a84eac5abb8af1e2274971f8640b38f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::label','data' => ['class' => 'text-[11px] font-black uppercase tracking-widest text-zinc-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-[11px] font-black uppercase tracking-widest text-zinc-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                    Resumo
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8a84eac5abb8af1e2274971f8640b38f)): ?>
<?php $attributes = $__attributesOriginal8a84eac5abb8af1e2274971f8640b38f; ?>
<?php unset($__attributesOriginal8a84eac5abb8af1e2274971f8640b38f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8a84eac5abb8af1e2274971f8640b38f)): ?>
<?php $component = $__componentOriginal8a84eac5abb8af1e2274971f8640b38f; ?>
<?php unset($__componentOriginal8a84eac5abb8af1e2274971f8640b38f); ?>
<?php endif; ?>
                            </div>

                            <div class="p-6 bg-zinc-950 rounded-[2.5rem] flex items-center justify-between border border-zinc-800 shadow-2xl">
                                <div class="space-y-1">
                                    <p class="text-[9px] font-black uppercase text-zinc-500 tracking-[0.2em] italic">
                                        Investimento Total
                                    </p>

                                    <p class="text-white text-xs font-medium">
                                        <span class="text-indigo-400 font-black">
                                            <?php echo e(number_format((float)$quantity, 4, ',', ' ')); ?>

                                        </span> unidades

                                        <span class="text-zinc-500 mx-1">×</span>

                                        <span class="text-indigo-400 font-black">
                                            <?php echo e(number_format((float)$average_price, 2, ',', ' ')); ?> €
                                        </span>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($fees) && (float)$fees > 0): ?>
                                            <span class="text-zinc-500 mx-1">+</span>
                                            <span class="text-amber-400 font-black">
                                                <?php echo e(number_format((float)$fees, 2, ',', ' ')); ?> € taxas
                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </p>
                                </div>

                                <div class="text-right">
                                    <p class="text-2xl sm:text-3xl font-black text-white italic tracking-tighter">
                                        <?php echo e(number_format((float)$quantity * (float)$average_price + (float)($fees ?? 0), 2, ',', ' ')); ?> €
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                </div>

                
                <div class="shrink-0 p-5 sm:p-6 pt-4 flex gap-3 border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950">
                    <button
                        type="button"
                        @click="close()"
                        class="flex-1 uppercase font-black text-[10px] h-12 rounded-2xl border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="flex-[2] bg-indigo-600 h-12 rounded-2xl font-black uppercase tracking-widest shadow-lg shadow-indigo-500/20 text-white hover:bg-indigo-500 active:scale-95 transition-all disabled:opacity-60">
                        <span wire:loading.remove wire:target="save">Confirmar Posição</span>
                        <span wire:loading wire:target="save">A Processar...</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>




<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($recentIncomes) && $recentIncomes->count() > 0): ?>
<div class="px-2 mt-10">
    <div class="p-6 sm:p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm">

        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-3">
                <div class="size-9 sm:size-10 rounded-2xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center shadow-inner">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'banknotes','class' => 'size-4 sm:size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'banknotes','class' => 'size-4 sm:size-5']); ?>
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
                <div>
                    <h3 class="text-sm font-black uppercase italic tracking-tighter dark:text-white">
                        Rendimentos Recebidos
                    </h3>
                    <p class="text-[9px] font-black uppercase text-zinc-400 tracking-widest">
                        Juros Líquidos · Dívida Pública
                    </p>
                </div>
            </div>

            <div class="text-right">
                <p class="text-[9px] font-black uppercase text-zinc-400 tracking-widest italic">
                    Total Acumulado
                </p>
                <p class="text-xl sm:text-2xl font-black text-blue-600 dark:text-blue-400 italic tracking-tighter">
                    +<?php echo e(number_format($totalIncomeNet, 2, ',', ' ')); ?> €
                </p>
            </div>
        </div>

        <div class="overflow-y-auto max-h-[380px] custom-scrollbar pr-2">
            <table class="w-full text-left border-collapse min-w-[720px]">
                <thead class="sticky top-0 bg-white dark:bg-zinc-900 z-10 text-[9px] font-black uppercase text-zinc-400 tracking-widest border-b border-zinc-100 dark:border-zinc-800">
                    <tr>
                        <th class="pb-4">Ativo</th>
                        <th class="pb-4 text-center">Tipo</th>
                        <th class="pb-4 text-right">Data</th>
                        <th class="pb-4 text-right">Taxa Base</th>
                        <th class="pb-4 text-right">Prémio</th>
                        <th class="pb-4 text-right">Juro Bruto</th>
                        <th class="pb-4 text-right">IRS 28%</th>
                        <th class="pb-4 text-right">Juro Líquido</th>
                        <th class="pb-4 text-right">Saldo Após</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $recentIncomes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $income): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="hover:bg-blue-50/30 dark:hover:bg-blue-500/5 transition">
                            <td class="py-4">
                                <p class="text-xs font-black dark:text-white uppercase tracking-tight">
                                    <?php echo e($income->investment?->symbol ?? '—'); ?>

                                </p>
                                <p class="text-[10px] text-zinc-400 truncate max-w-[160px]">
                                    <?php echo e($income->investment?->name ?? '—'); ?>

                                </p>
                            </td>

                            <td class="py-4 text-center">
                                <span class="text-[8px] font-black uppercase px-2 py-1 rounded-lg
                                    <?php echo e($income->type === 'CA'
                                        ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400'
                                        : 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400'); ?>">
                                    <?php echo e($income->type === 'CA' ? 'Aforro' : 'Tesouro'); ?>

                                </span>
                            </td>

                            <td class="py-4 text-right text-xs font-bold text-zinc-500">
                                <?php echo e($income->reference_date->format('d/m/Y')); ?>

                            </td>

                            <td class="py-4 text-right text-xs font-black dark:text-zinc-200">
                                <?php echo e(number_format($income->base_rate, 3, ',', '')); ?>%
                            </td>

                            <td class="py-4 text-right text-xs font-black text-blue-500">
                                +<?php echo e(number_format($income->loyalty_bonus, 3, ',', '')); ?>%
                            </td>

                            <td class="py-4 text-right text-xs font-black dark:text-zinc-200">
                                <?php echo e(number_format($income->gross_amount, 2, ',', ' ')); ?> €
                            </td>

                            <td class="py-4 text-right text-xs font-black text-red-500">
                                -<?php echo e(number_format($income->tax_amount, 2, ',', ' ')); ?> €
                            </td>

                            <td class="py-4 text-right">
                                <span class="text-sm font-black text-emerald-500 italic tracking-tighter">
                                    +<?php echo e(number_format($income->net_amount, 2, ',', ' ')); ?> €
                                </span>
                            </td>

                            <td class="py-4 text-right text-xs font-black dark:text-white italic">
                                <?php echo e(number_format($income->capital_after, 2, ',', ' ')); ?> €
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<footer class="pt-20 pb-10 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20 px-4">
    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.5em]">
        © <?php echo e(date('Y')); ?> Finance Pro · Terminal de Ativos Inteligente
    </p>
</footer>

</div> 

<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/livewire/investments-hub.blade.php ENDPATH**/ ?>