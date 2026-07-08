<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 p-6 md:p-12 text-left">
    <div class="max-w-[1400px] mx-auto space-y-10">

        
        <div class="bg-white dark:bg-zinc-900 p-10 rounded-[3rem] border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col md:flex-row justify-between items-center gap-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-zinc-900/5 blur-[100px] rounded-full -mr-32 -mt-32"></div>

            <div class="flex items-center gap-8 relative z-10 text-left">
                <div class="size-20 rounded-[1.8rem] bg-zinc-900 flex items-center justify-center text-white shadow-2xl">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'building-library','class' => 'size-10']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'building-library','class' => 'size-10']); ?>
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
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 bg-zinc-900 text-white text-[10px] font-black uppercase tracking-widest rounded-lg">Acesso Auditado</span>
                        <h2 class="text-sm font-black text-zinc-400 uppercase tracking-widest">Protocolo de Transparência Financeira</h2>
                    </div>
                    <h1 class="text-4xl font-black dark:text-white tracking-tighter italic leading-none">Dossiê de Solvência: <?php echo e($workspace->name); ?></h1>
                </div>
            </div>

            <a href="<?php echo e(route('bank.portal')); ?>" class="px-8 py-4 bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all shadow-xl">
                Encerrar Auditoria
            </a>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            
            <div class="bg-zinc-950 p-10 rounded-[3rem] shadow-2xl border border-zinc-800 text-left">
                <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.4em] mb-4">Financial Rating</p>
                <div class="flex items-baseline gap-4">
                    <h3 class="text-7xl font-black text-emerald-500 italic tracking-tighter"><?php echo e($rating); ?></h3>
                    <span class="text-zinc-500 font-bold uppercase text-xs">Score Consolidado</span>
                </div>
                <p class="mt-6 text-xs text-zinc-400 leading-relaxed font-medium">Rating gerado automaticamente com base no rácio de liquidez imediata e histórico de passivo circulante.</p>
            </div>

            
            <div class="bg-white dark:bg-zinc-900 p-10 rounded-[3rem] border border-zinc-200 dark:border-zinc-800 shadow-sm text-left">
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.4em] mb-4">Liquidez Disponível</p>
                <h3 class="text-5xl font-black dark:text-white tracking-tighter italic"><?php echo e(number_format($liquidez, 2, ',', ' ')); ?>€</h3>
                <div class="mt-8 flex items-center gap-3">
                    <div class="h-2 flex-1 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500" style="width: 85%"></div>
                    </div>
                    <span class="text-[10px] font-black text-emerald-500">85% OPTIMAL</span>
                </div>
            </div>

            
            <div class="bg-white dark:bg-zinc-900 p-10 rounded-[3rem] border border-zinc-200 dark:border-zinc-800 shadow-sm text-left">
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.4em] mb-4">Passivo Circulante</p>
                <h3 class="text-5xl font-black text-red-500 tracking-tighter italic"><?php echo e(number_format($passivo, 2, ',', ' ')); ?>€</h3>
                <p class="mt-8 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Utilização de Linhas de Crédito: <span class="text-red-500">LOW RISK</span></p>
            </div>
        </div>

        
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[3.5rem] shadow-sm overflow-hidden text-left">
            <div class="p-10 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center bg-zinc-50/50 dark:bg-zinc-950/50">
                <div>
                    <h3 class="text-xs font-black uppercase text-zinc-400 tracking-[0.4em] mb-1">Mapa de Disponibilidades</h3>
                    <p class="text-xl font-black dark:text-white uppercase italic tracking-tighter">Contas Bancárias & Ativos</p>
                </div>
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Sincronização em Tempo Real via API</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-zinc-50/30 dark:bg-zinc-950/30 text-[9px] font-black uppercase text-zinc-400 tracking-widest border-b border-zinc-100 dark:border-zinc-800">
                            <th class="p-8">Instituição Bancária</th>
                            <th class="p-8">Tipo de Conta</th>
                            <th class="p-8">IBAN (Auditado)</th>
                            <th class="p-8 text-right">Saldo Atual</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="p-8 flex items-center gap-4">
                                    <div class="size-10 rounded-xl flex items-center justify-center text-white font-black" style="background-color: <?php echo e($acc->color); ?>">
                                        <?php echo e(substr($acc->bank_name, 0, 1)); ?>

                                    </div>
                                    <span class="text-sm font-black dark:text-white uppercase"><?php echo e($acc->bank_name); ?></span>
                                </td>
                                <td class="p-8">
                                    <span class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-[9px] font-black uppercase text-zinc-500 border border-zinc-200 dark:border-zinc-700"><?php echo e($acc->type); ?></span>
                                </td>
                                <td class="p-8 font-mono text-xs text-zinc-500 dark:text-zinc-400"><?php echo e($acc->iban); ?></td>
                                <td class="p-8 text-right font-black text-lg dark:text-white"><?php echo e(number_format($acc->current_balance, 2, ',', ' ')); ?>€</td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="pt-10 flex flex-col md:flex-row justify-between items-center gap-6 opacity-40">
            <p class="text-[9px] font-black text-zinc-500 uppercase tracking-[0.4em]">© <?php echo e(date('Y')); ?> Relatório Gerado por Finance Pro AI Auditor Engine</p>
            <div class="flex items-center gap-6">
                <span class="text-[9px] font-black uppercase tracking-widest">ID Auditoria: #<?php echo e(rand(100000, 999999)); ?></span>
                <div class="h-3 w-px bg-zinc-400"></div>
                <span class="text-[9px] font-black uppercase tracking-widest">Certificação SSL 256-bit</span>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/livewire/public/bank-dashboard.blade.php ENDPATH**/ ?>