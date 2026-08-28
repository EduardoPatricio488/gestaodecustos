<div class="space-y-10 pb-24"
    x-data="{
        chartInstance: null,
        initChart(labels, nominal, real) {
            if (this.chartInstance) { this.chartInstance.destroy(); }
            const ctx = document.getElementById('retirementChart');
            if (!ctx) return;
            this.chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Valor Nominal (€)',
                            data: nominal,
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99,102,241,0.08)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 0,
                            borderWidth: 2.5,
                        },
                        {
                            label: 'Valor Real (pós-inflação)',
                            data: real,
                            borderColor: '#10b981',
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.4,
                            pointRadius: 0,
                            borderWidth: 2,
                            borderDash: [6, 3],
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: true, position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { size: 10, weight: 'bold' } } },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ` ${ctx.dataset.label}: ${new Intl.NumberFormat('pt-PT', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(ctx.raw)}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.04)' },
                            ticks: { callback: (v) => `${(v/1000).toFixed(0)}k€`, font: { size: 10 } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 }, callback: (val, i) => (i % 5 === 0) ? this.getLabelForValue(val) + ' anos' : '' }
                        }
                    }
                }
            });
        }
    }"
    x-init="initChart(<?php echo e(json_encode($results['chartLabels'])); ?>, <?php echo e(json_encode($results['chartNominal'])); ?>, <?php echo e(json_encode($results['chartReal'])); ?>)"
    x-on:livewire:navigated.window="initChart(<?php echo e(json_encode($results['chartLabels'])); ?>, <?php echo e(json_encode($results['chartNominal'])); ?>, <?php echo e(json_encode($results['chartReal'])); ?>)"
>

    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <div class="relative">
                <div class="absolute inset-0 bg-indigo-500/20 blur-2xl rounded-full"></div>
                <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'clock','class' => 'w-10 h-10 text-indigo-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'clock','class' => 'w-10 h-10 text-indigo-600']); ?>
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
                    <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">Simulador de Reforma</h1>
                    <?php if (isset($component)) { $__componentOriginal4cc377eda9b63b796b6668ee7832d023 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4cc377eda9b63b796b6668ee7832d023 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::badge.index','data' => ['variant' => 'neutral','class' => 'bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'neutral','class' => 'bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Planeamento Futuro <?php echo $__env->renderComponent(); ?>
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
                <p class="text-sm text-zinc-500 font-medium italic mt-2">Projeta o teu capital e rendimento mensal na reforma</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">

        
        <div class="xl:col-span-4 space-y-6">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-8 shadow-sm space-y-6">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">Parâmetros</p>

                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 px-1">Idade Atual</label>
                        <input wire:model.live="currentAge" type="number" min="18" max="80"
                            class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-4 text-sm font-black dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/30 transition-all" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 px-1">Idade Reforma</label>
                        <input wire:model.live="retirementAge" type="number" min="50" max="80"
                            class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-4 text-sm font-black dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/30 transition-all" />
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 px-1">Esperança de Vida</label>
                    <input wire:model.live="lifeExpectancy" type="number" min="70" max="110"
                        class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-4 text-sm font-black dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/30 transition-all" />
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800 pt-5 space-y-4">
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">Capital & Contribuições</p>

                    <div class="space-y-2">
                        <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 px-1">Poupanças Atuais (€)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400 font-black text-sm">€</span>
                            <input wire:model.live="currentSavings" type="number" min="0" step="100"
                                class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl pl-9 pr-4 text-sm font-black dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/30 transition-all" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 px-1">Contribuição Mensal (€)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400 font-black text-sm">€</span>
                            <input wire:model.live="monthlyContribution" type="number" min="0" step="50"
                                class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl pl-9 pr-4 text-sm font-black dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/30 transition-all" />
                        </div>
                    </div>
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800 pt-5 space-y-4">
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">Taxas (%)</p>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 px-1">Retorno Anual</label>
                            <div class="relative">
                                <input wire:model.live="annualReturn" type="number" min="0" max="30" step="0.5"
                                    class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl pl-4 pr-7 text-sm font-black dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/30 transition-all" />
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-400 font-black text-xs">%</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 px-1">Inflação</label>
                            <div class="relative">
                                <input wire:model.live="inflationRate" type="number" min="0" max="20" step="0.5"
                                    class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl pl-4 pr-7 text-sm font-black dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/30 transition-all" />
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-400 font-black text-xs">%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800 pt-5 space-y-2">
                    <label class="text-[9px] font-black uppercase tracking-widest text-zinc-400 px-1">Rendimento Mensal Alvo (€)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400 font-black text-sm">€</span>
                        <input wire:model.live="targetMonthlyIncome" type="number" min="0" step="100"
                            class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl pl-9 pr-4 text-sm font-black dark:text-white outline-none focus:ring-2 focus:ring-indigo-500/30 transition-all" />
                    </div>
                    <p class="text-[9px] text-zinc-400 font-medium px-1">Quanto queres receber por mês na reforma</p>
                </div>
            </div>
        </div>

        
        <div class="xl:col-span-8 space-y-6">

            
            <div class="relative overflow-hidden p-8 rounded-[2.5rem] shadow-2xl border <?php echo e($results['onTrack'] ? 'bg-emerald-600 border-emerald-700' : 'bg-zinc-950 border-zinc-800'); ?>">
                <div class="absolute -right-10 -top-10 size-40 <?php echo e($results['onTrack'] ? 'bg-white/10' : 'bg-indigo-500/10'); ?> blur-3xl rounded-full"></div>
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.3em] <?php echo e($results['onTrack'] ? 'text-emerald-100' : 'text-indigo-400'); ?> mb-2">
                            <?php echo e($results['onTrack'] ? '✅ No Caminho Certo' : '⚠️ Atenção: Défice Previsto'); ?>

                        </p>
                        <h2 class="text-6xl font-black text-white tracking-tighter italic leading-none">
                            <?php echo e(number_format($results['nominalCapital'], 0, ',', '.')); ?><small class="text-2xl ml-1">€</small>
                        </h2>
                        <p class="text-[11px] font-bold text-white/60 mt-2 uppercase tracking-widest">
                            Capital acumulado aos <?php echo e($retirementAge); ?> anos · <?php echo e($results['years']); ?> anos de poupança
                        </p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-[9px] font-black uppercase tracking-widest <?php echo e($results['onTrack'] ? 'text-emerald-100' : 'text-zinc-500'); ?> mb-1">Rendimento Mensal Est.</p>
                        <p class="text-3xl font-black text-white italic"><?php echo e(number_format($results['monthlyIncome'], 0, ',', '.')); ?>€</p>
                        <p class="text-[9px] <?php echo e($results['onTrack'] ? 'text-emerald-200' : 'text-zinc-500'); ?> font-bold mt-1">
                            Objetivo: <?php echo e(number_format($targetMonthlyIncome, 0, ',', '.')); ?>€/mês
                        </p>
                    </div>
                </div>
            </div>

            
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-[2rem] shadow-sm">
                    <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-1">Valor Real (€)</p>
                    <p class="text-2xl font-black text-emerald-500 tracking-tighter italic"><?php echo e(number_format($results['realCapital'], 0, ',', '.')); ?></p>
                    <p class="text-[9px] text-zinc-400 mt-1">Poder de compra de hoje</p>
                </div>
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-[2rem] shadow-sm">
                    <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-1">Rendim. Real/mês</p>
                    <p class="text-2xl font-black text-indigo-500 tracking-tighter italic"><?php echo e(number_format($results['realMonthlyIncome'], 0, ',', '.')); ?>€</p>
                    <p class="text-[9px] text-zinc-400 mt-1">Pós-inflação</p>
                </div>
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-[2rem] shadow-sm">
                    <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-1">Total Investido</p>
                    <p class="text-2xl font-black dark:text-white tracking-tighter italic"><?php echo e(number_format($results['totalContributed'], 0, ',', '.')); ?>€</p>
                    <p class="text-[9px] text-zinc-400 mt-1">Do teu bolso</p>
                </div>
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-[2rem] shadow-sm">
                    <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-1">Juros Compostos</p>
                    <p class="text-2xl font-black text-amber-500 tracking-tighter italic"><?php echo e(number_format(max(0, $results['totalGrowth']), 0, ',', '.')); ?>€</p>
                    <p class="text-[9px] text-zinc-400 mt-1">Dinheiro gerado</p>
                </div>
            </div>

            
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-8 shadow-sm"
                wire:ignore
                x-on:livewire:update.window="
                    $nextTick(() => initChart(
                        <?php echo e(json_encode($results['chartLabels'])); ?>,
                        <?php echo e(json_encode($results['chartNominal'])); ?>,
                        <?php echo e(json_encode($results['chartReal'])); ?>

                    ))
                ">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400 mb-6">Evolução do Capital até à Reforma</p>
                <canvas id="retirementChart" height="80"></canvas>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$results['onTrack']): ?>
                <div class="bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 rounded-[2rem] p-6 flex gap-5 items-start">
                    <div class="p-3 bg-amber-500/10 rounded-2xl text-amber-600 shrink-0">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'light-bulb','class' => 'size-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'light-bulb','class' => 'size-6']); ?>
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
                        <p class="text-sm font-black text-amber-800 dark:text-amber-200 uppercase tracking-tight">
                            Aumenta a contribuição para atingir o objetivo
                        </p>
                        <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">
                            Para receber <strong><?php echo e(number_format($targetMonthlyIncome, 0, ',', '.')); ?>€/mês</strong> na reforma, precisas de poupar
                            <strong><?php echo e(number_format($results['requiredMonthly'], 0, ',', '.')); ?>€/mês</strong> em vez de
                            <?php echo e(number_format($monthlyContribution, 0, ',', '.')); ?>€.
                            Défice atual: <strong class="text-red-600"><?php echo e(number_format(abs($results['gap']), 0, ',', '.')); ?>€/mês</strong>.
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 rounded-[2rem] p-6 flex gap-5 items-start">
                    <div class="p-3 bg-emerald-500/10 rounded-2xl text-emerald-600 shrink-0">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'check-circle','variant' => 'solid','class' => 'size-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check-circle','variant' => 'solid','class' => 'size-6']); ?>
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
                        <p class="text-sm font-black text-emerald-800 dark:text-emerald-200 uppercase tracking-tight">
                            Estás no caminho certo!
                        </p>
                        <p class="text-sm text-emerald-700 dark:text-emerald-300 mt-1">
                            Com as contribuições atuais terás <strong><?php echo e(number_format($results['monthlyIncome'], 0, ',', '.')); ?>€/mês</strong> na reforma,
                            superando o teu objetivo de <?php echo e(number_format($targetMonthlyIncome, 0, ',', '.')); ?>€.
                            Excedente: <strong>+<?php echo e(number_format(abs($results['gap']), 0, ',', '.')); ?>€/mês</strong>.
                        </p>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2.5rem] p-8 shadow-sm space-y-4">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">Decomposição do Capital Final</p>
                <?php
                    $total = max(1, $results['nominalCapital']);
                    $pctContrib = min(100, ($results['totalContributed'] / $total) * 100);
                    $pctGrowth = 100 - $pctContrib;
                ?>
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="font-bold dark:text-white flex items-center gap-2">
                            <span class="size-3 rounded-full bg-indigo-500 inline-block"></span> Capital Investido
                        </span>
                        <span class="font-black dark:text-white"><?php echo e(number_format($results['totalContributed'], 0, ',', '.')); ?>€ (<?php echo e(round($pctContrib)); ?>%)</span>
                    </div>
                    <div class="h-3 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-500 rounded-full transition-all duration-700" style="width: <?php echo e($pctContrib); ?>%"></div>
                    </div>
                    <div class="flex justify-between items-center text-sm mt-2">
                        <span class="font-bold dark:text-white flex items-center gap-2">
                            <span class="size-3 rounded-full bg-emerald-500 inline-block"></span> Juros Compostos
                        </span>
                        <span class="font-black text-emerald-600">+<?php echo e(number_format(max(0, $results['totalGrowth']), 0, ',', '.')); ?>€ (<?php echo e(round($pctGrowth)); ?>%)</span>
                    </div>
                    <div class="h-3 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full transition-all duration-700" style="width: <?php echo e($pctGrowth); ?>%"></div>
                    </div>
                </div>

                <p class="text-[9px] text-zinc-400 font-medium italic pt-2">
                    * Simulação baseada em retorno anual composto de <?php echo e($annualReturn); ?>% e inflação de <?php echo e($inflationRate); ?>%. Não constitui aconselhamento financeiro.
                </p>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/livewire/retirement-simulator.blade.php ENDPATH**/ ?>