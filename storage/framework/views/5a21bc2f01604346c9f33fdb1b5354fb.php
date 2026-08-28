<div class="space-y-10 pb-20">
    
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 relative z-10">
        <div class="flex items-center gap-5">
            <div class="relative group">
                <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full group-hover:bg-brand-500/40 transition-all duration-700"></div>
                <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl shadow-brand-500/10 text-brand-600">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'presentation-chart-line','class' => 'w-10 h-10']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'presentation-chart-line','class' => 'w-10 h-10']); ?>
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
                <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Resultados (P&L)</h1>
                <p class="text-sm text-zinc-500 font-medium italic mt-2 text-zinc-400">Análise estrutural de performance, margens e rentabilidade líquida</p>
            </div>
        </div>

        
        <div class="flex items-center gap-1 bg-zinc-100 dark:bg-zinc-900 p-1.5 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-inner">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [now()->year - 1, now()->year]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <button wire:click="setYear(<?php echo e($y); ?>)"
                    class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all <?php echo e($year == $y ? 'bg-white dark:bg-zinc-800 shadow-md text-brand-600 dark:text-white' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300'); ?>">
                    Ano <?php echo e($y); ?>

                </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </header>

    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group hover:border-emerald-500/30 transition-all">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Faturação Total (Bruta)</p>
            <h3 class="text-4xl font-black text-emerald-600 tracking-tighter">
                <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                    <?php echo e(number_format($yearlyRevenue, 2, ',', ' ')); ?> €
                </span>
            </h3>
            <div class="mt-4 flex items-center gap-2">
                <div class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Acumulado <?php echo e($year); ?></span>
            </div>
        </div>

        
        <div class="relative overflow-hidden bg-zinc-950 p-8 rounded-[2.5rem] shadow-2xl border border-zinc-800 group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-500/10 blur-[60px] rounded-full -mr-10 -mt-10 group-hover:bg-brand-500/20 transition-all"></div>

            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-white/5 rounded-2xl text-brand-400 shadow-inner">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'banknotes','variant' => 'outline','class' => 'size-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'banknotes','variant' => 'outline','class' => 'size-6']); ?>
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
                    <span class="text-[9px] font-black text-white/50 border border-white/10 px-2 py-1 rounded-lg uppercase tracking-widest italic">Profit & Loss</span>
                </div>
                <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-1">Lucro Líquido Real</p>
                <h3 class="text-4xl font-black text-white tracking-tighter italic">
                    <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                        <?php echo e(number_format($yearlyProfit, 2, ',', ' ')); ?> €
                    </span>
                </h3>
            </div>
        </div>

        
        <div class="glass-card relative overflow-hidden bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm transition-all hover:border-brand-500/30">
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Margem Média Líquida</p>
            <div class="flex items-baseline gap-2">
                <h3 class="text-4xl font-black dark:text-white tracking-tighter"><?php echo e(round($avgMargin, 1)); ?>%</h3>
                <span class="text-[9px] font-bold <?php echo e($avgMargin > 20 ? 'text-emerald-500' : 'text-amber-500'); ?> uppercase italic">Eficiência Operacional</span>
            </div>
            <div class="mt-4 h-1.5 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                <div class="h-full bg-brand-500 shadow-[0_0_10px_#3b82f6]" style="width: <?php echo e($avgMargin); ?>%"></div>
            </div>
        </div>
    </div>

    
    <div class="glass-card bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm overflow-hidden group">
        <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/30 dark:bg-zinc-900/30 flex justify-between items-center">
            <div>
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-1">Mapa de Exploração</h2>
                <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter">Balanço Mensal Detalhado</p>
            </div>
            <?php if (isset($component)) { $__componentOriginal4cc377eda9b63b796b6668ee7832d023 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4cc377eda9b63b796b6668ee7832d023 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::badge.index','data' => ['variant' => 'neutral','class' => 'bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase px-3 py-1 border-none shadow-sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'neutral','class' => 'bg-zinc-100 dark:bg-zinc-800 text-[10px] font-black uppercase px-3 py-1 border-none shadow-sm']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Audit Interno <?php echo $__env->renderComponent(); ?>
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

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[9px] uppercase text-zinc-400 dark:text-zinc-500 font-black tracking-widest text-center">
                        <th class="p-6 text-left">Mês Fiscal</th>
                        <th class="p-6">Faturação (+)</th>
                        <th class="p-6">OpEx / Custos (-)</th>
                        <th class="p-6">Retenção IVA</th>
                        <th class="p-6 text-right">Resultado (=)</th>
                        <th class="p-6 px-10">Margem Operacional</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $monthlyData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="hover:bg-zinc-50 dark:hover:bg-brand-500/5 transition-all duration-300 group/row <?php echo e($data['revenue'] > 0 ? '' : 'opacity-40 grayscale'); ?>">
                            
                            <td class="p-6">
                                <span class="text-sm font-black dark:text-white uppercase tracking-tight"><?php echo e($data['month_name']); ?></span>
                            </td>

                            
                            <td class="p-6 text-center">
                                <span class="text-sm font-bold text-emerald-600">
                                    <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500">
                                        +<?php echo e(number_format($data['revenue'], 2, ',', ' ')); ?> €
                                    </span>
                                </span>
                            </td>

                            
                            <td class="p-6 text-center">
                                <span class="text-sm font-bold text-red-500">
                                    <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500">
                                        -<?php echo e(number_format($data['costs'], 2, ',', ' ')); ?> €
                                    </span>
                                </span>
                            </td>

                            
                            <td class="p-6 text-center text-xs font-black text-zinc-400 italic">
                                <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500">
                                    <?php echo e(number_format($data['vat'], 2, ',', ' ')); ?> €
                                </span>
                            </td>

                            
                            <td class="p-6 text-right">
                                <span class="text-sm font-black dark:text-white <?php echo e($data['profit'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-zinc-900 dark:text-zinc-100'); ?> tracking-tighter">
                                    <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500">
                                        <?php echo e(number_format($data['profit'], 2, ',', ' ')); ?> €
                                    </span>
                                </span>
                            </td>

                            
                            <td class="p-6 px-10">
                                <div class="flex flex-col gap-2 min-w-[120px]">
                                    <div class="flex justify-between items-center">
                                        <span class="text-[9px] font-black uppercase text-zinc-400 tracking-widest">Performance</span>
                                        <span class="text-[10px] font-black dark:text-zinc-300"><?php echo e(round($data['margin'])); ?>%</span>
                                    </div>
                                    <div class="h-1.5 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden border border-zinc-50 dark:border-zinc-800">
                                        <div class="h-full transition-all duration-1000 ease-out <?php echo e($data['margin'] > 30 ? 'bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]' : ($data['margin'] > 0 ? 'bg-brand-500 shadow-[0_0_10px_rgba(59,130,246,0.5)]' : 'bg-red-500')); ?>"
                                             style="width: <?php echo e(min(max($data['margin'], 0), 100)); ?>%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>

                
                <tfoot class="bg-zinc-950 text-white font-black uppercase text-[10px] tracking-widest italic">
                    <tr>
                        <td class="p-8">Consolidado Anual</td>
                        <td class="p-8 text-center text-emerald-400">
                            <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500 inline-block">
                                <?php echo e(number_format($yearlyRevenue, 2, ',', ' ')); ?> €
                            </span>
                        </td>
                        <td class="p-8 text-center">
                            <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500 inline-block">
                                <?php echo e(number_format($monthlyData->sum('costs'), 2, ',', ' ')); ?> €
                            </span>
                        </td>
                        <td class="p-8 text-center text-zinc-500 font-bold">
                            <span :class="privacyMode ? 'blur-sm select-none' : ''" class="transition-all duration-500 inline-block">
                                <?php echo e(number_format($monthlyData->sum('vat'), 2, ',', ' ')); ?> €
                            </span>
                        </td>
                        <td class="p-8 text-right text-brand-400 text-lg tracking-tighter">
                            <span :class="privacyMode ? 'blur-md select-none' : ''" class="transition-all duration-500 inline-block">
                                <?php echo e(number_format($yearlyProfit, 2, ',', ' ')); ?> €
                            </span>
                        </td>
                        <td class="p-8 text-center text-zinc-400">
                            <?php echo e(round($avgMargin)); ?>% Global
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        
        <div class="lg:col-span-2 p-8 bg-zinc-50 dark:bg-zinc-900/50 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-inner relative overflow-hidden group">
            
            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'information-circle','class' => 'absolute -right-6 -bottom-6 size-32 text-zinc-100 dark:text-zinc-800/50 rotate-12 pointer-events-none']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'information-circle','class' => 'absolute -right-6 -bottom-6 size-32 text-zinc-100 dark:text-zinc-800/50 rotate-12 pointer-events-none']); ?>
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

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-brand-500/10 rounded-xl text-brand-600">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'shield-check','variant' => 'outline','class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shield-check','variant' => 'outline','class' => 'size-5']); ?>
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
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500">Notas de Auditoria Financeira</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <p class="text-xs font-black dark:text-white uppercase tracking-tight">Cálculo de Imposto (IRC)</p>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed font-medium">
                            O Lucro Líquido apresentado já deduz automaticamente uma estimativa de <b>21% para IRC</b> sobre o lucro bruto positivo acumulado. Este valor serve apenas como provisão de tesouraria.
                        </p>
                    </div>

                    <div class="space-y-2">
                        <p class="text-xs font-black dark:text-white uppercase tracking-tight">Consolidação de Custos</p>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed font-medium">
                            As despesas apresentadas incluem o <b>Custo Total de Payroll (RH)</b> e todos os gastos operacionais marcados com a etiqueta de empresa no módulo de despesas.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] shadow-sm flex flex-col justify-center items-center text-center group">
            <div class="relative mb-4">
                <div class="size-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-600">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'arrow-path','class' => 'size-6 animate-spin-slow']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-path','class' => 'size-6 animate-spin-slow']); ?>
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
                <div class="absolute -bottom-1 -right-1 size-4 bg-emerald-500 border-4 border-white dark:border-zinc-900 rounded-full"></div>
            </div>

            <h4 class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Estado dos Dados</h4>
            <p class="text-sm font-bold dark:text-white mt-1 uppercase">Sincronizado</p>
            <p class="text-[9px] text-zinc-500 italic mt-3 font-medium uppercase tracking-tighter">Última atualização: <br> <?php echo e(now()->format('d/m/Y H:i')); ?></p>
        </div>
    </div>

    
    <footer class="pt-10 border-t border-zinc-100 dark:border-zinc-800 mt-10 flex flex-col md:flex-row justify-between items-center gap-4 opacity-70">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © <?php echo e(date('Y')); ?> <?php echo e(auth()->user()->currentWorkspace->name); ?> · Relatório de Exploração Anual
        </p>
        <div class="flex gap-6">
            <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest flex items-center gap-2">
                <div class="size-1.5 rounded-full bg-emerald-500"></div>
                Audit Pass
            </span>
            <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest"><?php echo e(config('app.name')); ?> SaaS Pro</span>
        </div>
    </footer>

    
    <style>
        .animate-spin-slow {
            animation: spin 8s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e4e4e7; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #27272a; }
    </style>
</div>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/livewire/business/business-pnl-hub.blade.php ENDPATH**/ ?>