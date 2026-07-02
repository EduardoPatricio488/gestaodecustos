
<div
    x-data="{ open: false }"
    x-on:open-modal.window="if ($event.detail.name === 'add-invoice-modal') open = true"
    x-on:modal-close.window="if ($event.detail.name === 'add-invoice-modal') open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-[200] flex items-center justify-center bg-black/40 backdrop-blur-sm"
>

    <div
        x-show="open"
        x-transition
        class="w-full max-w-lg bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 p-6"
    >
        
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-black uppercase tracking-tight dark:text-white">
                Emitir Fatura
            </h2>

            <button
                @click="open = false"
                class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition"
            >
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'x-mark','class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'x-mark','class' => 'size-5']); ?>
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

        
        <form wire:submit.prevent="save" class="space-y-4">

            
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500">
                    Cliente
                </label>
                <input
                    type="text"
                    wire:model.defer="client_name"
                    class="w-full mt-1 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-sm"
                    placeholder="Nome do cliente"
                >
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['client_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500">
                    Nº Fatura
                </label>
                <input
                    type="text"
                    wire:model.defer="invoice_number"
                    class="w-full mt-1 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-sm"
                    placeholder="Automático se vazio"
                >
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['invoice_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500">
                    Valor Base (€)
                </label>
                <input
                    type="number"
                    step="0.01"
                    wire:model.live="amount_excl_vat"
                    class="w-full mt-1 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-sm"
                    placeholder="0.00"
                >
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['amount_excl_vat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500">
                    IVA (€)
                </label>
                <input
                    type="number"
                    step="0.01"
                    wire:model="vat_amount"
                    readonly
                    class="w-full mt-1 rounded-xl bg-zinc-200 dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-sm opacity-70"
                >
            </div>

            
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500">
                    Total (€)
                </label>
                <input
                    type="number"
                    step="0.01"
                    wire:model="total_amount"
                    readonly
                    class="w-full mt-1 rounded-xl bg-zinc-200 dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-sm opacity-70"
                >
            </div>

            
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500">
                    Data Limite
                </label>
                <input
                    type="date"
                    wire:model.defer="due_date"
                    class="w-full mt-1 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-sm"
                >
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['due_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <button
                type="submit"
                class="w-full py-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-black uppercase tracking-widest transition"
            >
                Emitir Fatura
            </button>
        </form>
    </div>
</div>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/livewire/business/partials/invoice-modal.blade.php ENDPATH**/ ?>