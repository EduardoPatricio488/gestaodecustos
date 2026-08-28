<div class="relative p-8 md:p-12">
    
   <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!auth()->user()->isAdmin()): ?>
   <div class="absolute -right-20 -top-20 size-80 blur-[120px] rounded-full opacity-20 transition-all duration-1000 pointer-events-none"
         style="background-color: <?php echo e($profile_color); ?>"></div>

    <div class="relative z-10 flex flex-col md:flex-row items-center gap-12">

        
        <div class="flex flex-col items-center gap-6 shrink-0">
            <div class="size-48 rounded-[3rem] flex items-center justify-center text-8xl shadow-2xl transition-all duration-700 animate-in zoom-in duration-500"
                 style="background-color: <?php echo e($profile_color); ?>15; border: 4px solid <?php echo e($profile_color); ?>40; box-shadow: 0 20px 50px -12px <?php echo e($profile_color); ?>30;">
                <?php echo e($profile_emoji); ?>

            </div>
            <div class="text-center space-y-1">
                <p class="text-[10px] font-black uppercase text-zinc-500 tracking-[0.3em]">Identidade Ativa</p>
                <p class="text-xs font-bold text-zinc-400 italic">Visualizado no Dashboard</p>
            </div>
        </div>

        
        <div class="flex-1 w-full space-y-10">

            

    
    <div class="space-y-4">
        <div class="flex items-center gap-2 mb-4">
            <span class="text-indigo-500 font-bold text-xs">01.</span>
            <p class="text-[10px] font-black uppercase text-zinc-500 tracking-[0.2em]">Escolhe o teu Mood</p>
        </div>
        <div class="grid grid-cols-5 sm:grid-cols-6 lg:grid-cols-10 gap-3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['😀', '😎', '🤓', '🧑‍💻', '🤑', '🚀', '💎', '📈', '🧘‍♂️', '🦁', '🔥', '✨', '⚡', '🏆', '🎮', '🎧', '🍕', '🌍', '❤️', '👑']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <button
                    type="button"
                    wire:click="updateIdentity('emoji', '<?php echo e($e); ?>')"
                    class="size-11 rounded-xl bg-zinc-50 dark:bg-zinc-800 flex items-center justify-center text-xl hover:scale-110 active:scale-95 transition-all border-2 <?php echo e($profile_emoji === $e ? 'border-brand-500 bg-white dark:bg-zinc-700 shadow-lg scale-110' : 'border-transparent opacity-60'); ?>"
                >
                    <?php echo e($e); ?>

                </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        
        <div class="flex items-center gap-3 mt-4">
            <input
                type="text"
                wire:model="custom_emoji"
                wire:keydown.enter="applyCustomEmoji"
                placeholder="Cola ou digita qualquer emoji..."
                maxlength="8"
                class="flex-1 text-2xl text-center rounded-xl border-2 border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-4 py-2 focus:border-brand-500 focus:outline-none transition-all placeholder:text-sm placeholder:text-zinc-400 dark:text-white"
            />
            <button
                type="button"
                wire:click="applyCustomEmoji"
                class="shrink-0 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 active:scale-95 text-white text-xs font-black uppercase tracking-widest transition-all shadow-md"
            >
                Aplicar
            </button>
        </div>
    </div>


            
            <div class="space-y-4">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-indigo-500 font-bold text-xs">02.</span>
                    <p class="text-[10px] font-black uppercase text-zinc-500 tracking-[0.2em]">Cor da tua Marca</p>
                </div>
                <div class="flex flex-wrap gap-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['#10b981', '#6366f1', '#f59e0b', '#ef4444', '#0ea5e9', '#ec4899', '#a855f7', '#71717a', '#fb7185', '#2dd4bf']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <button
                            type="button"
                            wire:click="updateIdentity('color', '<?php echo e($c); ?>')"
                            class="size-10 rounded-full border-4 border-white dark:border-zinc-800 shadow-xl transition-all hover:scale-125 <?php echo e($profile_color === $c ? 'ring-4 ring-brand-500/40 scale-125 z-10' : 'opacity-80'); ?>"
                            style="background-color: <?php echo e($c); ?>;"
                        ></button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>

            
            <div wire:loading wire:target="updateIdentity" class="absolute bottom-4 right-8 flex items-center gap-2 text-brand-500">
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'arrow-path','class' => 'size-3 animate-spin']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-path','class' => 'size-3 animate-spin']); ?>
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
                <span class="text-[9px] font-black uppercase tracking-widest">Sincronizando...</span>
            </div>
        </div>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/livewire/profile/update-visual-identity-form.blade.php ENDPATH**/ ?>