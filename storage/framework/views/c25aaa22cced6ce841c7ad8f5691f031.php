<div class="space-y-10 pb-20"> 

    
    <div class="relative">

        
        <div class="flex justify-center mb-8">
            <div x-data="{ open: false }" class="relative">
                <?php $isCollab = session()->has('impersonator_id'); ?>

                <button @click="open = !open" @click.outside="open = false" type="button"
                    class="relative group cursor-pointer active:scale-95 transition-all outline-none">

                    <div class="absolute -inset-0.5 bg-gradient-to-r <?php echo e($isCollab ? 'from-emerald-500/40 to-teal-500/40' : 'from-brand-500/40 to-indigo-500/40'); ?> rounded-full blur opacity-20 group-hover:opacity-100 transition duration-500"></div>

                    <div class="relative flex items-center gap-3 px-4 py-1.5 rounded-full bg-white/80 dark:bg-zinc-900/80 border border-zinc-200/50 dark:border-zinc-800/50 shadow-sm backdrop-blur-md">

                        
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full <?php echo e($isCollab ? 'bg-emerald-400' : 'bg-brand-400'); ?> opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 <?php echo e($isCollab ? 'bg-emerald-500' : 'bg-brand-500'); ?>"></span>
                        </span>

                        <p class="text-[10px] font-black uppercase tracking-[0.15em] flex items-center gap-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isCollab): ?>
                                <span class="text-zinc-400 dark:text-zinc-500">Sessão:</span>
                                <span class="text-zinc-800 dark:text-white"><?php echo e(auth()->user()->name); ?></span>
                                <span class="text-zinc-300 dark:text-zinc-700">|</span>
                                <span class="text-emerald-600 font-black italic">Acesso Colaborador</span>
                            <?php else: ?>
                                <span class="text-zinc-400 dark:text-zinc-500">Conta Administrativa:</span>
                                <span class="text-zinc-800 dark:text-white"><?php echo e(auth()->user()->name); ?></span>
                                <span class="text-zinc-300 dark:text-zinc-700">|</span>
                                <span class="bg-gradient-to-r from-brand-600 to-indigo-500 bg-clip-text text-transparent italic font-black">CEO Founder</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </p>

                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'chevron-down','variant' => 'micro','class' => 'size-3 text-zinc-400 transition-transform duration-300','xBind:class' => 'open ? \'rotate-180\' : \'\'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-down','variant' => 'micro','class' => 'size-3 text-zinc-400 transition-transform duration-300','x-bind:class' => 'open ? \'rotate-180\' : \'\'']); ?>
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
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" x-cloak x-transition
                    class="absolute left-1/2 -translate-x-1/2 mt-3 w-64 z-[100] bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl overflow-hidden p-2 text-left">
                    <div class="px-3 py-2 border-b border-zinc-100 dark:border-zinc-800 mb-1">
                        <span class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Mudar para conta de:</span>
                    </div>

                    <div class="space-y-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $workspace->employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <button wire:click="switchToEmployee(<?php echo e($employee->id); ?>)" @click="open = false"
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors text-left group">
                                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'user','variant' => 'micro','class' => 'size-4 text-zinc-400 group-hover:text-brand-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'user','variant' => 'micro','class' => 'size-4 text-zinc-400 group-hover:text-brand-500']); ?>
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
                                <div class="flex flex-col leading-tight">
                                    <span class="font-black text-[10px] uppercase tracking-widest text-zinc-800 dark:text-white"><?php echo e($employee->name); ?></span>
                                    <span class="text-[9px] text-zinc-500 font-bold uppercase tracking-tighter"><?php echo e($employee->role ?? 'Colaborador'); ?></span>
                                </div>
                            </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="px-3 py-4 text-center text-[10px] text-zinc-400 uppercase">Sem colaboradores</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="mt-2 pt-2 border-t border-zinc-100 dark:border-zinc-800 text-center">
                        <button wire:click="createTestEmployees" class="text-[9px] font-black uppercase text-brand-600 hover:text-brand-700">
                            + Gerar Contas de Teste
                        </button>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 relative z-10">
            <div class="flex items-center gap-6 text-left">
                <div class="relative group">
                    <img src="<?php echo e($workspace->logo_url); ?>" class="size-24 rounded-[2.5rem] shadow-2xl border-4 border-white dark:border-zinc-800 object-cover bg-white">
                    <div class="absolute -bottom-1 -right-1 size-7 bg-emerald-500 border-4 border-zinc-50 dark:border-zinc-950 rounded-full shadow-lg"></div>
                </div>

                <div>
                    <div class="flex items-center gap-3">

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($businessWorkspaces->count() > 1): ?>
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" @click.outside="open = false" type="button"
                                    class="flex items-center gap-2 group outline-none">
                                    <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none group-hover:text-brand-500 transition-colors">
                                        <?php echo e($workspace->name); ?>

                                    </h1>
                                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'chevron-down','class' => 'size-5 text-zinc-400 transition-transform duration-200 mt-1','xBind:class' => 'open ? \'rotate-180\' : \'\'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-down','class' => 'size-5 text-zinc-400 transition-transform duration-200 mt-1','x-bind:class' => 'open ? \'rotate-180\' : \'\'']); ?>
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

                                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    class="absolute left-0 top-full mt-3 w-72 z-50 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl overflow-hidden p-2">

                                    <div class="px-3 py-2 border-b border-zinc-100 dark:border-zinc-800 mb-1">
                                        <span class="text-[9px] font-black uppercase tracking-widest text-zinc-400">As tuas empresas</span>
                                    </div>

                                    <div class="space-y-1 max-h-64 overflow-y-auto">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $businessWorkspaces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bws): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <button
                                                wire:click="switchBusinessWorkspace(<?php echo e($bws->id); ?>)"
                                                @click="open = false"
                                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-left group/item <?php echo e($bws->id === $workspace->id ? 'bg-brand-50 dark:bg-brand-950/30' : 'hover:bg-zinc-50 dark:hover:bg-zinc-800'); ?>"
                                            >
                                                <img src="<?php echo e($bws->logo_url); ?>" class="size-8 rounded-xl object-cover bg-zinc-100 shrink-0" />
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-black uppercase tracking-tight text-zinc-800 dark:text-white truncate group-hover/item:text-brand-500 transition-colors">
                                                        <?php echo e($bws->name); ?>

                                                    </p>
                                                    <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bws->user_role === 'admin'): ?> CEO / Proprietário
                                                        <?php elseif($bws->user_role === 'editor'): ?> Colaborador
                                                        <?php else: ?> Visitante
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </p>
                                                </div>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bws->id === $workspace->id): ?>
                                                    <div class="size-2 rounded-full bg-brand-500 shadow-[0_0_6px_#10b981] shrink-0"></div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </button>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </div>

                                    <div class="mt-2 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                                        <a href="<?php echo e(route('hub.business.gateway')); ?>" wire:navigate
                                            class="flex items-center gap-2 px-3 py-2 text-[10px] font-black uppercase tracking-widest text-brand-600 hover:text-brand-500 transition-colors">
                                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'plus-circle','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'plus-circle','class' => 'size-3.5']); ?>
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
                                            Entrar noutra empresa
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none"><?php echo e($workspace->name); ?></h1>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if (isset($component)) { $__componentOriginal4cc377eda9b63b796b6668ee7832d023 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4cc377eda9b63b796b6668ee7832d023 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::badge.index','data' => ['variant' => 'neutral','size' => 'sm','class' => 'font-black text-[9px] tracking-[0.2em] border-none bg-zinc-100 dark:bg-zinc-800 text-zinc-500 uppercase']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'neutral','size' => 'sm','class' => 'font-black text-[9px] tracking-[0.2em] border-none bg-zinc-100 dark:bg-zinc-800 text-zinc-500 uppercase']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Empresa Ativa <?php echo $__env->renderComponent(); ?>
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
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-3">
                        <p class="text-xs font-black text-zinc-400 uppercase tracking-widest flex items-center gap-2">
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'identification','class' => 'size-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'identification','class' => 'size-3']); ?>
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
<?php endif; ?> NIF: <span :class="privacyMode ? 'blur-sm select-none' : ''"><?php echo e($workspace->tax_number ?? 'S/ NIF'); ?></span>
                        </p>
                        <span class="text-zinc-300 dark:text-zinc-800">|</span>
                        <p class="text-xs font-black text-brand-600 dark:text-brand-400 uppercase tracking-widest flex items-center gap-2">
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'building-office','class' => 'size-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'building-office','class' => 'size-3']); ?>
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
<?php endif; ?> <?php echo e($workspace->industry ?? 'Gestão de Negócio'); ?>

                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['href' => ''.e(route('export.business')).'','variant' => 'ghost','icon' => 'document-arrow-down','class' => 'rounded-2xl font-bold text-zinc-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('export.business')).'','variant' => 'ghost','icon' => 'document-arrow-down','class' => 'rounded-2xl font-bold text-zinc-500']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Contabilista <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['href' => ''.e(route('hub.business.ai')).'','variant' => 'primary','icon' => 'sparkles','class' => 'bg-brand-600 border-none shadow-lg rounded-2xl font-black uppercase tracking-tighter px-6 text-white']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('hub.business.ai')).'','variant' => 'primary','icon' => 'sparkles','class' => 'bg-brand-600 border-none shadow-lg rounded-2xl font-black uppercase tracking-tighter px-6 text-white']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Estrategista IA <?php echo $__env->renderComponent(); ?>
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




    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        
        <div class="glass-card bg-white dark:bg-zinc-900 p-7 rounded-[2.5rem]
            border border-zinc-200 dark:border-zinc-800 shadow-sm group hover:border-emerald-500/30 transition-all">

            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">
                Faturação Paga
            </p>

            <h3 class="text-3xl font-black text-emerald-600 tracking-tighter">
                <span :class="privacyMode ? 'blur-md select-none' : ''"
                    class="transition-all duration-500 inline-block">
                    <?php echo e(number_format($revenue, 2, ',', ' ')); ?> €
                </span>
            </h3>

            <div class="mt-4 flex items-center gap-2">
                <span class="text-[10px] font-bold text-emerald-500">
                    ▲ <?php echo e(number_format($margin, 1, ',', ' ')); ?>%
                </span>
                <span class="text-[10px] text-zinc-400 font-medium italic">
                    Rentabilidade
                </span>
            </div>
        </div>

        
        <div class="glass-card bg-white dark:bg-zinc-900 p-7 rounded-[2.5rem]
            border border-zinc-200 dark:border-zinc-800 shadow-sm group hover:border-red-500/30 transition-all">

            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">
                Custos Operacionais
            </p>

            <h3 class="text-3xl font-black text-red-500 tracking-tighter">
                <span :class="privacyMode ? 'blur-md select-none' : ''"
                    class="transition-all duration-500 inline-block">
                    <?php echo e(number_format($totalCosts, 2, ',', ' ')); ?> €
                </span>
            </h3>

            <div class="mt-4 flex items-center gap-2">
                <span class="text-[10px] font-bold text-red-500">▼ 4%</span>
                <span class="text-[10px] text-zinc-400 font-medium italic">poupança ativa</span>
            </div>
        </div>

        
        <div class="relative overflow-hidden bg-zinc-950 p-7 rounded-[2.5rem]
            shadow-2xl border border-zinc-800 group">

            <p class="text-[10px] font-black text-brand-400 uppercase tracking-[0.2em] mb-1">
                Resultado Líquido
            </p>

            <h3 class="text-3xl font-black <?php echo e($netProfit >= 0 ? 'text-white' : 'text-red-400'); ?> tracking-tighter">
                <span :class="privacyMode ? 'blur-md select-none' : ''"
                    class="transition-all duration-500 inline-block">
                    <?php echo e(number_format($netProfit, 2, ',', ' ')); ?> €
                </span>
            </h3>

            <div class="mt-4 h-1 w-full bg-white/5 rounded-full overflow-hidden">
                <div class="h-full bg-brand-500 shadow-[0_0_10px_#3b82f6]"
                    style="width: <?php echo e(min(100, max(0, abs($margin)))); ?>%">
                </div>
            </div>
        </div>

        
        <div class="glass-card bg-white dark:bg-zinc-900 p-7 rounded-[2.5rem]
            border border-zinc-200 dark:border-zinc-800 shadow-sm transition-all">

            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">
                Business Runway
            </p>

            <h3 class="text-3xl font-black dark:text-white italic tracking-tighter">
                <span :class="privacyMode ? 'blur-md select-none' : ''"
                    class="transition-all duration-500 inline-block">
                    <?php echo e($runway); ?>

                </span>
            </h3>

            <p class="mt-4 text-[10px] text-zinc-500 font-medium italic">
                Tempo de sobrevivência.
            </p>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        
        <div class="lg:col-span-2 glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem]
            border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">

            <div class="flex justify-between items-center mb-10">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-brand-500/10 rounded-lg text-brand-600">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'presentation-chart-bar','variant' => 'outline','class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'presentation-chart-bar','variant' => 'outline','class' => 'size-5']); ?>
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
                        <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">
                            Pipeline de Execução
                        </h3>
                        <p class="text-lg font-black dark:text-white uppercase italic tracking-tighter">
                            Projetos & Eficiência
                        </p>
                    </div>
                </div>

                <?php if (isset($component)) { $__componentOriginal4cc377eda9b63b796b6668ee7832d023 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4cc377eda9b63b796b6668ee7832d023 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::badge.index','data' => ['variant' => 'neutral','class' => 'font-black text-[9px] uppercase bg-zinc-100 dark:bg-zinc-800 border-none px-3 py-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'neutral','class' => 'font-black text-[9px] uppercase bg-zinc-100 dark:bg-zinc-800 border-none px-3 py-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <span :class="privacyMode ? 'blur-sm select-none' : ''"
                        class="transition-all duration-500">
                        <?php echo e($activeProjects->count()); ?> Ativos
                    </span>
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $activeProjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="p-6 bg-zinc-50/50 dark:bg-zinc-800/40 rounded-[2rem]
                        border border-zinc-100 dark:border-zinc-700/50 hover:border-brand-500/50
                        transition-all duration-300 group/item">

                        <div class="flex justify-between items-center mb-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-black dark:text-white uppercase tracking-tight truncate w-32">
                                    <?php echo e($project->name); ?>

                                </span>

                                <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest mt-0.5">
                                    Rentabilidade
                                </span>
                            </div>

                            <span class="text-xs font-black <?php echo e($project->margin >= 30 ? 'text-emerald-500' : 'text-amber-500'); ?>">
                                <span :class="privacyMode ? 'blur-sm select-none' : ''"
                                    class="transition-all duration-500">
                                    <?php echo e(round($project->margin)); ?>% Margem
                                </span>
                            </span>
                        </div>

                        <div class="h-2 w-full bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden
                            border border-zinc-100 dark:border-zinc-800 shadow-inner">

                            <div class="h-full bg-brand-500 transition-all duration-1000
                                shadow-[0_0_10px_rgba(59,130,246,0.4)]"
                                style="width: <?php echo e(min(100, max(0, $project->margin))); ?>%">
                            </div>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="col-span-2 py-10 text-center">
                        <p class="text-zinc-400 italic text-sm font-medium">
                            Sem projetos ativos.
                        </p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div class="glass-card p-8 bg-zinc-950 dark:bg-zinc-900 border-2 border-dashed border-zinc-800
            dark:border-brand-500/20 rounded-[2.5rem] flex flex-col group relative overflow-hidden">

            <div class="absolute inset-0 bg-gradient-to-b from-brand-500/5 to-transparent pointer-events-none"></div>

            <div class="relative z-10 flex-1 flex flex-col">

                <div class="flex items-center gap-3 mb-8">
                    <div class="size-2 rounded-full bg-brand-500 animate-ping"></div>
                    <h3 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-500">
                        Radar de Riscos IA
                    </h3>
                </div>

                <div class="space-y-6 flex-1">

                    
                    <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl
                        border border-white/5 transition-colors">

                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">
                            Inventário
                        </p>

                        <span :class="privacyMode ? 'blur-sm select-none' : ''"
                            class="text-[10px] font-black px-3 py-1 rounded-lg
                            <?php echo e($lowStockCount > 0 ? 'bg-red-500 text-white' : 'bg-emerald-500/10 text-emerald-500'); ?>

                            transition-all duration-500">

                            <?php echo e($lowStockCount > 0 ? $lowStockCount . ' Alertas' : 'Nível OK'); ?>

                        </span>
                    </div>

                    
                    <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl
                        border border-white/5 transition-colors">

                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">
                            Arquivo Fiscal
                        </p>

                        <span :class="privacyMode ? 'blur-sm select-none' : ''"
                            class="text-[10px] font-black px-3 py-1 rounded-lg
                            <?php echo e($criticalDocsCount > 0 ? 'bg-amber-500 text-white' : 'bg-emerald-500/10 text-emerald-500'); ?>

                            transition-all duration-500">

                            <?php echo e($criticalDocsCount > 0 ? $criticalDocsCount . ' Críticos' : 'Tudo em dia'); ?>

                        </span>
                    </div>

                    
                    <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl
                        border border-white/5 transition-colors">

                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">
                            Tarefas Equipa
                        </p>

                        <span :class="privacyMode ? 'blur-sm select-none' : ''"
                            class="text-[10px] font-black px-3 py-1 rounded-lg
                            <?php echo e($overdueTasksCount > 0 ? 'bg-red-500 text-white shadow-[0_0_10px_rgba(239,68,68,0.3)]' : 'bg-emerald-500/10 text-emerald-500'); ?>

                            transition-all duration-500">

                            <?php echo e($overdueTasksCount > 0 ? $overdueTasksCount . ' Atrasadas' : 'OK'); ?>

                        </span>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-white/5">
                    <p class="text-[9px] text-zinc-500 font-bold uppercase leading-relaxed italic">
                        <span class="text-brand-400 font-black tracking-widest mr-1">Análise IA:</span>
                        Foram detetados
                        <span :class="privacyMode ? 'blur-sm select-none' : ''"
                            class="transition-all duration-500">
                            <?php echo e($lowStockCount + $criticalDocsCount + $overdueTasksCount); ?>

                        </span>
                        pontos de atenção operacional.
                    </p>
                </div>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        
        <div class="glass-card p-8 bg-amber-50/50 dark:bg-amber-900/10 rounded-[2.5rem]
            border border-amber-200 dark:border-amber-800/50 relative overflow-hidden group">

            <div class="relative z-10">

                <div class="flex items-center gap-3 mb-8">
                    <div class="p-2 bg-amber-500 rounded-xl shadow-lg text-white">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'receipt-percent','variant' => 'mini','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'receipt-percent','variant' => 'mini','class' => 'size-4']); ?>
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

                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-amber-700 dark:text-amber-500">
                        Provisão Fiscal Estimada
                    </h3>
                </div>

                <div class="space-y-4">

                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-amber-900/60 dark:text-zinc-400">
                            IVA (Saldo do Mês)
                        </span>

                        <span :class="privacyMode ? 'blur-sm select-none' : ''"
                            class="text-sm font-black text-amber-700 dark:text-zinc-200 transition-all duration-500">
                            <?php echo e(number_format($vatProvision, 2, ',', ' ')); ?> €
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-amber-900/60 dark:text-zinc-400">
                            IRC (Estimativa)
                        </span>

                                               <span :class="privacyMode ? 'blur-sm select-none' : ''"
                            class="text-sm font-black text-amber-700 dark:text-zinc-200 transition-all duration-500">
                            <?php echo e(number_format($ircProvision, 2, ',', ' ')); ?> €
                        </span>
                    </div>

                    <div class="pt-6 mt-2 border-t border-amber-200/50 dark:border-zinc-800 flex justify-between items-end">
                        <div>
                            <p class="text-[9px] font-black uppercase text-amber-600 dark:text-amber-500 tracking-widest">
                                Reserva Total
                            </p>
                        </div>

                        <p class="text-3xl font-black text-amber-600 tracking-tighter">
                            <span :class="privacyMode ? 'blur-md select-none' : ''"
                                class="transition-all duration-500 inline-block">
                                <?php echo e(number_format($vatProvision + $ircProvision, 2, ',', ' ')); ?> €
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem]
            border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col justify-between
            group hover:border-emerald-500/30 transition-all">

            <div>
                <div class="flex items-center gap-3 mb-8">
                    <div class="p-2 bg-emerald-500 rounded-xl shadow-lg text-white">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'arrow-up-right','variant' => 'mini','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-up-right','variant' => 'mini','class' => 'size-4']); ?>
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

                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">
                        Receitas em Trânsito
                    </h3>
                </div>

                <p class="text-4xl font-black text-emerald-600 tracking-tighter italic">
                    <span :class="privacyMode ? 'blur-md select-none' : ''"
                        class="transition-all duration-500 inline-block">
                        <?php echo e(number_format($accountsReceivable, 2, ',', ' ')); ?> €
                    </span>
                </p>
            </div>

            <p class="mt-6 text-[10px] text-zinc-500 font-bold uppercase tracking-widest">
                Faturação emitida pendente
            </p>
        </div>

        
        <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem]
            border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col justify-between
            group hover:border-brand-500/30 transition-all">

            <div class="flex justify-between items-start">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-brand-600 rounded-xl shadow-lg text-white">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'users','variant' => 'mini','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'users','variant' => 'mini','class' => 'size-4']); ?>
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

                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400">
                        Payroll Mensal
                    </h3>
                </div>

                <?php if (isset($component)) { $__componentOriginal4cc377eda9b63b796b6668ee7832d023 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4cc377eda9b63b796b6668ee7832d023 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::badge.index','data' => ['variant' => 'neutral','size' => 'sm','class' => 'font-black text-[9px] tracking-widest bg-zinc-100 dark:bg-zinc-800 border-none px-3 py-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'neutral','size' => 'sm','class' => 'font-black text-[9px] tracking-widest bg-zinc-100 dark:bg-zinc-800 border-none px-3 py-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <span :class="privacyMode ? 'blur-sm select-none' : ''"
                        class="transition-all duration-500">
                        <?php echo e($teamCount); ?> PESSOAS
                    </span>
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

            <div class="mt-8">
                <p class="text-3xl font-black dark:text-white tracking-tighter">
                    <span :class="privacyMode ? 'blur-md select-none' : ''"
                        class="transition-all duration-500 inline-block">
                        <?php echo e(number_format($payroll, 2, ',', ' ')); ?> €
                    </span>
                </p>

                <div class="mt-4 p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl
                    border border-zinc-100 dark:border-zinc-700">
                    <p class="text-[9px] font-black uppercase text-zinc-500">
                        Custo RH Mensal Fixo
                    </p>
                </div>
            </div>
        </div>
    </div>

    
    <?php
        $openTickets = \App\Models\SupportTicket::where('status', 'open')
            ->where('workspace_id', $workspace->id)
            ->count();
    ?>

    <div class="pt-10 border-t border-zinc-100 dark:border-zinc-800 mt-10
        flex flex-col md:flex-row justify-between items-center gap-4">

        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © <?php echo e(date('Y')); ?> <?php echo e($workspace->name); ?> · Sistema de Gestão Enterprise
        </p>

        <div class="flex items-center gap-6">

            
            <?php if (isset($component)) { $__componentOriginalfe86969babb72517ecf97426e7c9330d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe86969babb72517ecf97426e7c9330d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::sidebar.item','data' => ['icon' => 'chat-bubble-left-right','href' => route('hub.business.support'),'current' => request()->routeIs('hub.business.support'),'wire:navigate' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::sidebar.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'chat-bubble-left-right','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('hub.business.support')),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('hub.business.support')),'wire:navigate' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase tracking-widest">
                        Suporte Técnico
                    </span>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openTickets > 0): ?>
                        <span class="inline-flex items-center justify-center min-w-[22px] h-5 px-1.5
                            rounded-full bg-red-500 text-white text-[10px] font-black
                            shadow-lg shadow-red-500/30">
                            <?php echo e($openTickets); ?>

                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $attributes = $__attributesOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__attributesOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe86969babb72517ecf97426e7c9330d)): ?>
<?php $component = $__componentOriginalfe86969babb72517ecf97426e7c9330d; ?>
<?php unset($__componentOriginalfe86969babb72517ecf97426e7c9330d); ?>
<?php endif; ?>

            
            <a href="#"
                class="text-[9px] font-black text-zinc-400 hover:text-brand-500 uppercase tracking-widest transition-colors">
                Segurança
            </a>
        </div>
    </div>

</div>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/livewire/business/business-dashboard.blade.php ENDPATH**/ ?>