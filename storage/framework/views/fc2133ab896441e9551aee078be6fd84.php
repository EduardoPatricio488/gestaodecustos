<div class="space-y-8 pb-24" x-data="netWorthHub()" x-init="init()">

    
    
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-1">
        <div class="flex items-center gap-5">
            <div class="relative">
                <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full"></div>
                <div class="relative p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-xl">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'briefcase','class' => 'w-8 h-8 text-brand-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'briefcase','class' => 'w-8 h-8 text-brand-600']); ?>
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
                    <h1 class="text-3xl font-black dark:text-white uppercase tracking-tighter italic">Património Líquido</h1>
                    <span class="text-[9px] font-black uppercase tracking-widest bg-zinc-100 dark:bg-zinc-800 text-zinc-500 px-3 py-1 rounded-full">Wealth Report</span>
                </div>
                <p class="text-xs text-zinc-400 mt-1">Análise estrutural · ativos · passivos · solvabilidade · <?php echo e(now()->format('d M Y')); ?></p>
            </div>
        </div>
        <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['href' => ''.e(route('dashboard')).'','variant' => 'ghost','icon' => 'arrow-left','wire:navigate' => true,'class' => 'rounded-2xl font-bold self-start md:self-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('dashboard')).'','variant' => 'ghost','icon' => 'arrow-left','wire:navigate' => true,'class' => 'rounded-2xl font-bold self-start md:self-auto']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Voltar <?php echo $__env->renderComponent(); ?>
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

    
    
    
    <div class="bg-zinc-950 text-white p-8 lg:p-10 rounded-[2.5rem] shadow-2xl relative overflow-hidden border border-zinc-800">
        <div class="absolute top-0 right-0 w-96 h-96 bg-brand-500/10 blur-[120px] rounded-full -mr-32 -mt-32 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-500/5 blur-[100px] rounded-full -ml-20 -mb-20 pointer-events-none"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

            
            <div class="lg:col-span-5 space-y-6">
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.4em] text-brand-400 mb-2">Valor Líquido Total</p>
                    <div class="text-5xl sm:text-7xl font-black tracking-tighter italic leading-none tabular-nums">
                        <?php $nw = $netWorth; ?>
                        <span class="<?php echo e($nw >= 0 ? 'text-white' : 'text-red-400'); ?>">
                            <?php echo e(number_format(abs($nw), 2, ',', ' ')); ?> <span class="text-2xl sm:text-3xl">€</span>
                        </span>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($netWorth < 0): ?>
                        <p class="text-red-400 text-xs font-bold mt-2 uppercase tracking-widest">⚠ Passivo supera ativos</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div class="flex items-center gap-4">
                    <div class="relative w-16 h-16 flex-shrink-0">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 56 56">
                            <circle cx="28" cy="28" r="22" fill="none" stroke="rgba(255,255,255,0.08)" stroke-width="5"/>
                            <circle cx="28" cy="28" r="22" fill="none"
                                stroke="<?php echo e($healthScore >= 70 ? '#10b981' : ($healthScore >= 40 ? '#f59e0b' : '#ef4444')); ?>"
                                stroke-width="5"
                                stroke-linecap="round"
                                stroke-dasharray="<?php echo e(round($healthScore * 1.382)); ?> 138.2"
                                style="transition: stroke-dasharray 1.5s cubic-bezier(.4,0,.2,1)"/>
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center text-xs font-black"><?php echo e(round($healthScore)); ?></span>
                    </div>
                    <div>
                        <p class="text-[9px] uppercase font-black tracking-widest text-zinc-400">Score de Saúde</p>
                        <p class="text-sm font-bold <?php echo e($healthScore >= 70 ? 'text-emerald-400' : ($healthScore >= 40 ? 'text-amber-400' : 'text-red-400')); ?>">
                            <?php echo e($healthScore >= 70 ? 'Sólida' : ($healthScore >= 40 ? 'Moderada' : 'Crítica')); ?>

                        </p>
                        <p class="text-[10px] text-zinc-500 mt-0.5">Taxa de poupança: <?php echo e(round($avgSavingsRate)); ?>%</p>
                    </div>
                </div>

                
                <div class="grid grid-cols-2 gap-3">
                    <div class="px-5 py-4 bg-white/5 rounded-2xl border border-white/8">
                        <p class="text-[9px] uppercase font-black text-zinc-500 tracking-widest mb-1">Ativos Totais</p>
                        <p class="text-xl font-black text-emerald-400 tracking-tighter tabular-nums"><?php echo e(number_format($totalAssets, 0, ',', ' ')); ?> €</p>
                    </div>
                    <div class="px-5 py-4 bg-white/5 rounded-2xl border border-white/8">
                        <p class="text-[9px] uppercase font-black text-zinc-500 tracking-widest mb-1">Passivos</p>
                        <p class="text-xl font-black text-red-400 tracking-tighter tabular-nums"><?php echo e(number_format($liabilities, 0, ',', ' ')); ?> €</p>
                    </div>
                    <div class="px-5 py-4 bg-white/5 rounded-2xl border border-white/8">
                        <p class="text-[9px] uppercase font-black text-zinc-500 tracking-widest mb-1">Liquidez</p>
                        <p class="text-xl font-black text-sky-400 tracking-tighter tabular-nums"><?php echo e(number_format($totalBankBalance, 0, ',', ' ')); ?> €</p>
                    </div>
                    <div class="px-5 py-4 bg-white/5 rounded-2xl border border-white/8">
                        <p class="text-[9px] uppercase font-black text-zinc-500 tracking-widest mb-1">Solvabilidade</p>
                        <p class="text-xl font-black <?php echo e($solvencyRatio >= 2 ? 'text-emerald-400' : 'text-amber-400'); ?> tracking-tighter">
                            <?php echo e($solvencyRatio >= 99 ? '∞' : number_format($solvencyRatio, 1, ',', '')); ?>x
                        </p>
                    </div>
                </div>
            </div>

            
            <div class="lg:col-span-4 bg-white/5 p-6 rounded-3xl border border-white/8 space-y-5">
                <p class="text-[9px] font-black uppercase tracking-[0.3em] text-zinc-400">Composição do Ativo</p>

                <?php
                    $slices = [
                        ['label' => 'Liquidez (Bancos)',  'value' => $totalBankBalance,  'color' => '#38bdf8', 'pct' => $liquidityRatio],
                        ['label' => 'Investimentos',      'value' => $investmentsValue,  'color' => '#818cf8', 'pct' => $investmentExposure],
                        ['label' => 'Metas de Poupança', 'value' => $goalsSaved,         'color' => '#34d399', 'pct' => $savingsHealth],
                    ];
                    $maxVal = max(array_column($slices, 'value'), 0.01);
                ?>

                
                <?php
                    $total = $totalAssets ?: 1;
                    $donutR = 40; $donutC = 50;
                    $circ = 2 * M_PI * $donutR;
                    $offset = 0;
                    $donutColors = ['#38bdf8','#818cf8','#34d399'];
                    $donutVals  = [$totalBankBalance, $investmentsValue, $goalsSaved];
                ?>
                <div class="flex items-center justify-center">
                    <svg viewBox="0 0 100 100" class="w-36 h-36">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $donutVals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $pct   = $val / $total;
                                $dash  = $pct * $circ;
                                $gap   = $circ - $dash;
                            ?>
                            <circle cx="<?php echo e($donutC); ?>" cy="<?php echo e($donutC); ?>" r="<?php echo e($donutR); ?>"
                                fill="none"
                                stroke="<?php echo e($donutColors[$idx]); ?>"
                                stroke-width="12"
                                stroke-dasharray="<?php echo e(round($dash, 2)); ?> <?php echo e(round($gap, 2)); ?>"
                                stroke-dashoffset="<?php echo e(round(-$offset, 2)); ?>"
                                transform="rotate(-90 <?php echo e($donutC); ?> <?php echo e($donutC); ?>)"
                                opacity="0.9"/>
                            <?php $offset += $dash; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <text x="50" y="46" text-anchor="middle" fill="white" font-size="8" font-weight="900" font-family="monospace">NET</text>
                        <text x="50" y="57" text-anchor="middle" fill="white" font-size="7" font-family="monospace">WORTH</text>
                    </svg>
                </div>

                
                <div class="space-y-3.5">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $slices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="space-y-1">
                            <div class="flex justify-between text-[10px] font-bold text-zinc-300">
                                <span class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:<?php echo e($slice['color']); ?>"></span>
                                    <?php echo e($slice['label']); ?>

                                </span>
                                <span style="color:<?php echo e($slice['color']); ?>"><?php echo e(round($slice['pct'])); ?>%</span>
                            </div>
                            <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-1000"
                                    style="width:<?php echo e(min(100, $slice['pct'])); ?>%; background:<?php echo e($slice['color']); ?>; box-shadow:0 0 8px <?php echo e($slice['color']); ?>80"></div>
                            </div>
                            <p class="text-[10px] text-zinc-500 tabular-nums"><?php echo e(number_format($slice['value'], 0, ',', ' ')); ?> €</p>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>

            
            <div class="lg:col-span-3 space-y-3">
                <p class="text-[9px] font-black uppercase tracking-[0.3em] text-zinc-400">Rácios Chave</p>
                <?php
                    $ratios = [
                        ['label' => 'Dívida / Ativo',       'val' => round($debtToAssetRatio, 1).'%',  'good' => $debtToAssetRatio < 30,   'tip' => 'Ideal < 30%'],
                        ['label' => 'Taxa de Poupança',     'val' => round($avgSavingsRate, 1).'%',     'good' => $avgSavingsRate > 20,      'tip' => 'Ideal > 20%'],
                        ['label' => 'Solvabilidade',        'val' => ($solvencyRatio >= 99 ? '∞' : number_format($solvencyRatio,1,',','')).'x', 'good' => $solvencyRatio >= 2, 'tip' => 'Ideal > 2x'],
                        ['label' => 'Exposição Invest.',    'val' => round($investmentExposure, 1).'%', 'good' => $investmentExposure > 30,   'tip' => 'Ideal > 30%'],
                        ['label' => 'Progresso Metas',      'val' => round($goalsProgress, 1).'%',      'good' => $goalsProgress > 50,        'tip' => ''],
                    ];
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $ratios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="flex items-center justify-between px-4 py-3 bg-white/5 rounded-2xl border border-white/8 hover:bg-white/8 transition-colors">
                        <div>
                            <p class="text-[9px] uppercase font-black tracking-widest text-zinc-500"><?php echo e($r['label']); ?></p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r['tip']): ?>
                                <p class="text-[9px] text-zinc-600 mt-0.5"><?php echo e($r['tip']); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <span class="text-sm font-black <?php echo e($r['good'] ? 'text-emerald-400' : 'text-amber-400'); ?> tabular-nums"><?php echo e($r['val']); ?></span>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                
                <div class="px-4 py-3 bg-red-500/10 rounded-2xl border border-red-500/20">
                    <p class="text-[9px] uppercase font-black tracking-widest text-red-400">Custo Anual Subs.</p>
                    <p class="text-sm font-black text-red-300 tabular-nums mt-0.5"><?php echo e(number_format($totalAnnualSubscriptions, 0, ',', ' ')); ?> €/ano</p>
                    <p class="text-[10px] text-red-400/60 mt-0.5"><?php echo e($activeSubscriptions->count()); ?> subscrições ativas</p>
                </div>
            </div>
        </div>
    </div>

    
    
    
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="font-black text-zinc-900 dark:text-white uppercase tracking-tight text-sm">Fluxo de Caixa · 12 Meses</h2>
                <p class="text-xs text-zinc-400 mt-0.5">Rendimentos vs despesas mensais</p>
            </div>
            <div class="flex gap-4 text-[10px] font-black uppercase tracking-widest">
                <span class="flex items-center gap-1.5 text-emerald-500"><span class="w-2.5 h-2.5 rounded bg-emerald-500"></span>Rendimento</span>
                <span class="flex items-center gap-1.5 text-red-400"><span class="w-2.5 h-2.5 rounded bg-red-400"></span>Despesa</span>
                <span class="flex items-center gap-1.5 text-brand-500"><span class="w-2.5 h-2.5 rounded bg-brand-500"></span>Saldo</span>
            </div>
        </div>

        <?php
            $months = $last12Months->values();
            $maxBar = max($months->max('income'), $months->max('expense'), 1);
            $barH   = 120;
        ?>

        <div class="overflow-x-auto">
            <div class="flex items-end gap-1 min-w-[640px]" style="height:<?php echo e($barH + 40); ?>px">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $incH  = round(($m['income'] / $maxBar) * $barH);
                        $expH  = round(($m['expense'] / $maxBar) * $barH);
                        $net   = $m['net'];
                        $netPct = $maxBar > 0 ? ($net / $maxBar) * 100 : 0;
                    ?>
                    <div class="flex-1 flex flex-col items-center gap-0.5 group">
                        
                        <div class="w-full flex justify-center mb-1">
                            <div class="w-1.5 h-1.5 rounded-full <?php echo e($net >= 0 ? 'bg-brand-500' : 'bg-red-400'); ?> opacity-70 group-hover:opacity-100 transition-opacity"></div>
                        </div>
                        
                        <div class="w-full flex items-end justify-center gap-px" style="height:<?php echo e($barH); ?>px">
                            <div class="w-5/12 rounded-t transition-all duration-700 bg-emerald-400/80 hover:bg-emerald-400"
                                style="height:<?php echo e($incH); ?>px; min-height:2px"></div>
                            <div class="w-5/12 rounded-t transition-all duration-700 bg-red-400/80 hover:bg-red-400"
                                style="height:<?php echo e($expH); ?>px; min-height:2px"></div>
                        </div>
                        
                        <p class="text-[9px] font-bold text-zinc-400 uppercase mt-1 group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition-colors">
                            <?php echo e($m['label']); ?>

                        </p>
                        
                        <div class="hidden group-hover:block absolute bg-zinc-900 dark:bg-zinc-800 border border-zinc-700 rounded-xl px-3 py-2 text-[9px] font-bold whitespace-nowrap shadow-xl z-10 -translate-y-full -translate-x-1/2 left-1/2 text-white pointer-events-none">
                            +<?php echo e(number_format($m['income'], 0, ',', ' ')); ?> / -<?php echo e(number_format($m['expense'], 0, ',', ' ')); ?>

                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-zinc-100 dark:border-zinc-800">
            <div class="text-center">
                <p class="text-[9px] uppercase font-black text-zinc-400 tracking-widest">Média Receita (3m)</p>
                <p class="text-lg font-black text-emerald-500 tabular-nums mt-1"><?php echo e(number_format($avg3Income, 0, ',', ' ')); ?> €</p>
            </div>
            <div class="text-center">
                <p class="text-[9px] uppercase font-black text-zinc-400 tracking-widest">Média Despesa (3m)</p>
                <p class="text-lg font-black text-red-400 tabular-nums mt-1"><?php echo e(number_format($avg3Expense, 0, ',', ' ')); ?> €</p>
            </div>
            <div class="text-center">
                <p class="text-[9px] uppercase font-black text-zinc-400 tracking-widest">Taxa Poupança (3m)</p>
                <p class="text-lg font-black <?php echo e($avgSavingsRate > 20 ? 'text-brand-500' : 'text-amber-500'); ?> tabular-nums mt-1"><?php echo e(round($avgSavingsRate)); ?>%</p>
            </div>
        </div>
    </div>

    
    
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        
        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="font-black text-zinc-900 dark:text-white uppercase tracking-tight text-sm">Carteira de Investimentos</h2>
                    <p class="text-xs text-zinc-400 mt-0.5">Valor de mercado atual</p>
                </div>
                <div class="text-right">
                    <p class="text-xl font-black text-zinc-900 dark:text-white tabular-nums"><?php echo e(number_format($investmentsValue, 0, ',', ' ')); ?> €</p>
                    <p class="text-[10px] font-bold <?php echo e($unrealizedGain >= 0 ? 'text-emerald-500' : 'text-red-400'); ?> tabular-nums">
                        <?php echo e($unrealizedGain >= 0 ? '+' : ''); ?><?php echo e(number_format($unrealizedGain, 0, ',', ' ')); ?> € (<?php echo e($unrealizedGain >= 0 ? '+' : ''); ?><?php echo e(round($unrealizedGainPct, 1)); ?>%)
                    </p>
                </div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($investmentsByType->count()): ?>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-5">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $investmentsByType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php $typePct = $investmentsValue > 0 ? ($data['value'] / $investmentsValue * 100) : 0; ?>
                        <div class="bg-zinc-50 dark:bg-zinc-800 rounded-2xl p-3.5">
                            <p class="text-[9px] uppercase font-black text-zinc-500 tracking-widest"><?php echo e($type); ?></p>
                            <p class="text-base font-black text-zinc-900 dark:text-white tabular-nums mt-0.5"><?php echo e(number_format($data['value'], 0, ',', ' ')); ?> €</p>
                            <div class="flex items-center gap-2 mt-1.5">
                                <div class="flex-1 h-0.5 bg-zinc-200 dark:bg-zinc-700 rounded-full">
                                    <div class="h-full bg-indigo-500 rounded-full" style="width:<?php echo e(round($typePct)); ?>%"></div>
                                </div>
                                <span class="text-[9px] font-bold text-zinc-400"><?php echo e(round($typePct)); ?>%</span>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($topInvestments->count()): ?>
                <div class="space-y-2">
                    <p class="text-[9px] uppercase font-black text-zinc-400 tracking-widest mb-3">Top Posições</p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $topInvestments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $cost   = $inv['quantity'] * $inv['average_price'];
                            $val    = $inv['current_value'];
                            $gain   = $val - $cost;
                            $gainPct = $cost > 0 ? ($gain / $cost * 100) : 0;
                        ?>
                        <div class="flex items-center justify-between py-2.5 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                                    <span class="text-[8px] font-black text-indigo-600 dark:text-indigo-400 uppercase"><?php echo e(substr($inv['symbol'] ?? $inv['name'], 0, 3)); ?></span>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-zinc-900 dark:text-white"><?php echo e(Str::limit($inv['name'], 22)); ?></p>
                                    <p class="text-[9px] text-zinc-400"><?php echo e($inv['type'] ?? '—'); ?> · <?php echo e($inv['quantity']); ?> un.</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-black text-zinc-900 dark:text-white tabular-nums"><?php echo e(number_format($val, 0, ',', ' ')); ?> €</p>
                                <p class="text-[10px] font-bold <?php echo e($gain >= 0 ? 'text-emerald-500' : 'text-red-400'); ?> tabular-nums">
                                    <?php echo e($gain >= 0 ? '+' : ''); ?><?php echo e(round($gainPct, 1)); ?>%
                                </p>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php else: ?>
                <p class="text-sm text-zinc-400 text-center py-6">Sem investimentos registados.</p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="space-y-5">

            
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm">
                <h2 class="font-black text-zinc-900 dark:text-white uppercase tracking-tight text-sm mb-4">Contas Bancárias</h2>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $bankAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="flex items-center justify-between py-2 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
                        <div class="flex items-center gap-2.5">
                            <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:<?php echo e($acc->color ?? '#6366f1'); ?>"></div>
                            <div>
                                <p class="text-xs font-bold text-zinc-800 dark:text-zinc-200"><?php echo e($acc->name); ?></p>
                                <p class="text-[9px] text-zinc-400 uppercase"><?php echo e($acc->type); ?></p>
                            </div>
                        </div>
                        <p class="text-sm font-black <?php echo e($acc->balance >= 0 ? 'text-zinc-900 dark:text-white' : 'text-red-400'); ?> tabular-nums">
                            <?php echo e(number_format($acc->balance, 0, ',', ' ')); ?> €
                        </p>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <p class="text-xs text-zinc-400 text-center py-3">Sem contas.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex justify-between">
                    <span class="text-[9px] uppercase font-black text-zinc-400 tracking-widest">Total Liquidez</span>
                    <span class="text-sm font-black text-sky-500 tabular-nums"><?php echo e(number_format($totalBankBalance, 0, ',', ' ')); ?> €</span>
                </div>
            </div>

            
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-black text-zinc-900 dark:text-white uppercase tracking-tight text-sm">Metas de Poupança</h2>
                    <span class="text-[9px] font-black text-emerald-500"><?php echo e(round($goalsProgress)); ?>% global</span>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $goals->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $goal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php $gp = $goal->target_amount > 0 ? ($goal->current_amount / $goal->target_amount * 100) : 0; ?>
                    <div class="mb-3 last:mb-0">
                        <div class="flex justify-between text-[10px] font-bold mb-1">
                            <span class="text-zinc-700 dark:text-zinc-300 truncate max-w-[60%]"><?php echo e($goal->name); ?></span>
                            <span class="text-zinc-500 tabular-nums"><?php echo e(number_format($goal->current_amount, 0, ',', ' ')); ?> / <?php echo e(number_format($goal->target_amount, 0, ',', ' ')); ?> €</span>
                        </div>
                        <div class="h-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-emerald-400 transition-all duration-700" style="width:<?php echo e(min(100,$gp)); ?>%"></div>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($goal->deadline): ?>
                            <p class="text-[9px] text-zinc-400 mt-0.5">Prazo: <?php echo e(\Carbon\Carbon::parse($goal->deadline)->format('d/m/Y')); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <p class="text-xs text-zinc-400 text-center py-3">Sem metas definidas.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    
    
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="font-black text-zinc-900 dark:text-white uppercase tracking-tight text-sm">Passivos / Dívidas</h2>
                    <p class="text-xs text-zinc-400 mt-0.5">Por liquidar</p>
                </div>
                <span class="text-base font-black text-red-400 tabular-nums"><?php echo e(number_format($liabilities, 0, ',', ' ')); ?> €</span>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($upcomingDebts->count()): ?>
                <div class="mb-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 rounded-2xl px-4 py-3">
                    <p class="text-[9px] uppercase font-black text-amber-600 dark:text-amber-400 tracking-widest">⚠ <?php echo e($upcomingDebts->count()); ?> dívida(s) nos próximos 30 dias</p>
                    <p class="text-sm font-black text-amber-700 dark:text-amber-300 tabular-nums mt-0.5"><?php echo e(number_format($upcomingDebts->sum('amount'), 0, ',', ' ')); ?> €</p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $debts->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="flex items-center justify-between py-2.5 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
                    <div>
                        <p class="text-xs font-bold text-zinc-800 dark:text-zinc-200"><?php echo e($debt->person_name); ?></p>
                        <div class="flex gap-2 mt-0.5">
                            <span class="text-[9px] uppercase font-black px-1.5 py-0.5 rounded bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400"><?php echo e($debt->type); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($debt->due_at): ?>
                                <span class="text-[9px] text-zinc-400"><?php echo e(\Carbon\Carbon::parse($debt->due_at)->format('d/m/Y')); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <p class="text-sm font-black text-red-400 tabular-nums"><?php echo e(number_format($debt->amount, 0, ',', ' ')); ?> €</p>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="text-center py-8">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'check-circle','class' => 'w-8 h-8 text-emerald-400 mx-auto mb-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check-circle','class' => 'w-8 h-8 text-emerald-400 mx-auto mb-2']); ?>
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
                    <p class="text-sm font-bold text-emerald-500">Sem dívidas registadas</p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="font-black text-zinc-900 dark:text-white uppercase tracking-tight text-sm">Subscrições Ativas</h2>
                    <p class="text-xs text-zinc-400 mt-0.5">Compromissos recorrentes</p>
                </div>
                <div class="text-right">
                    <p class="text-base font-black text-red-400 tabular-nums"><?php echo e(number_format($monthlySubscriptionCost, 0, ',', ' ')); ?> €/mês</p>
                    <p class="text-[9px] text-zinc-400 tabular-nums"><?php echo e(number_format($totalAnnualSubscriptions, 0, ',', ' ')); ?> €/ano</p>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $activeSubscriptions->sortByDesc('amount')->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="flex items-center justify-between py-2.5 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                            <span class="text-[9px] font-black text-zinc-500"><?php echo e(strtoupper(substr($sub->name, 0, 2))); ?></span>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-zinc-800 dark:text-zinc-200"><?php echo e($sub->name); ?></p>
                            <p class="text-[9px] text-zinc-400 uppercase"><?php echo e($sub->cycle); ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-black text-zinc-700 dark:text-zinc-300 tabular-nums"><?php echo e(number_format($sub->amount, 2, ',', ' ')); ?> €</p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sub->renewal_date): ?>
                            <p class="text-[9px] text-zinc-400">renova <?php echo e(\Carbon\Carbon::parse($sub->renewal_date)->format('d/m')); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <p class="text-xs text-zinc-400 text-center py-8">Sem subscrições ativas.</p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    
    
    <div class="bg-zinc-950 border border-zinc-800 rounded-3xl p-6 lg:p-8 relative overflow-hidden"
         x-data="{
            loading: false,
            insight: '',
            generated: false,
            async generate() {
                if (this.generated) return;
                this.loading = true;
                const prompt = `Sou um consultor financeiro a analisar o seguinte resumo de patrimônio líquido:\n\n- Valor Líquido: <?php echo e(number_format($netWorth, 2, ',', '.')); ?>€\n- Ativos Totais: <?php echo e(number_format($totalAssets, 2, ',', '.')); ?>€\n- Passivos: <?php echo e(number_format($liabilities, 2, ',', '.')); ?>€\n- Liquidez (bancos): <?php echo e(number_format($totalBankBalance, 2, ',', '.')); ?>€\n- Investimentos: <?php echo e(number_format($investmentsValue, 2, ',', '.')); ?>€\n- Metas de poupança: <?php echo e(number_format($goalsSaved, 2, ',', '.')); ?>€\n- Taxa de poupança (3m): <?php echo e(round($avgSavingsRate, 1)); ?>%\n- Rácio dívida/ativo: <?php echo e(round($debtToAssetRatio, 1)); ?>%\n- Solvabilidade: <?php echo e($solvencyRatio >= 99 ? 'sem dívidas' : number_format($solvencyRatio, 1, ',', '.')); ?>x\n- Score de saúde financeira: <?php echo e(round($healthScore)); ?>/100\n- Exposição a investimentos: <?php echo e(round($investmentExposure, 1)); ?>%\n- Custo anual de subscrições: <?php echo e(number_format($totalAnnualSubscriptions, 0, ',', '.')); ?>€\n\nFaz uma análise concisa e prática em português europeu (máximo 4 parágrafos curtos). Identifica os pontos fortes, os riscos principais e dá 2-3 ações concretas prioritárias. Sê direto, sem rodeios.`;
                try {
                    const res = await fetch('https://api.anthropic.com/v1/messages', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            model: 'claude-sonnet-4-6',
                            max_tokens: 1000,
                            messages: [{ role: 'user', content: prompt }]
                        })
                    });
                    const data = await res.json();
                    this.insight = data.content?.[0]?.text || 'Não foi possível gerar análise.';
                    this.generated = true;
                } catch(e) {
                    this.insight = 'Erro ao gerar análise. Verifica a tua chave de API.';
                } finally {
                    this.loading = false;
                }
            }
         }"
         x-init="$nextTick(() => generate())">

        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-500/5 blur-[80px] rounded-full pointer-events-none"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-8 h-8 rounded-xl bg-brand-500/20 flex items-center justify-center">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'sparkles','class' => 'w-4 h-4 text-brand-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'sparkles','class' => 'w-4 h-4 text-brand-400']); ?>
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
                    <h2 class="text-sm font-black text-white uppercase tracking-tight">Análise AI · Consultor Financeiro</h2>
                    <p class="text-[9px] text-zinc-500">Gerado com base nos teus dados reais</p>
                </div>
            </div>

            <div x-show="loading" class="flex items-center gap-3 py-4">
                <div class="flex gap-1">
                    <span class="w-1.5 h-1.5 bg-brand-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                    <span class="w-1.5 h-1.5 bg-brand-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                    <span class="w-1.5 h-1.5 bg-brand-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                </div>
                <span class="text-xs text-zinc-500">A analisar o teu patrimônio…</span>
            </div>

            <div x-show="!loading && insight" x-html="insight.replace(/\n\n/g,'<br><br>').replace(/\*\*(.*?)\*\*/g,'<strong class=\'text-white\'>$1</strong>')"
                class="text-sm text-zinc-300 leading-relaxed space-y-3 [&_strong]:font-black">
            </div>

            <div x-show="!loading && !insight" class="text-xs text-zinc-500 py-4 italic">
                A iniciar análise…
            </div>

            <button x-show="generated" @click="generated=false; insight=''; generate()"
                class="mt-5 text-[9px] uppercase font-black tracking-widest text-zinc-500 hover:text-brand-400 transition-colors flex items-center gap-1.5">
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'arrow-path','class' => 'w-3 h-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-path','class' => 'w-3 h-3']); ?>
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
<?php endif; ?> Regenerar análise
            </button>
        </div>
    </div>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
function netWorthHub() {
    return {
        init() {}
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/livewire/net-worth-hub.blade.php ENDPATH**/ ?>