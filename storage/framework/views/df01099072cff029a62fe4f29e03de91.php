<div class="space-y-8 pb-20" x-data="{ cartOpen: false }" @open-cart.window="cartOpen = true">
    
    <header class="relative overflow-hidden bg-zinc-100 rounded-[3rem] p-10 border border-zinc-300 shadow-xl">
        <div class="relative z-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8">
            <div class="text-left">
                <?php if (isset($component)) { $__componentOriginal4cc377eda9b63b796b6668ee7832d023 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4cc377eda9b63b796b6668ee7832d023 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::badge.index','data' => ['variant' => 'neutral','class' => 'bg-zinc-200 border-zinc-300 text-zinc-700 font-black uppercase tracking-widest text-[9px] mb-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'neutral','class' => 'bg-zinc-200 border-zinc-300 text-zinc-700 font-black uppercase tracking-widest text-[9px] mb-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    Finance Hub PRO Store
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
                <h1 class="text-4xl font-black text-zinc-900 uppercase italic tracking-tighter leading-none">
                    Extensões PRO & IA Financeira
                </h1>
                <p class="text-zinc-600 font-medium mt-4 max-w-xl">
                    Pesquisa, compara e adquire extensões premium com confiança.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">

                <a href="<?php echo e(route('store.wishlist')); ?>" wire:navigate class="px-4 py-2.5 bg-white border border-zinc-300 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-zinc-50 flex items-center gap-2">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'heart','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'heart','class' => 'size-4']); ?>
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
<?php endif; ?> Favoritos
                </a>
                <a href="<?php echo e(route('store.compare')); ?>" wire:navigate class="px-4 py-2.5 bg-white border border-zinc-300 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-zinc-50 flex items-center gap-2">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'scale','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'scale','class' => 'size-4']); ?>
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
<?php endif; ?> Comparar
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($compareCount > 0): ?>
                        <span class="bg-brand-500 text-white text-[9px] px-1.5 py-0.5 rounded-full"><?php echo e($compareCount); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </a>
                

<button @click="cartOpen = true" type="button" class="relative px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-500 flex items-center gap-2 shadow-lg transition-all active:scale-95">
    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'shopping-cart','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shopping-cart','class' => 'size-4']); ?>
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
    Carrinho
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cartCount > 0): ?>
        <span class="absolute -top-2 -right-2 min-w-5 h-5 px-1 flex items-center justify-center bg-white text-emerald-700 text-[9px] font-black rounded-full border-2 border-emerald-600 animate-bounce">
            <?php echo e($cartCount); ?>

        </span>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</button>
                
            </div>
        </div>
    </header>
 
    <template x-teleport="body">
        <div>
            <!-- Fundo escurecido (Backdrop) -->
            <div x-show="cartOpen"
                 x-transition.opacity
                 @click="cartOpen = false"
                 class="fixed inset-0 bg-zinc-950/40 backdrop-blur-sm z-[999]">
            </div>

            <!-- Painel Lateral -->
            <div x-show="cartOpen"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="fixed right-0 top-0 h-screen w-full max-w-sm bg-white dark:bg-zinc-900 shadow-2xl z-[1000] flex flex-col border-l border-zinc-200 dark:border-zinc-800">

                <div class="p-6 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center bg-zinc-50 dark:bg-zinc-950">
                    <div class="flex items-center gap-3">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'shopping-cart','class' => 'size-5 text-emerald-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shopping-cart','class' => 'size-5 text-emerald-600']); ?>
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
                        <h2 class="text-sm font-black uppercase tracking-widest text-zinc-800 dark:text-white leading-none">O Teu Carrinho</h2>
                    </div>
                    <button @click="cartOpen = false" class="p-2 hover:bg-zinc-200 dark:hover:bg-zinc-800 rounded-full transition-colors">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'x-mark','variant' => 'micro','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'x-mark','variant' => 'micro','class' => 'size-4']); ?>
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

                <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar text-left">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="flex gap-4 group animate-in slide-in-from-right-4 duration-300 items-center text-left">
                            <div class="size-16 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-3xl shadow-inner shrink-0">
                                <?php echo e($item['image']); ?>

                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-[11px] font-black uppercase text-zinc-800 dark:text-white truncate"><?php echo e($item['title']); ?></h4>
                                <p class="text-[10px] text-emerald-600 font-bold mt-1 uppercase"><?php echo e(number_format($item['price'], 2, ',', '.')); ?> €</p>

                                <button wire:click="removeFromCart(<?php echo e($item['id']); ?>)" class="mt-2 text-[8px] font-black text-red-500 uppercase hover:underline">Remover</button>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="h-full flex flex-col items-center justify-center text-center space-y-4 opacity-30">
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'shopping-cart','class' => 'size-12']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shopping-cart','class' => 'size-12']); ?>
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
                            <p class="text-[10px] font-black uppercase tracking-widest">O carrinho está vazio</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($cartItems) > 0): ?>
                    <div class="p-8 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 space-y-4">
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-[10px] font-black uppercase text-zinc-400">Total:</span>
                            <span class="text-2xl font-black italic text-zinc-900 dark:text-white">
                                <?php echo e(number_format($cartTotal, 2, ',', '.')); ?> €
                            </span>
                        </div>

                        <div class="grid gap-3">
    
    <a href="<?php echo e(route('store.checkout')); ?>" wire:navigate class="w-full h-14 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl flex items-center justify-center gap-3 font-black uppercase text-[11px] tracking-[0.2em] shadow-lg transition-all active:scale-95">
        Finalizar Compra
    </a>

    
    <a href="<?php echo e(route('store.cart')); ?>" wire:navigate class="w-full py-3 text-center bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-[9px] font-black uppercase tracking-widest text-zinc-500 hover:bg-zinc-200 transition-colors">
        Ver Carrinho Detalhado
    </a>
</div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </template>
    
    <a href="<?php echo e(route('hub.inventory')); ?>" wire:navigate
       class="group flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-6 bg-gradient-to-r from-emerald-600 to-emerald-500 rounded-3xl text-white shadow-xl hover:shadow-2xl hover:from-emerald-500 hover:to-emerald-400 transition-all">
        <div class="flex items-center gap-4">
            <div class="size-14 bg-white/20 rounded-2xl flex items-center justify-center group-hover:scale-105 transition-transform">
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'archive-box','class' => 'size-7']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'archive-box','class' => 'size-7']); ?>
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
            <div class="text-left">
                <p class="text-[10px] font-black uppercase tracking-[0.25em] text-emerald-100">Os teus produtos</p>
                <h2 class="text-xl font-black uppercase italic tracking-tight">O Meu Inventário</h2>
                <p class="text-sm text-emerald-50 mt-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inventoryCount > 0): ?>
                        Tens <?php echo e($inventoryCount); ?> <?php echo e($inventoryCount === 1 ? 'recurso ativo' : 'recursos ativos'); ?> — descarrega, ativa e gere tudo aqui.
                    <?php else: ?>
                        Ainda não tens produtos. Após comprar, aparecem aqui para download imediato.
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>
            </div>
        </div>
        <span class="shrink-0 px-6 py-3 bg-white text-emerald-700 rounded-2xl text-[10px] font-black uppercase tracking-widest group-hover:bg-emerald-50 transition-colors">
            Abrir Inventário →
        </span>
    </a>

    
    <div class="bg-white border border-zinc-200 rounded-3xl p-6 shadow-sm space-y-4">
        <div class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1 relative">
                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'magnifying-glass','class' => 'absolute left-4 top-1/2 -translate-y-1/2 size-5 text-zinc-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'magnifying-glass','class' => 'absolute left-4 top-1/2 -translate-y-1/2 size-5 text-zinc-400']); ?>
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
                <input wire:model.live.debounce.300ms="search" type="search" placeholder="Pesquisar produtos..."
                       class="w-full pl-12 pr-4 py-3 rounded-2xl border border-zinc-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none text-sm" />
            </div>
            <div class="flex flex-wrap gap-3">
                <input wire:model.live.debounce.500ms="priceMin" type="number" step="0.01" min="0" placeholder="Min €"
                       class="w-24 px-3 py-3 rounded-xl border border-zinc-200 text-sm" />
                <input wire:model.live.debounce.500ms="priceMax" type="number" step="0.01" min="0" placeholder="Max €"
                       class="w-24 px-3 py-3 rounded-xl border border-zinc-200 text-sm" />
                <select wire:model.live="sortBy" class="px-4 py-3 rounded-xl border border-zinc-200 text-sm font-bold">
                    <option value="popular">Mais Popular</option>
                    <option value="rating">Melhor Avaliado</option>
                    <option value="price_asc">Preço ↑</option>
                    <option value="price_desc">Preço ↓</option>
                    <option value="newest">Mais Recente</option>
                </select>
                <label class="flex items-center gap-2 px-4 py-3 rounded-xl border border-zinc-200 text-sm font-bold cursor-pointer">
                    <input wire:model.live="onlyFeatured" type="checkbox" class="rounded" />
                    Destaques
                </label>
                <button wire:click="clearFilters" class="px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-500 hover:bg-zinc-100">
                    Limpar
                </button>
            </div>
        </div>

        
        <?php $storeTabs = app(\App\Services\StoreTabsService::class)->visible(); ?>
        <div class="flex overflow-x-auto gap-1 p-1 bg-zinc-100 rounded-2xl">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $storeTabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <button wire:click="setTab('<?php echo e($tab['key']); ?>')"
                        class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest whitespace-nowrap transition-all
                               <?php echo e($activeTab === $tab['key'] ? 'bg-zinc-800 text-white' : 'text-zinc-600 hover:bg-zinc-200'); ?>">
                    <?php echo e($tab['label']); ?>

                </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bundles->isNotEmpty() && $activeTab === 'all' && !$search): ?>
        <section class="space-y-4">
            <h2 class="text-lg font-black uppercase tracking-tight">Packs & Bundles</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $bundles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bundle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="bg-gradient-to-br from-brand-50 to-white border border-brand-200 rounded-3xl p-6 flex gap-4">
                        <div class="text-5xl"><?php echo e($bundle->image); ?></div>
                        <div class="flex-1">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bundle->badge): ?><span class="text-[9px] font-black text-brand-600 uppercase"><?php echo e($bundle->badge); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <h3 class="font-black uppercase"><?php echo e($bundle->title); ?></h3>
                            <p class="text-xs text-zinc-600 mt-1"><?php echo e($bundle->description); ?></p>
                            <p class="text-xs text-emerald-600 font-bold mt-2">Poupa <?php echo e($bundle->savings_percent); ?>%</p>
                            <div class="flex items-center gap-3 mt-4">
                                <span class="text-xl font-black"><?php echo e(number_format($bundle->price, 2, ',', '.')); ?> €</span>
                                <span class="text-xs text-zinc-400 line-through"><?php echo e(number_format($bundle->individualTotal(), 2, ',', '.')); ?> €</span>
                            </div>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($products->isEmpty()): ?>
        <div class="py-20 text-center bg-zinc-50 border-2 border-dashed border-zinc-200 rounded-[3rem]">
            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'magnifying-glass','class' => 'size-12 mx-auto mb-4 text-zinc-300']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'magnifying-glass','class' => 'size-12 mx-auto mb-4 text-zinc-300']); ?>
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
            <h3 class="text-xl font-black text-zinc-400 uppercase">Nenhum produto encontrado</h3>
            <button wire:click="clearFilters" class="mt-4 text-sm font-bold text-brand-600">Limpar filtros</button>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="group relative bg-zinc-50 border border-zinc-200 rounded-[2.5rem] overflow-hidden flex flex-col hover:border-brand-500 hover:shadow-xl transition-all" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'product-'.e($product->id).''; ?>wire:key="product-<?php echo e($product->id); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->badge): ?>
                        <div class="absolute m-6 z-10">
                            <span class="px-3 py-1 bg-brand-600 text-white text-[9px] font-black uppercase rounded-full"><?php echo e($product->badge); ?></span>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="h-44 bg-gradient-to-br from-zinc-100 to-zinc-200 flex items-center justify-center text-6xl relative">
                        <span class="group-hover:scale-110 transition-transform"><?php echo e($product->image); ?></span>
                        <div class="absolute top-4 right-4 flex gap-2">
                            <button wire:click="toggleWishlist(<?php echo e($product->id); ?>)"
                                    class="p-2 bg-white/90 rounded-xl shadow hover:scale-110 transition-transform"
                                    title="Favoritos">
                                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'heart','class' => 'size-4 '.e(in_array($product->id, $wishlistIds) ? 'text-red-500 fill-red-500' : 'text-zinc-400').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'heart','class' => 'size-4 '.e(in_array($product->id, $wishlistIds) ? 'text-red-500 fill-red-500' : 'text-zinc-400').'']); ?>
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
                            <button wire:click="addToCompare(<?php echo e($product->id); ?>)"
                                    class="p-2 bg-white/90 rounded-xl shadow hover:scale-110 transition-transform"
                                    title="Comparar">
                                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'scale','class' => 'size-4 text-zinc-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'scale','class' => 'size-4 text-zinc-400']); ?>
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

                    <div class="p-6 flex flex-col flex-1">
                        <span class="text-[9px] font-black text-brand-600 uppercase tracking-widest"><?php echo e($product->category_label); ?></span>
                        <h3 class="text-lg font-black uppercase leading-tight mt-1"><?php echo e($product->title); ?></h3>
                        <p class="text-xs text-zinc-600 mt-2 line-clamp-2"><?php echo e($product->description); ?></p>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->rating_count > 0): ?>
                            <div class="flex items-center gap-2 mt-3">
                                <span class="text-amber-500 text-sm">★ <?php echo e(number_format($product->rating_avg, 1)); ?></span>
                                <span class="text-[10px] text-zinc-400">(<?php echo e($product->rating_count); ?> reviews)</span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-auto pt-4 flex items-center justify-between border-t border-zinc-200 mt-4">
                            <span class="text-2xl font-black italic"><?php echo e(number_format($product->price, 2, ',', '.')); ?> €</span>
                            <div class="flex flex-col gap-1.5">
                                <a href="<?php echo e(route('store.product.show', $product)); ?>" wire:navigate
                                   class="text-center px-3 py-2 bg-white border border-zinc-200 rounded-xl text-[9px] font-black uppercase">Ver</a>
                                <button wire:click="addToCart(<?php echo e($product->id); ?>)"
                                        class="px-3 py-2 bg-zinc-200 rounded-xl text-[9px] font-black uppercase">+ Carrinho</button>
                                <button wire:click="buyNow(<?php echo e($product->id); ?>)"
                                        class="px-3 py-2 bg-brand-600 text-white rounded-xl text-[9px] font-black uppercase">Comprar</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'all' && $planProducts->isNotEmpty() && !$search): ?>
        <section class="space-y-4">
            <h2 class="text-lg font-black uppercase">Planos PRO</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $planProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['border rounded-3xl p-6', $index === 1 ? 'bg-zinc-200 border-zinc-400 shadow-lg' : 'bg-zinc-50 border-zinc-200']); ?>">
                        <h3 class="font-black uppercase"><?php echo e($plan->title); ?></h3>
                        <p class="text-xs text-zinc-600 mt-2"><?php echo e($plan->description); ?></p>
                        <p class="text-2xl font-black mt-4"><?php echo e(number_format($plan->price, 2, ',', '.')); ?> €/mês</p>
                        <div class="flex gap-2 mt-4">
                            <button wire:click="addToCart(<?php echo e($plan->id); ?>)" class="flex-1 py-2 bg-white border rounded-xl text-[9px] font-black uppercase">+ Carrinho</button>
                            <button wire:click="buyNow(<?php echo e($plan->id); ?>)" class="flex-1 py-2 bg-zinc-900 text-white rounded-xl text-[9px] font-black uppercase">Subscrever</button>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/livewire/store/hub-store.blade.php ENDPATH**/ ?>