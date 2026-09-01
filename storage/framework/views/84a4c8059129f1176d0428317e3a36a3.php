<?php
    $isCurrent = $currentPlan === $plan->slug;
    $isBusiness = ($variant ?? '') === 'business' || $plan->hasFeature('business_mode');
    $isExtra = ($variant ?? '') === 'extra';

    // 🔥 LÓGICA DE COR DINÂMICA (Ponto crucial para a tua personalização)
    // 1. Usa a cor da BD | 2. Se for Business usa Roxo | 3. Fallback: Verde esmeralda
    $brandColor = $plan->color ?? ($isBusiness ? '#8b5cf6' : '#10b981');
?>

<div class="glass-card p-8 rounded-[2.5rem] flex flex-col relative transition-all duration-500 hover:scale-[1.02]
    <?php echo e($isExtra ? 'bg-white dark:bg-zinc-900 border-2' : ($isBusiness ? 'bg-zinc-950 text-white border border-zinc-800 shadow-2xl scale-105 z-10' : 'bg-white dark:bg-zinc-900 border-2 border-zinc-100 dark:border-zinc-800 shadow-lg')); ?>

    <?php echo e($isCurrent ? 'ring-4' : ''); ?>"
    style="<?php echo e($isCurrent ? 'ring-color: ' . $brandColor . '33;' : ''); ?> <?php echo e($isExtra ? 'border-color: ' . $brandColor . ';' : ''); ?>">

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isExtra): ?>
        <div class="absolute -top-4 left-1/2 -translate-x-1/2 text-white px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-xl"
             style="background-color: <?php echo e($brandColor); ?>;">
             Extra
        </div>
    <?php elseif($isBusiness): ?>
        <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-violet-600 text-white px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-xl">
            Empresa
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="mb-8">
        
        <span class="px-3 py-1 rounded-full text-white text-[10px] font-black uppercase shadow-sm"
              style="background-color: <?php echo e($brandColor); ?>;">
            <?php echo e($plan->name); ?>

        </span>

        
        <div class="mt-4 flex items-baseline gap-1">
            <span class="text-5xl font-black <?php echo e(($isBusiness && !$isExtra) ? 'text-white' : 'dark:text-white text-zinc-900'); ?>">
                <?php echo e(number_format($plan->price, 0, ',', ' ')); ?>€
            </span>
            <span class="<?php echo e(($isBusiness && !$isExtra) ? 'text-zinc-400' : 'text-zinc-500'); ?> font-bold">/mês</span>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->description): ?>
            <p class="mt-4 text-sm font-medium italic leading-relaxed <?php echo e(($isBusiness && !$isExtra) ? 'text-zinc-400' : 'text-zinc-500'); ?>">
                <?php echo e($plan->description); ?>

            </p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <ul class="space-y-4 mb-10 flex-1 text-left">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $plan->featureKeys(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <li class="flex items-center gap-3 text-sm font-bold <?php echo e(($isBusiness && !$isExtra) ? 'text-zinc-200' : 'text-zinc-700 dark:text-zinc-300'); ?>">
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'check-circle','variant' => 'solid','class' => 'w-5 h-5 shrink-0','style' => 'color: '.e($brandColor).';']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check-circle','variant' => 'solid','class' => 'w-5 h-5 shrink-0','style' => 'color: '.e($brandColor).';']); ?>
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
                <?php echo e(\App\Models\SubscriptionPlan::FEATURE_LABELS[$feat] ?? str_replace('_', ' ', ucfirst($feat))); ?>

            </li>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <li class="text-sm text-zinc-400 italic text-center">Sem funcionalidades listadas.</li>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </ul>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isCurrent && $plan->hasFeature('business_mode')): ?>
        <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['href' => ''.e(route('hub.business.gateway')).'','variant' => 'primary','class' => 'w-full !h-14 font-black uppercase tracking-widest shadow-lg rounded-2xl']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('hub.business.gateway')).'','variant' => 'primary','class' => 'w-full !h-14 font-black uppercase tracking-widest shadow-lg rounded-2xl']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            Aceder à Empresa
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
    <?php else: ?>
        <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['wire:click' => 'upgrade(\''.e($plan->slug).'\')','variant' => ''.e($isCurrent ? 'ghost' : 'primary').'','class' => 'w-full !h-14 font-black uppercase tracking-widest shadow-lg rounded-2xl transition-all active:scale-95','style' => ''.e(!$isCurrent ? 'background-color: ' . $brandColor . ';' : '').' '.e(!$isCurrent ? 'border: none;' : '').'','disabled' => $isCurrent]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'upgrade(\''.e($plan->slug).'\')','variant' => ''.e($isCurrent ? 'ghost' : 'primary').'','class' => 'w-full !h-14 font-black uppercase tracking-widest shadow-lg rounded-2xl transition-all active:scale-95','style' => ''.e(!$isCurrent ? 'background-color: ' . $brandColor . ';' : '').' '.e(!$isCurrent ? 'border: none;' : '').'','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isCurrent)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php echo e($isCurrent ? 'Plano Ativo' : 'Aderir ao ' . $plan->name); ?>

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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/livewire/partials/pricing-plan-card.blade.php ENDPATH**/ ?>