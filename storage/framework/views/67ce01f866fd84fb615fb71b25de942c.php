
<div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-8 sm:p-10 rounded-[2.5rem] shadow-2xl backdrop-blur-md space-y-8 text-left">

    
    <div class="flex justify-center">
        <div class="size-16 bg-zinc-900 rounded-2xl shadow-xl flex items-center justify-center border border-white/10">
            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'building-library','variant' => 'solid','class' => 'size-10 text-white']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'building-library','variant' => 'solid','class' => 'size-10 text-white']); ?>
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

    
    <div class="text-center space-y-2">
        <h1 class="text-2xl font-black text-zinc-900 dark:text-white uppercase italic tracking-tighter leading-none">
            Acesso de Auditoria
        </h1>
        <p class="text-[11px] text-zinc-500 font-medium italic">
            Introduz as credenciais institucionais para verificação.
        </p>
    </div>

    
    <form wire:submit.prevent="login" class="space-y-6">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('error')): ?>
            <div class="p-3 bg-red-500/10 text-red-500 text-[10px] font-bold rounded-xl border border-red-500/20 text-center uppercase tracking-widest italic">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="space-y-5">
            
            <div class="space-y-2" x-data="{
                nif: <?php if ((object) ('company_nif') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('company_nif'->value()); ?>')<?php echo e('company_nif'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('company_nif'); ?>')<?php endif; ?>,
                formatNIF(v) { if (!v) return ''; return v.replace(/\D/g, '').replace(/(\d{3})(?=\d)/g, '$1 ').substring(0, 11); }
            }" x-init="nif = formatNIF(nif)">
                <label class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1">NIF da Empresa Auditada</label>
                <input type="text" x-model="nif" x-on:input="nif = formatNIF($event.target.value)" placeholder="000 000 000"
                       class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-center font-mono font-bold text-sm focus:ring-2 focus:ring-zinc-500 outline-none transition-all dark:text-white" />
            </div>

            
            <div class="space-y-2">
                <label class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1">Código de Auditoria (Token)</label>
                <input wire:model="token" type="text" maxlength="6" placeholder="000000"
                       class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-center font-mono font-black text-xl tracking-[0.6em] text-zinc-900 dark:text-white focus:ring-2 focus:ring-zinc-500 outline-none transition-all" />
            </div>

            <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['type' => 'submit','variant' => 'primary','class' => 'w-full h-14 !bg-zinc-900 hover:!bg-zinc-800 text-white rounded-2xl font-black uppercase tracking-widest text-[11px] border-none mt-4 shadow-lg active:scale-95 transition-all']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'primary','class' => 'w-full h-14 !bg-zinc-900 hover:!bg-zinc-800 text-white rounded-2xl font-black uppercase tracking-widest text-[11px] border-none mt-4 shadow-lg active:scale-95 transition-all']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                Autenticar Auditoria
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
        </div>
    </form>

    
    <div class="text-center pt-2 border-t border-zinc-100 dark:border-zinc-800/50">
        <a href="/" class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.3em] hover:text-zinc-900 transition-colors">
            ← Voltar ao site principal
        </a>
    </div>
</div>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/livewire/public/bank-portal.blade.php ENDPATH**/ ?>