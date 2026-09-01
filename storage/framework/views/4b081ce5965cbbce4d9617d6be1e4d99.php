<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

?>

<section class="space-y-6" x-data="{ openDeleteModal: false }">
    <header>
        <h2 class="text-lg font-black text-red-600 uppercase tracking-tighter italic">Eliminar Conta</h2>
        <p class="text-xs text-zinc-500 font-medium">Esta ação é permanente. Todos os teus dados serão apagados para sempre.</p>
    </header>

    
    <button
        type="button"
        @click="openDeleteModal = true"
        class="bg-red-600 hover:bg-red-700 text-white font-black uppercase text-[10px] px-8 py-3 rounded-xl shadow-lg shadow-red-500/20 transition-all active:scale-95"
    >
        Eliminar Conta Definitivamente
    </button>

    
    <div
        x-show="openDeleteModal"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-[500] flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm"
    >
        
        <div
            @click.away="openDeleteModal = false"
            class="bg-white dark:bg-zinc-900 w-full max-w-md rounded-[2.5rem] p-10 shadow-2xl border border-zinc-200 dark:border-zinc-800 space-y-8"
        >
            <div class="text-center space-y-3">
                <div class="size-12 bg-red-500/10 rounded-2xl flex items-center justify-center mx-auto text-red-600">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'exclamation-triangle','variant' => 'solid','class' => 'size-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'exclamation-triangle','variant' => 'solid','class' => 'size-6']); ?>
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
                <h2 class="text-xl font-black text-zinc-900 dark:text-white uppercase italic tracking-tighter">Confirmar Eliminação</h2>
                <p class="text-xs text-zinc-500 leading-relaxed font-medium">
                    Por segurança, insere a tua password para apagar todos os teus dados e progresso na rede social.
                </p>
            </div>

            <form wire:submit.prevent="deleteUser" class="space-y-6 text-left">
                <div class="space-y-2">
                    <label class="text-[9px] font-black uppercase text-zinc-400 ml-1">Password de Segurança</label>
                    <input
                        type="password"
                        wire:model="password"
                        placeholder="Insere a tua password"
                        class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-sm font-bold text-zinc-900 dark:text-white outline-none focus:ring-2 focus:ring-red-500/20 shadow-inner"
                    >
                    <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('password'),'class' => 'px-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('password')),'class' => 'px-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                </div>

                <div class="flex gap-3">
                    <button
                        type="button"
                        @click="openDeleteModal = false"
                        class="flex-1 px-6 py-3 rounded-xl text-[10px] font-black uppercase text-zinc-400 hover:text-zinc-600 transition-colors"
                    >
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="flex-[2] bg-red-600 hover:bg-red-700 text-white rounded-xl font-black uppercase text-[10px] h-12 shadow-xl shadow-red-500/30 active:scale-95 transition-all"
                    >
                        Confirmar e Apagar 🟢
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<style>
    [x-cloak] { display: none !important; }
</style><?php /**PATH C:\Projetos\gestao-de-custos\resources\views\livewire/profile/delete-user-form.blade.php ENDPATH**/ ?>