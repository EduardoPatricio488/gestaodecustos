<div class="space-y-10 pb-32 overflow-x-hidden"
     x-data="{
        slide: 0,
        total: 9,
        next() { if(this.slide < this.total - 1) this.slide++ },
        prev() { if(this.slide > 0) this.slide-- }
     }"
     x-on:keydown.right.window="next()"
     x-on:keydown.left.window="prev()"
>
    
    <style>
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-15px); } }
        @keyframes shine { from { left: -120%; } to { left: 120%; } }
        .animate-float { animation: float 4s ease-in-out infinite; }
        .perspective-lg { perspective: 2000px; }
    </style>

    
    <div class="flex flex-col items-center gap-6 pt-6">
        <div class="inline-flex p-1 bg-zinc-100 dark:bg-zinc-900 rounded-full border border-zinc-200 dark:border-zinc-800 shadow-inner">
            <button wire:click="setView('year')" class="px-6 py-2 rounded-full text-[10px] font-bold uppercase transition-all <?php echo e($view === 'year' ? 'bg-white dark:bg-zinc-800 shadow text-brand-600' : 'text-zinc-500'); ?>">Ano</button>
            <button wire:click="setView('month')" class="px-6 py-2 rounded-full text-[10px] font-bold uppercase transition-all <?php echo e($view === 'month' ? 'bg-white dark:bg-zinc-800 shadow text-brand-600' : 'text-zinc-500'); ?>">Mês</button>
        </div>

        <div class="flex items-center gap-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($view === 'year'): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $availableYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <button wire:click="setYear(<?php echo e($y); ?>)" class="text-xs font-black <?php echo e($year == $y ? 'text-brand-500 underline' : 'text-zinc-400'); ?>"><?php echo e($y); ?></button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <?php else: ?>
                <select wire:model.live="month" class="bg-transparent border-none text-xs font-bold uppercase tracking-widest focus:ring-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = range(1, 12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($m); ?>"><?php echo e(Carbon\Carbon::create()->month($m)->translatedFormat('F')); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div class="max-w-xl mx-auto px-4 perspective-lg">
        <div class="relative min-h-[600px] w-full flex items-center justify-center">

            <?php
                $base = "absolute inset-0 p-10 rounded-[3.5rem] flex flex-col justify-center text-center shadow-2xl border border-white/10 transition-all duration-700 ease-out backdrop-blur-xl overflow-hidden";
            ?>

            
            <div x-show="slide === 0" class="<?php echo e($base); ?> bg-gradient-to-br from-brand-600 to-indigo-900 text-white">
    <p class="text-[12px] font-black uppercase tracking-widest opacity-70 mb-4">O Teu Resumo Financeiro</p>

    
    <h1 class="text-[70px] md:text-[100px] font-black italic leading-none animate-float uppercase">
        <?php echo e($view === 'year' ? $year : Carbon\Carbon::create()->month($month)->translatedFormat('F')); ?>

    </h1>

    <p class="text-white/60 font-bold uppercase tracking-[0.3em] mt-8">Finance Pro Wrapped</p>
</div>

            
            <div x-show="slide === 1" x-cloak class="<?php echo e($base); ?> bg-zinc-950 text-white border-red-500/30 border-2">
                <p class="text-[10px] font-black uppercase tracking-widest text-red-500 mb-6">Volume de Transações</p>
                <p class="text-[80px] font-black italic leading-none text-red-500"><?php echo e(number_format($spent, 0, ',', ' ')); ?>€</p>
                <div class="mt-10 grid grid-cols-2 gap-4 text-left">
                    <div class="bg-white/5 p-4 rounded-3xl">
                        <p class="text-[10px] uppercase opacity-50">Transações</p>
                        <p class="text-xl font-black"><?php echo e($transactionCount); ?></p>
                    </div>
                    <div class="bg-white/5 p-4 rounded-3xl">
                        <p class="text-[10px] uppercase opacity-50">Média/Mês</p>
                        <p class="text-xl font-black"><?php echo e(number_format($avgSpending, 0)); ?>€</p>
                    </div>
                </div>
            </div>

            
            <div x-show="slide === 2" x-cloak class="<?php echo e($base); ?> bg-emerald-600 text-white">
                <p class="text-[10px] font-black uppercase tracking-widest text-emerald-200 mb-2">Escudo Financeiro</p>
                <p class="text-[90px] font-black italic leading-none mb-8 animate-float"><?php echo e(number_format($saved, 0)); ?>€</p>
                <div class="relative h-4 bg-emerald-900/40 rounded-full overflow-hidden mb-4">
                    <div class="absolute h-full bg-emerald-300 transition-all duration-1000" style="width: <?php echo e($savingsRate); ?>%"></div>
                </div>
                <p class="text-xs font-bold uppercase">Taxa de Poupança: <?php echo e($savingsRate); ?>%</p>
            </div>

            
            <div x-show="slide === 3" x-cloak class="<?php echo e($base); ?> bg-orange-500 text-white">
                <p class="text-[10px] font-black uppercase tracking-widest text-orange-100 mb-8">O Grande Impacto</p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($biggestExpense): ?>
                    <div class="space-y-4">
                        <p class="text-4xl font-black italic leading-tight uppercase"><?php echo e($biggestExpense->description); ?></p>
                        <p class="text-7xl font-black tabular-nums"><?php echo e(number_format($biggestExpense->amount, 0)); ?>€</p>
                    </div>
                <?php else: ?>
                    <p class="italic">Sem registos significativos.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div x-show="slide === 4" x-cloak class="<?php echo e($base); ?> bg-indigo-800 text-white">
                <p class="text-[10px] font-black uppercase tracking-widest text-indigo-300 mb-10">Onde o dinheiro flui</p>
                <div class="space-y-6">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $topCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="flex items-center justify-between bg-white/10 p-5 rounded-3xl">
                            <span class="font-black italic text-xl">#<?php echo e($index + 1); ?> <?php echo e($cat->name); ?></span>
                            <span class="font-bold opacity-70"><?php echo e(number_format($cat->expenses_sum_amount, 0)); ?>€</span>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>

            
            <div x-show="slide === 5" x-cloak class="<?php echo e($base); ?> bg-amber-500 text-white">
                <p class="text-[10px] font-black uppercase tracking-widest text-amber-900 mb-8">Evolução de Metas</p>
                <p class="text-[140px] font-black italic leading-none animate-float"><?php echo e($goalsCompleted); ?></p>
                <p class="text-sm font-black uppercase tracking-widest mt-4">Objetivos Concluídos</p>
            </div>

            
            <div x-show="slide === 6" x-cloak class="<?php echo e($base); ?> bg-zinc-900 text-white border-emerald-500/20 border-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-emerald-400 mb-6">Património Ativo</p>
                <div class="space-y-2 mb-10">
                    <p class="text-[10px] uppercase opacity-50">Valor da Carteira</p>
                    <p class="text-6xl font-black italic text-emerald-400"><?php echo e(number_format($portfolioValue, 0)); ?>€</p>
                </div>
                <div class="bg-white/5 p-6 rounded-[2.5rem] flex items-center justify-between">
                    <div class="text-left">
                        <p class="text-[10px] uppercase opacity-50">Ganhos Totais</p>
                        <p class="text-2xl font-black <?php echo e($portfolioGain >= 0 ? 'text-emerald-400' : 'text-red-400'); ?>">
                            <?php echo e($portfolioGain >= 0 ? '+' : ''); ?><?php echo e(number_format($portfolioGain, 0)); ?>€
                        </p>
                    </div>
                    <div class="size-12 rounded-full bg-emerald-500/20 flex items-center justify-center">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'chart-bar','class' => 'size-6 text-emerald-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chart-bar','class' => 'size-6 text-emerald-400']); ?>
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

            
            <div x-show="slide === 7" x-cloak class="<?php echo e($base); ?> bg-gradient-to-br from-purple-700 to-indigo-900 text-white">
                <p class="text-[10px] font-black uppercase tracking-widest text-purple-200 mb-10">Ritmo de Gastos</p>
                <div class="space-y-8">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($worstMonth): ?>
                        <div class="text-left bg-black/20 p-6 rounded-3xl border border-white/5">
                            <p class="text-[10px] uppercase text-red-300 font-bold mb-1">Mês de Maior Gasto</p>
                            <div class="flex justify-between items-end">
                                <p class="text-3xl font-black italic"><?php echo e(Carbon\Carbon::create()->month((int)$worstMonth->month)->translatedFormat('F')); ?></p>
                                <p class="text-xl font-bold"><?php echo e(number_format($worstMonth->total, 0)); ?>€</p>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bestMonth): ?>
                        <div class="text-left bg-white/10 p-6 rounded-3xl border border-white/5">
                            <p class="text-[10px] uppercase text-emerald-300 font-bold mb-1">Mês de Maior Economia</p>
                            <div class="flex justify-between items-end">
                                <p class="text-3xl font-black italic"><?php echo e(Carbon\Carbon::create()->month((int)$bestMonth->month)->translatedFormat('F')); ?></p>
                                <p class="text-xl font-bold"><?php echo e(number_format($bestMonth->total, 0)); ?>€</p>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div x-show="slide === 8" x-cloak class="<?php echo e($base); ?> bg-white dark:bg-zinc-950 text-zinc-900 dark:text-white border-brand-500/20 border-8">
                <p class="text-[10px] font-black uppercase tracking-widest text-brand-600 mb-6">O Teu Veredito</p>
                <p class="text-[120px] font-black italic leading-none text-brand-600 mb-2 drop-shadow-xl"><?php echo e($score); ?></p>
                <p class="text-2xl font-black italic uppercase mb-10"><?php echo e($scoreGrade); ?></p>

                <div class="space-y-2 mb-10 max-w-xs mx-auto w-full">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $scoreFactors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $factor => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                <div class="h-full bg-brand-500" style="width: <?php echo e($val); ?>%"></div>
                            </div>
                            <span class="text-[8px] font-bold uppercase opacity-50"><?php echo e($factor); ?></span>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>

                <button wire:click="shareToSocial" class="w-full py-5 bg-brand-600 text-white rounded-3xl font-black uppercase tracking-widest text-xs shadow-xl hover:scale-105 active:scale-95 transition-all">
                    Partilhar Resultados
                </button>
            </div>

        </div>

        
        <div class="flex items-center justify-between mt-10 px-4">
            <button @click="prev()" class="size-12 rounded-2xl bg-zinc-100 dark:bg-zinc-900 text-zinc-400 disabled:opacity-10" :disabled="slide === 0">
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'chevron-left','class' => 'size-5 mx-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-left','class' => 'size-5 mx-auto']); ?>
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
            <div class="flex gap-2">
                <template x-for="i in total" :key="i">
                    <div class="h-1.5 rounded-full transition-all" :class="slide === i-1 ? 'bg-brand-500 w-8' : 'bg-zinc-200 dark:bg-zinc-800 w-1.5'"></div>
                </template>
            </div>
            <button @click="next()" class="size-12 rounded-2xl bg-brand-600 text-white shadow-lg shadow-brand-500/20 disabled:opacity-10" :disabled="slide === total-1">
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'chevron-right','class' => 'size-5 mx-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-right','class' => 'size-5 mx-auto']); ?>
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
    </div>
</div>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/livewire/wrapped-report.blade.php ENDPATH**/ ?>