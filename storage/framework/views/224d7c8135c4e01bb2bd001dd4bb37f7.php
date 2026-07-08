<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, viewport-fit=cover">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(config('app.name')); ?> — Finanças pessoais inteligentes</title>

        
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#10b981">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Finance Pro">
        <link rel="apple-touch-icon" href="/pwa/splash_screens/apple-icon-180x180.png">

        <link rel="apple-touch-startup-image"
              href="/pwa/splash_screens/iPhone_16__iPhone_15_Pro__iPhone_15__iPhone_14_Pro_portrait.png"
              media="(device-width: 393px) and (device-height: 852px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">

        <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>💰</text></svg>">

        
        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark')
            } else {
                document.documentElement.classList.remove('dark')
            }
        </script>

        <?php echo $__env->make('partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>

    <body class="app-shell antialiased bg-zinc-50 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100 transition-colors duration-300">
    <?php
        $dashPath = '/dashboard';
        if(auth()->check() && auth()->user()->currentWorkspace?->type === 'company') {
            $dashPath = '/empresa/dashboard';
        }
    ?>

    <header class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-6">
        <span class="flex items-center gap-3 font-bold uppercase tracking-tighter">
            <span class="flex size-10 items-center justify-center rounded-xl brand-gradient shadow-lg shadow-brand-500/20">
                <svg class="size-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <?php echo e(config('app.name')); ?>

        </span>

        <nav class="flex items-center gap-4">
            <button
                x-data="{ darkMode: document.documentElement.classList.contains('dark') }"
                x-on:click="darkMode = !darkMode; darkMode ? (localStorage.theme = 'dark', document.documentElement.classList.add('dark')) : (localStorage.theme = 'light', document.documentElement.classList.remove('dark'))"
                class="mr-2 flex size-9 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-500 transition-all hover:bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800"
            >
                <?php if (isset($component)) { $__componentOriginald5f987720f2b51852a6659d9a2c7a66b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald5f987720f2b51852a6659d9a2c7a66b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.sun','data' => ['xShow' => 'darkMode','variant' => 'outline','class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon.sun'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-show' => 'darkMode','variant' => 'outline','class' => 'size-5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald5f987720f2b51852a6659d9a2c7a66b)): ?>
<?php $attributes = $__attributesOriginald5f987720f2b51852a6659d9a2c7a66b; ?>
<?php unset($__attributesOriginald5f987720f2b51852a6659d9a2c7a66b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald5f987720f2b51852a6659d9a2c7a66b)): ?>
<?php $component = $__componentOriginald5f987720f2b51852a6659d9a2c7a66b; ?>
<?php unset($__componentOriginald5f987720f2b51852a6659d9a2c7a66b); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal8aa8d5bc914d8b570a9db7c847a44cc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8aa8d5bc914d8b570a9db7c847a44cc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.moon','data' => ['xShow' => '!darkMode','variant' => 'outline','class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon.moon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-show' => '!darkMode','variant' => 'outline','class' => 'size-5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8aa8d5bc914d8b570a9db7c847a44cc2)): ?>
<?php $attributes = $__attributesOriginal8aa8d5bc914d8b570a9db7c847a44cc2; ?>
<?php unset($__attributesOriginal8aa8d5bc914d8b570a9db7c847a44cc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8aa8d5bc914d8b570a9db7c847a44cc2)): ?>
<?php $component = $__componentOriginal8aa8d5bc914d8b570a9db7c847a44cc2; ?>
<?php unset($__componentOriginal8aa8d5bc914d8b570a9db7c847a44cc2); ?>
<?php endif; ?>
            </button>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                <div class="flex items-center gap-3 pl-4 border-l border-zinc-200 dark:border-zinc-800">
                    <div class="hidden sm:flex flex-col items-end leading-none">
                        <span class="text-[9px] font-black uppercase text-zinc-400 tracking-widest">Sessão de</span>
                        <span class="text-sm font-bold text-zinc-900 dark:text-white"><?php echo e(auth()->user()->name); ?></span>
                    </div>

                    <a href="<?php echo e($dashPath); ?>" wire:navigate class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white shadow-lg shadow-brand-500/25 hover:bg-brand-500 transition-all">
                        Dashboard
                    </a>

                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" title="Sair da conta" class="flex size-9 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 dark:border-red-900/30 dark:bg-red-900/10 dark:text-red-500 dark:hover:bg-red-900/20 transition-all">
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'arrow-right-start-on-rectangle','variant' => 'micro','class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-right-start-on-rectangle','variant' => 'micro','class' => 'size-5']); ?>
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
                    </form>
                </div>
            <?php else: ?>
               <div class="flex items-center gap-2">
    
    <a href="/login" wire:navigate class="rounded-lg px-4 py-2 text-sm font-medium text-zinc-600 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800">
        Entrar
    </a>

    
    <a href="/register" wire:navigate class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white shadow-lg shadow-brand-500/25 hover:bg-brand-500">
        Começar grátis
    </a>

    
   
<div x-data="{ openExternal: false }" class="inline-block">

    
    <button @click="openExternal = true"
        class="px-5 py-2.5 border border-zinc-200 dark:border-zinc-800 rounded-xl text-sm font-bold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-all active:scale-95 shadow-sm uppercase tracking-tight">
        Área Externa
    </button>

    
    <template x-teleport="body">
        <div x-show="openExternal"
             x-transition.opacity
             class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/60 backdrop-blur-md">

            <div @click.outside="openExternal = false"
                 x-show="openExternal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 class="w-full max-w-2xl bg-white dark:bg-zinc-900 rounded-[3rem] shadow-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">

                
                <div class="p-10 pb-0 flex justify-between items-start text-left">
                    <div>
                        <h2 class="text-3xl font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white leading-none">Selecione o seu Perfil</h2>
                        <p class="text-sm text-zinc-500 mt-2 font-medium">Escolha a porta de entrada para o ecossistema empresarial.</p>
                    </div>
                    <button @click="openExternal = false" class="p-2 text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">
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

                <div class="p-10 space-y-10">

                    
                    <div class="space-y-4">
                        <p class="text-[10px] font-black uppercase text-zinc-400 tracking-[0.3em] px-1">Terminal de Negócios</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                            
                            <a href="<?php echo e(route('client.login')); ?>" class="group p-6 bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-800 rounded-3xl hover:border-emerald-500/50 hover:shadow-xl hover:shadow-emerald-500/5 transition-all text-left">
                                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'user-group','variant' => 'outline','class' => 'size-8 text-emerald-600 mb-4 group-hover:scale-110 transition-transform']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'user-group','variant' => 'outline','class' => 'size-8 text-emerald-600 mb-4 group-hover:scale-110 transition-transform']); ?>
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
                                <span class="block font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">Sou Cliente</span>
                                <span class="block text-[10px] text-zinc-500 mt-1 uppercase font-bold leading-tight">Acesso ao Portal e Faturação</span>
                            </a>

                            
                            <a href="<?php echo e(route('supplier.portal')); ?>" class="group p-6 bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-800 rounded-3xl hover:border-brand-500/50 hover:shadow-xl hover:shadow-brand-500/5 transition-all text-left">
                                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'building-storefront','variant' => 'outline','class' => 'size-8 text-brand-600 mb-4 group-hover:scale-110 transition-transform']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'building-storefront','variant' => 'outline','class' => 'size-8 text-brand-600 mb-4 group-hover:scale-110 transition-transform']); ?>
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
                                <span class="block font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">Sou Fornecedor</span>
                                <span class="block text-[10px] text-zinc-500 mt-1 uppercase font-bold leading-tight">Gestão de Encomendas e Pagamentos</span>
                            </a>

                            
                            <a href="<?php echo e(route('bank.portal')); ?>" class="group p-6 bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-800 rounded-3xl hover:border-zinc-400 transition-all text-left">
                                <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'building-library','variant' => 'outline','class' => 'size-8 text-zinc-600 dark:text-zinc-400 mb-4 group-hover:scale-110 transition-transform']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'building-library','variant' => 'outline','class' => 'size-8 text-zinc-600 dark:text-zinc-400 mb-4 group-hover:scale-110 transition-transform']); ?>
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
                                <span class="block font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">Entidade Bancária</span>
                                <span class="block text-[10px] text-zinc-500 mt-1 uppercase font-bold leading-tight">Auditoria e Verificação de Fundos</span>
                            </a>

                        </div>
                    </div>

                    
                    <div class="space-y-4">
                        <p class="text-[10px] font-black uppercase text-zinc-400 tracking-[0.3em] px-1">Talento & Carreiras</p>
                        <a href="<?php echo e(route('careers.apply')); ?>" class="flex items-center justify-between p-6 bg-zinc-950 rounded-3xl border border-zinc-800 group hover:border-brand-500 transition-all shadow-xl">
                            <div class="flex items-center gap-6 text-left">
                                <div class="size-14 rounded-2xl bg-brand-500/10 flex items-center justify-center text-brand-500">
                                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'briefcase','variant' => 'solid','class' => 'size-7']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'briefcase','variant' => 'solid','class' => 'size-7']); ?>
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
                                    <span class="block font-black uppercase italic tracking-tighter text-white text-xl">Candidato a Emprego</span>
                                    <span class="block text-xs text-zinc-500 mt-1 font-medium">Submeta a sua candidatura e junte-se à nossa equipa.</span>
                                </div>
                            </div>
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'arrow-right','class' => 'size-6 text-zinc-700 group-hover:text-brand-500 group-hover:translate-x-2 transition-all']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-right','class' => 'size-6 text-zinc-700 group-hover:text-brand-500 group-hover:translate-x-2 transition-all']); ?>
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
                        </a>
                    </div>
                </div>

                
                <div class="px-10 py-6 bg-zinc-50 dark:bg-zinc-950 border-t border-zinc-100 dark:border-zinc-800 text-center">
                    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">Protocolo de Acesso Seguro · <?php echo e(date('Y')); ?></p>
                </div>

            </div>
        </div>
    </template>
</div>
</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </nav>
    </header>

    <main class="mx-auto max-w-6xl px-6 pb-24 pt-8 lg:pt-16">
        <!-- HERO SECTION -->
        <div class="grid items-center gap-16 lg:grid-cols-2">
            <div>
                <p class="mb-4 inline-flex items-center gap-2 rounded-full border border-brand-500/20 bg-brand-500/10 px-3 py-1 text-sm font-medium text-brand-700 dark:text-brand-300">
                    <span class="size-2 rounded-full bg-brand-500 animate-pulse"></span>
                    Finanças pessoais com IA
                </p>
                <h1 class="text-5xl font-black tracking-tighter text-zinc-900 sm:text-6xl lg:text-7xl dark:text-white leading-[0.9]">
                    Todos os custos da vida, <br>
                    <span class="bg-gradient-to-r from-brand-500 to-brand-700 bg-clip-text text-transparent ">num só lugar</span>
                </h1>
                <p class="mt-6 text-lg leading-relaxed text-zinc-600 dark:text-zinc-400 max-w-md font-medium">
                    Casa, carro, trabalho, saúde — regista despesas por categoria, acompanha tendências no dashboard e recebe insights da IA.
                </p>
                <div class="mt-10 flex flex-wrap gap-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->guest()): ?>
                        <a href="/register" wire:navigate class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-8 py-4 font-bold text-white shadow-xl shadow-brand-500/30 hover:bg-brand-500 transition-all hover:-translate-y-1">
                            Criar conta grátis
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'arrow-right','variant' => 'micro']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-right','variant' => 'micro']); ?>
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
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <a href="<?php echo e(auth()->check() ? $dashPath : '/login'); ?>" wire:navigate class="inline-flex items-center rounded-xl border border-zinc-300 bg-white dark:bg-zinc-900 px-8 py-4 font-bold hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800 transition-all">
                        <?php echo e(auth()->check() ? 'Abrir dashboard' : 'Já tenho conta'); ?>

                    </a>
                </div>
            </div>

            <!-- DEMO CARD -->
            <div class="relative">
                <div class="glass-card space-y-6 p-8 shadow-2xl shadow-brand-500/10 border-brand-500/10 rounded-[2.5rem]">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black uppercase tracking-widest text-zinc-400">Gasto este mês</span>
                        <?php if (isset($component)) { $__componentOriginal4cc377eda9b63b796b6668ee7832d023 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4cc377eda9b63b796b6668ee7832d023 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::badge.index','data' => ['variant' => 'success','class' => 'bg-brand-500/10 text-brand-600 border-none font-bold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'success','class' => 'bg-brand-500/10 text-brand-600 border-none font-bold']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Demo Live <?php echo $__env->renderComponent(); ?>
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
                    <p class="text-5xl font-black dark:text-white tracking-tighter">1.247,50 €</p>
                    <div class="space-y-4 pt-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [['Casa', '#3b82f6', 42], ['Carro', '#ef4444', 28], ['Alimentação', '#f59e0b', 18]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $color, $pct]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="space-y-1">
                                <div class="flex justify-between text-[10px] font-black uppercase tracking-wider">
                                    <span class="text-zinc-500"><?php echo e($label); ?></span>
                                    <span class="text-zinc-900 dark:text-zinc-100"><?php echo e($pct); ?>%</span>
                                </div>
                                <div class="h-1.5 rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-1000" style="width: <?php echo e($pct); ?>%; background: <?php echo e($color); ?>"></div>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                    <div class="flex h-20 items-end gap-2 border-t border-zinc-100 dark:border-zinc-800 pt-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [40, 65, 30, 80, 50, 100, 60]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="flex-1 rounded-t-sm bg-brand-500/20 hover:bg-brand-500 transition-all cursor-pointer" style="height: <?php echo e($h); ?>%"></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION: HOW IT WORKS -->
        <div class="mt-32 text-center space-y-4">
            <h2 class="text-xs font-black uppercase tracking-[0.3em] text-brand-600">Fluxo Simples</h2>
            <p class="text-4xl font-black dark:text-white tracking-tighter uppercase">Como funciona o <?php echo e(config('app.name')); ?>?</p>

            <div class="mt-16 grid gap-8 sm:grid-cols-3">
                <div class="space-y-4">
                    <div class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-white dark:bg-zinc-900 shadow-lg font-black text-xl text-brand-500 border border-zinc-100 dark:border-zinc-800">1</div>
                    <h4 class="font-bold text-lg">Regista</h4>
                    <p class="text-sm text-zinc-500 leading-relaxed">Adiciona os teus ganhos e gastos em segundos, seja no PC ou no telemóvel.</p>
                </div>
                <div class="space-y-4">
                    <div class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-white dark:bg-zinc-900 shadow-lg font-black text-xl text-brand-500 border border-zinc-100 dark:border-zinc-800">2</div>
                    <h4 class="font-bold text-lg">Planeia</h4>
                    <p class="text-sm text-zinc-500 leading-relaxed">Define orçamentos por categoria e metas de poupança para os teus sonhos.</p>
                </div>
                <div class="space-y-4">
                    <div class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-white dark:bg-zinc-900 shadow-lg font-black text-xl text-brand-500 border border-zinc-100 dark:border-zinc-800">3</div>
                    <h4 class="font-bold text-lg">Evolui</h4>
                    <p class="text-sm text-zinc-500 leading-relaxed">Deixa a nossa IA analisar os teus padrões e sugerir onde podes poupar mais.</p>
                </div>
            </div>
        </div>

        <!-- SECTION: NEW ECOSYSTEM FEATURES (Adicionado para refletir o código atual) -->
        <div class="mt-32">
             <div class="text-center mb-16">
                <h2 class="text-xs font-black uppercase tracking-[0.3em] text-brand-600">Além das Finanças</h2>
                <p class="text-4xl font-black dark:text-white tracking-tighter uppercase italic">O Ecossistema Finance Connect</p>
            </div>

            <div class="grid gap-8 md:grid-cols-2">
                <div class="glass-card p-8 flex gap-6 items-start">
                    <div class="size-14 shrink-0 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-indigo-600">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'globe-alt','variant' => 'outline']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'globe-alt','variant' => 'outline']); ?>
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
                        <h3 class="font-black uppercase tracking-tight text-zinc-900 dark:text-white">Finance Connect (Social)</h3>
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Junta-te à comunidade. Partilha metas, segue amigos e aprende com as estratégias de outros investidores de forma anónima ou pública.</p>
                    </div>
                </div>

                <div class="glass-card p-8 flex gap-6 items-start">
                    <div class="size-14 shrink-0 rounded-2xl bg-orange-500/10 flex items-center justify-center text-orange-600">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'bolt','variant' => 'outline']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'bolt','variant' => 'outline']); ?>
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
                        <h3 class="font-black uppercase tracking-tight text-zinc-900 dark:text-white">Fitness & Saúde</h3>
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Sincroniza o teu Strava ou MiFitness. No <?php echo e(config('app.name')); ?>, acreditamos que a saúde física é o maior ativo do teu património.</p>
                    </div>
                </div>

                <div class="glass-card p-8 flex gap-6 items-start">
                    <div class="size-14 shrink-0 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-600">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'trophy','variant' => 'outline']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'trophy','variant' => 'outline']); ?>
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
                        <h3 class="font-black uppercase tracking-tight text-zinc-900 dark:text-white">Gamificação & Níveis</h3>
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Ganha XP ao registar despesas e concluir metas. Sobe de nível e desbloqueia medalhas exclusivas enquanto dominas o teu dinheiro.</p>
                    </div>
                </div>

                <div class="glass-card p-8 flex gap-6 items-start">
                    <div class="size-14 shrink-0 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-600">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'document-text','variant' => 'outline']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'document-text','variant' => 'outline']); ?>
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
                        <h3 class="font-black uppercase tracking-tight text-zinc-900 dark:text-white">Relatórios de Auditoria PDF</h3>
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Exporta relatórios certificados e detalhados. Perfeito para contabilidade, gestão de casal ou análise de auditoria profunda.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION: FEATURES -->
        <div class="mt-32 grid gap-6 sm:grid-cols-3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                ['briefcase', 'Património Líquido', 'Soma total dos teus ativos e investimentos menos passivos.'],
                ['credit-card', 'Assinaturas & Fixos', 'Controlo total de débitos diretos, Netflix, rendas e ginásio.'],
                ['magnifying-glass', 'Busca Global (Ctrl+K)', 'Encontra qualquer transação, contacto ou lembrete instantaneamente.'],
                ['chart-bar', 'Análise de Fluxo', 'Gráficos comparativos de fluxo de caixa e rácio de poupança mensal.'],
                ['building-office-2', 'Espaços Business', 'Gestão separada para a tua empresa: CRM, Projetos e Faturação.'],
                ['sparkles', 'CFO Inteligente', 'O teu consultor de IA que analisa riscos e sugere melhorias no teu estilo de vida.'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$icon, $title, $desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="glass-card p-8 group hover:border-brand-500/50 transition-all duration-300 rounded-[2rem]">
                    <span class="mb-6 flex size-12 items-center justify-center rounded-2xl bg-brand-500/10 text-brand-600 dark:text-brand-400 group-hover:scale-110 transition-transform">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => $icon,'variant' => 'outline']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($icon),'variant' => 'outline']); ?>
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
                    </span>
                    <h3 class="font-black uppercase tracking-tight text-zinc-900 dark:text-white"><?php echo e($title); ?></h3>
                    <p class="mt-3 text-sm leading-relaxed text-zinc-500 dark:text-zinc-400"><?php echo e($desc); ?></p>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        <!-- SECTION: SECURITY -->
        <div class="mt-32 p-1 bg-gradient-to-r from-zinc-200 to-zinc-300 dark:from-zinc-800 dark:to-zinc-700 rounded-[3rem]">
            <div class="bg-white dark:bg-zinc-950 rounded-[2.9rem] p-12 text-center space-y-6">
                <div class="flex justify-center">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'shield-check','class' => 'size-16 text-emerald-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shield-check','class' => 'size-16 text-emerald-500']); ?>
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
                <h2 class="text-3xl font-black dark:text-white uppercase tracking-tighter italic">Privacidade Total</h2>
                <p class="text-zinc-500 max-w-2xl mx-auto leading-relaxed font-medium">
                    Os teus dados financeiros são encriptados e nunca são partilhados com terceiros.
                    A nossa missão é apenas ajudar-te a crescer o teu património com segurança.
                </p>
            </div>
        </div>

        <!-- FINAL CTA -->
        <div class="mt-32 text-center space-y-10 pb-20">
            <h2 class="text-5xl md:text-6xl font-black tracking-tighter dark:text-white leading-tight uppercase italic">
                Pronto para dominar as <br>
                <span class="text-brand-500 italic underline decoration-zinc-200 dark:decoration-zinc-800">tuas finanças?</span>
            </h2>
            <div class="flex justify-center gap-4">
                <a href="<?php echo e(auth()->check() ? $dashPath : '/register'); ?>" wire:navigate class="rounded-2xl bg-brand-600 px-12 py-5 text-xl font-black text-white shadow-2xl shadow-brand-500/40 hover:bg-brand-500 transition-all hover:scale-105 uppercase tracking-tighter">
                    <?php echo e(auth()->check() ? 'Voltar ao Dashboard' : 'Começar Agora — É Grátis'); ?>

                </a>
            </div>
            <p class="text-zinc-400 text-[10px] font-black uppercase tracking-[0.4em]">© <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?> · High Fidelity Dashboard</p>
        </div>
    </main>

    <?php app('livewire')->forceAssetInjection(); ?>
<?php echo app('flux')->scripts(); ?>

</body>
</html>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/welcome.blade.php ENDPATH**/ ?>