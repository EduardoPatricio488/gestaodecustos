<div class="max-w-7xl mx-auto space-y-10 pb-20" x-data="{ modal: <?php if ((object) ('openModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('openModal'->value()); ?>')<?php echo e('openModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('openModal'); ?>')<?php endif; ?> }">

    
    <?php if (! $__env->hasRenderedOnce('b223a3ef-9caa-4953-80bb-f88c72372833')): $__env->markAsRenderedOnce('b223a3ef-9caa-4953-80bb-f88c72372833'); ?>
    <style>
        .reminder-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(16, 185, 129, 0.15);
            border-radius: 2.5rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .reminder-card:hover { transform: translateY(-8px); box-shadow: 0 25px 50px -12px rgba(16, 185, 129, 0.15); }

        .text-black-nlt { color: #0f172a !important; }
        .bg-emerald-soft { background: rgba(16, 185, 129, 0.05); }

        .category-badge { font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; padding: 4px 12px; border-radius: 10px; }

        .priority-indicator { width: 6px; height: 40px; border-radius: 10px; }

        [x-cloak] { display: none !important; }
    </style>
    <?php endif; ?>

    <div class="flex flex-col xl:flex-row gap-6 items-center justify-between bg-white dark:bg-zinc-900/50 p-6 lg:p-8 rounded-[2.5rem] border border-zinc-100 dark:border-zinc-800 shadow-sm w-full">

        
        <div class="flex flex-col md:flex-row items-center gap-4 w-full xl:flex-1">
            <div class="relative w-full md:max-w-xs">
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'magnifying-glass','class' => 'absolute left-4 top-1/2 -translate-y-1/2 size-4 text-zinc-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'magnifying-glass','class' => 'absolute left-4 top-1/2 -translate-y-1/2 size-4 text-zinc-400']); ?>
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
                <input wire:model.live.debounce.400ms="search" placeholder="Filtrar..."
                    class="w-full pl-11 pr-6 py-4 bg-zinc-50 dark:bg-zinc-950 rounded-2xl border-none shadow-inner text-sm font-bold text-zinc-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20">
            </div>

            <nav class="flex p-1 bg-zinc-100 dark:bg-zinc-800 rounded-2xl w-full md:w-auto shrink-0">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['pending' => 'Pendentes', 'history' => 'Histórico', 'all' => 'Todos']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <button wire:click="$set('activeTab', '<?php echo e($tab); ?>')"
                        class="flex-1 md:flex-none px-4 py-2.5 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all
                        <?php echo e($activeTab === $tab ? 'bg-white dark:bg-zinc-900 text-emerald-600 shadow-sm' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white'); ?>">
                        <?php echo e($label); ?>

                    </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </nav>
        </div>

        
        <div class="flex flex-col md:flex-row items-center gap-4 w-full xl:w-auto">
            <select wire:model.live="filterPriority" class="w-full md:w-48 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-5 py-4 text-[10px] font-black uppercase tracking-widest outline-none cursor-pointer">
                <option value="all">Prioridades</option>
                <option value="high">🔴 Alta</option>
                <option value="medium">🟡 Média</option>
                <option value="low">🟢 Baixa</option>
            </select>

            <button @click="modal = true; $wire.openReminderModal()"
                class="w-full md:w-auto bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-4 rounded-2xl font-black uppercase tracking-widest text-[11px] shadow-xl shadow-emerald-500/30 transition-all active:scale-95 whitespace-nowrap flex items-center justify-center gap-2">
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'plus','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'plus','class' => 'size-4']); ?>
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
                Novo Lembrete
            </button>
        </div>
    </div>
    
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 pt-6">

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
        ['Ativos', $this->stats['total_active'], 'emerald', 'clock'],
        ['Urgentes', $this->stats['high_priority'], 'red', 'fire'],
        ['Concluídos Hoje', $this->stats['completed_today'], 'blue', 'check-badge'],
        ['Em Atraso', $this->stats['overdue'], 'amber', 'exclamation-circle']
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $value, $color, $icon]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>

        <div class="relative overflow-hidden p-8 rounded-[2.5rem]
                    bg-white/10 dark:bg-zinc-900/30
                    backdrop-blur-lg
                    border border-white/10 dark:border-white/5
                    shadow-[0_6px_28px_rgba(0,0,0,0.35)]
                    flex flex-col justify-between group transition-all duration-200 hover:scale-[1.02]">

            
            <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full
                        bg-<?php echo e($color); ?>-500/20 blur-2xl opacity-40 group-hover:opacity-60 transition-all"></div>

            
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:opacity-20 transition-all">
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => $icon,'class' => 'size-24 text-'.e($color).'-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($icon),'class' => 'size-24 text-'.e($color).'-500']); ?>
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

            
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-300">
                <?php echo e($label); ?>

            </p>

            
            <h3 class="text-4xl font-black mt-2 tracking-tighter text-<?php echo e($color); ?>-400 drop-shadow-sm">
                <?php echo e($value); ?>

            </h3>

        </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

</div>




    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->reminders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reminder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'rem-'.e($reminder->id).''; ?>wire:key="rem-<?php echo e($reminder->id); ?>" class="reminder-card p-8 flex flex-col justify-between group relative overflow-hidden">
                <div class="flex justify-between items-start mb-6">
                    <div class="flex items-center gap-4">
                        <button wire:click="toggleComplete(<?php echo e($reminder->id); ?>)"
                            class="size-9 rounded-2xl border-2 flex items-center justify-center transition-all <?php echo e($reminder->is_completed ? 'bg-emerald-500 border-emerald-500 text-white shadow-lg shadow-emerald-500/40' : 'border-zinc-200 hover:border-emerald-500 bg-white'); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($reminder->is_completed): ?> <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'check','variant' => 'mini','class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check','variant' => 'mini','class' => 'size-5']); ?>
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
<?php endif; ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </button>
                        <span class="category-badge bg-emerald-100 text-emerald-700"><?php echo e($reminder->category); ?></span>
                    </div>

                    <div class="priority-indicator <?php echo e('priority-'.$reminder->priority); ?>"></div>
                </div>

                <div class="space-y-4">
                    <h4 class="text-xl font-black text-black-nlt tracking-tight leading-tight <?php echo e($reminder->is_completed ? 'line-through opacity-30' : ''); ?>">
                        <?php echo e($reminder->title); ?>

                    </h4>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($reminder->notes): ?>
                        <p class="text-sm text-zinc-500 font-medium line-clamp-2 italic">"<?php echo e($reminder->notes); ?>"</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="mt-8 pt-6 border-t border-zinc-100 flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Data Limite</span>
                        <span class="text-xs font-bold text-zinc-900 mt-1 flex items-center gap-2">
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'calendar','variant' => 'micro','class' => 'size-4 text-emerald-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'calendar','variant' => 'micro','class' => 'size-4 text-emerald-600']); ?>
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
                            <?php echo e($reminder->remind_at->format('d M, Y · H:i')); ?>

                        </span>
                    </div>

                    <div class="flex items-center gap-1">
                        <button wire:click="openReminderModal(<?php echo e($reminder->id); ?>)" class="p-2.5 rounded-xl hover:bg-emerald-50 text-zinc-400 hover:text-emerald-600 transition-colors">
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'pencil-square','class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'pencil-square','class' => 'size-5']); ?>
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
                        <button wire:click="deleteReminder(<?php echo e($reminder->id); ?>)" class="p-2.5 rounded-xl hover:bg-red-50 text-zinc-400 hover:text-red-500 transition-colors">
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'trash','class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'trash','class' => 'size-5']); ?>
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
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="col-span-full py-40 text-center bg-white/50 rounded-[4rem] border-2 border-dashed border-zinc-200">
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'archive-box','class' => 'size-20 mx-auto mb-6 text-zinc-200']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'archive-box','class' => 'size-20 mx-auto mb-6 text-zinc-200']); ?>
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
                <h3 class="text-xl font-black text-zinc-400 uppercase tracking-widest">Nada para Processar</h3>
                <p class="text-zinc-500 mt-2 font-medium italic">A tua agenda está totalmente limpa.</p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>














<div x-show="modal" x-cloak
     class="fixed inset-0 z-[300] flex items-center justify-center p-3 sm:p-6
            bg-zinc-950/50 transition-opacity duration-75">

    <div @click.away="modal = false"
         x-show="modal"

         
         x-transition:enter="transition duration-80 ease-out"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"

         
         x-transition:leave="transition duration-60 ease-in"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"

         class="relative w-full max-w-2xl rounded-[2.5rem] overflow-hidden
                bg-white/10 dark:bg-zinc-900/30
                backdrop-blur-lg
                border border-white/10 dark:border-white/5
                shadow-[0_6px_28px_rgba(0,0,0,0.45)]
                max-h-[90vh] flex flex-col">

        
        <div class="shrink-0 px-8 py-6 border-b border-white/10 bg-white/5 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black text-white uppercase italic tracking-tighter">
                    Configurar Lembrete
                </h2>
                <p class="text-[9px] text-emerald-300 font-black uppercase mt-2 tracking-widest">
                    Agendamento Inteligente Finance Connect
                </p>
            </div>

            <button @click="modal = false"
                    class="p-3 bg-white/10 rounded-2xl shadow-sm text-white hover:text-emerald-300 transition-all active:scale-95">
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'x-mark','class' => 'size-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'x-mark','class' => 'size-6']); ?>
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

        
        <form wire:submit.prevent="saveReminder" class="flex flex-col min-h-0 flex-1">

            
            <div class="min-h-0 flex-1 overflow-y-auto custom-scrollbar p-6 space-y-6">

                
                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white/5 text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                        Título do Evento
                    </label>
                    <input type="text" wire:model="title"
                           placeholder="Ex: Liquidação de Dividendos"
                           class="w-full bg-zinc-200 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700
                                  rounded-2xl py-4 px-5 text-sm font-bold text-black dark:text-white
                                  placeholder-zinc-500 outline-none transition-all focus:ring-2 focus:ring-emerald-500/40">
                </div>

                
                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white/5 text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                        Data e Hora
                    </label>
                    <input type="datetime-local" wire:model="remind_at"
                           class="w-full bg-zinc-200 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700
                                  rounded-2xl py-4 px-5 text-sm font-bold text-black dark:text-white
                                  outline-none transition-all focus:ring-2 focus:ring-emerald-500/40">
                </div>

                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white/5 text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                            Prioridade
                        </label>
                        <select wire:model="priority"
                                class="w-full bg-zinc-200 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700
                                       rounded-2xl py-4 px-5 text-sm font-bold text-black dark:text-white outline-none">
                            <option class="text-black" value="low">🟢 Baixa</option>
                            <option class="text-black" value="medium">🟡 Média</option>
                            <option class="text-black" value="high">🔴 Alta</option>
                        </select>
                    </div>

                    
                    <div class="relative">
                        <label class="absolute left-4 -top-2.5 px-2 bg-white/5 text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                            Frequência
                        </label>
                        <select wire:model="frequency"
                                class="w-full bg-zinc-200 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700
                                       rounded-2xl py-4 px-5 text-sm font-bold text-black dark:text-white outline-none">
                            <option class="text-black" value="once">📌 Única</option>
                            <option class="text-black" value="daily">📅 Diária</option>
                            <option class="text-black" value="weekly">🔁 Semanal</option>
                            <option class="text-black" value="monthly">📆 Mensal</option>
                        </select>
                    </div>
                </div>

                
                <div class="space-y-3">
                    <p class="text-[10px] font-black uppercase tracking-widest text-emerald-300 px-1">
                        Categoria
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['finance' => 'Finanças', 'personal' => 'Pessoal', 'work' => 'Trabalho', 'health' => 'Saúde']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <button type="button"
                                    wire:click="$set('category', '<?php echo e($val); ?>')"
                                    class="py-3 rounded-2xl border-2 text-[9px] font-black uppercase tracking-widest transition-all
                                    <?php echo e($category === $val
                                        ? 'bg-emerald-600 text-white border-emerald-600'
                                        : 'bg-zinc-200 dark:bg-zinc-800 border-zinc-300 dark:border-zinc-700 text-black dark:text-white hover:border-emerald-500'); ?>">
                                <?php echo e($l); ?>

                            </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>

                
                <div class="relative">
                    <label class="absolute left-4 -top-2.5 px-2 bg-white/5 text-[10px] font-black uppercase tracking-widest text-emerald-300 z-10">
                        Notas (opcional)
                    </label>
                    <textarea wire:model="notes" rows="3"
                              placeholder="Detalhes estratégicos..."
                              class="w-full bg-zinc-200 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700
                                     rounded-2xl py-4 px-5 text-sm font-medium text-black dark:text-white
                                     outline-none placeholder-zinc-500"></textarea>
                </div>

            </div>

            
            <div class="shrink-0 px-8 py-5 flex flex-col sm:flex-row gap-3 border-t border-white/10 bg-white/5">

                <button type="button"
                        @click="modal = false"
                        class="w-full h-14 rounded-2xl text-white/70 hover:text-white
                               hover:bg-white/10 font-bold uppercase text-xs tracking-widest
                               transition-all active:scale-95">
                    Cancelar
                </button>

                <button type="submit"
                        wire:loading.attr="disabled"
                        wire:target="saveReminder"
                        class="w-full h-14 rounded-2xl bg-emerald-600 hover:bg-emerald-500
                               text-white font-black uppercase text-xs tracking-widest shadow-xl
                               shadow-emerald-500/30 transition-all active:scale-95 disabled:opacity-60
                               flex items-center justify-center gap-3">
                    <span wire:loading.remove wire:target="saveReminder">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'check-circle','class' => 'size-5 inline-block mr-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check-circle','class' => 'size-5 inline-block mr-2']); ?>
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
                        <?php echo e($editingReminderId ? 'Guardar Modificações' : 'Finalizar Agendamento 🟢'); ?>

                    </span>
                    <span wire:loading wire:target="saveReminder">A guardar...</span>
                </button>

            </div>

        </form>
    </div>
</div>














</div>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/livewire/hub/reminders.blade.php ENDPATH**/ ?>