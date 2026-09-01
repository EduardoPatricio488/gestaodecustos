<div class="space-y-8 pb-24">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex items-center gap-5">
            <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl shadow-xl">
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'chart-bar','class' => 'w-8 h-8 text-violet-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chart-bar','class' => 'w-8 h-8 text-violet-600']); ?>
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
                <h1 class="text-3xl font-black dark:text-white uppercase tracking-tighter italic">Fluxo de Caixa</h1>
                <p class="text-xs text-zinc-400 mt-1">Previsão de entradas e saídas · <?php echo e($forecastDays); ?> dias</p>
            </div>
        </div>
        <?php if (isset($component)) { $__componentOriginala467913f9ff34913553be64599ec6e92 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala467913f9ff34913553be64599ec6e92 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::select.index','data' => ['wire:model.live' => 'forecastDays','class' => 'w-40']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'forecastDays','class' => 'w-40']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <option value="30">30 dias</option>
            <option value="60">60 dias</option>
            <option value="90">90 dias</option>
            <option value="180">180 dias</option>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala467913f9ff34913553be64599ec6e92)): ?>
<?php $attributes = $__attributesOriginala467913f9ff34913553be64599ec6e92; ?>
<?php unset($__attributesOriginala467913f9ff34913553be64599ec6e92); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala467913f9ff34913553be64599ec6e92)): ?>
<?php $component = $__componentOriginala467913f9ff34913553be64599ec6e92; ?>
<?php unset($__componentOriginala467913f9ff34913553be64599ec6e92); ?>
<?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($forecast['alert']): ?>
        <div class="p-4 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-2xl flex items-center gap-3">
            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'exclamation-triangle','class' => 'size-5 text-red-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'exclamation-triangle','class' => 'size-5 text-red-500']); ?>
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
            <p class="font-bold text-red-700 dark:text-red-300"><?php echo e($forecast['alert']); ?></p>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-zinc-950 text-white p-6 rounded-3xl">
            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">Saldo Atual</p>
            <p class="text-3xl font-black italic tabular-nums"><?php echo e(number_format($forecast['current_balance'], 0, ',', '.')); ?>€</p>
        </div>
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-3xl">
            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">Previsão Final</p>
            <p class="text-3xl font-black italic tabular-nums <?php echo e($forecast['forecast_end'] < 0 ? 'text-red-500' : 'text-emerald-500'); ?>">
                <?php echo e(number_format($forecast['forecast_end'], 0, ',', '.')); ?>€
            </p>
        </div>
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-3xl">
            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">Mínimo Previsto</p>
            <p class="text-3xl font-black italic tabular-nums <?php echo e($forecast['min_balance'] < 0 ? 'text-red-500' : 'text-orange-500'); ?>">
                <?php echo e(number_format($forecast['min_balance'], 0, ',', '.')); ?>€
            </p>
        </div>
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-3xl">
            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-400 mb-2">Máximo Previsto</p>
            <p class="text-3xl font-black italic tabular-nums text-blue-500">
                <?php echo e(number_format($forecast['max_balance'], 0, ',', '.')); ?>€
            </p>
        </div>
    </div>

    
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6"
         x-data="{
            timeline: <?php echo e(json_encode($forecast['timeline'])); ?>,
            init() {
                const ctx = this.$refs.chart.getContext('2d');
                const labels = this.timeline.map(t => t.date);
                const data = this.timeline.map(t => t.balance);
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Saldo Previsto (€)',
                            data,
                            borderColor: '#8b5cf6',
                            backgroundColor: 'rgba(139,92,246,0.1)',
                            fill: true,
                            tension: 0.3,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: false } }
                    }
                });
            }
         }">
        <h3 class="font-black dark:text-white uppercase tracking-widest text-sm mb-4">Evolução Prevista</h3>
        <canvas x-ref="chart" height="80"></canvas>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($forecast['events'])): ?>
        <div>
            <h3 class="text-sm font-black uppercase tracking-widest text-zinc-400 mb-4">Eventos Previstos</h3>
            <div class="space-y-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $forecast['events']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="flex justify-between items-center p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="size-2 rounded-full <?php echo e($event['type'] === 'inflow' ? 'bg-emerald-500' : 'bg-red-500'); ?>"></div>
                            <span class="font-bold text-sm dark:text-white"><?php echo e($event['label']); ?></span>
                        </div>
                        <div class="text-right">
                            <span class="font-black tabular-nums <?php echo e($event['amount'] >= 0 ? 'text-emerald-500' : 'text-red-500'); ?>">
                                <?php echo e($event['amount'] >= 0 ? '+' : ''); ?><?php echo e(number_format($event['amount'], 0, ',', '.')); ?>€
                            </span>
                            <p class="text-[10px] text-zinc-400"><?php echo e(\Carbon\Carbon::parse($event['date'])->format('d/m/Y')); ?></p>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/livewire/business/cash-flow-hub.blade.php ENDPATH**/ ?>