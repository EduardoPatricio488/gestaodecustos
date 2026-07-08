<!DOCTYPE html>
<html lang="pt" class="h-full bg-zinc-50 dark:bg-zinc-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificar Conta | Finance Pro</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="h-full antialiased font-sans">
    <div class="min-h-screen flex flex-col items-center justify-center p-6">

        <div class="w-full max-w-md space-y-8">

            
            <div class="text-center">
                <div class="inline-flex p-4 bg-emerald-500/10 rounded-[2rem] mb-6 shadow-inner">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'shield-check','class' => 'size-10 text-emerald-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shield-check','class' => 'size-10 text-emerald-600']); ?>
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
                <h1 class="text-4xl font-black dark:text-white uppercase tracking-tighter italic leading-none">
                    Verificar Conta
                </h1>
                <p class="text-zinc-500 font-medium mt-4 text-sm leading-relaxed px-8">
                    Enviámos um código de segurança para o teu e-mail. Introduz os 6 dígitos abaixo para ativar o teu acesso.
                </p>
            </div>

            
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[3rem] p-10 shadow-2xl transition-all">

                <form action="<?php echo e(route('verification.verify-code')); ?>" method="POST" class="space-y-8">
                    <?php echo csrf_field(); ?>

                    <div class="space-y-4 text-left">
                        <label class="text-[10px] font-black uppercase text-zinc-400 tracking-[0.3em] ml-1">
                            Código de Segurança
                        </label>

                        <input
                            type="text"
                            name="code"
                            maxlength="6"
                            placeholder="000000"
                            autofocus
                            class="w-full h-20 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-100 dark:border-zinc-800 rounded-[1.5rem]
                                   text-center text-4xl font-black tracking-[0.5em] text-emerald-600 outline-none
                                   focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-inner"
                        >

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs font-bold text-center mt-2"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <button type="submit" class="w-full h-16 rounded-2xl font-black uppercase tracking-widest bg-emerald-600 hover:bg-emerald-700 text-white border-none shadow-xl shadow-emerald-500/20 text-sm transition-all active:scale-95">
                        Ativar Acesso 🟢
                    </button>
                </form>

                
                <div class="mt-10 pt-8 border-t border-zinc-100 dark:border-zinc-800">
                    <form action="<?php echo e(route('verification.send')); ?>" method="POST" class="text-center">
                        <?php echo csrf_field(); ?>
                        <p class="text-xs text-zinc-400 font-medium mb-4 italic text-center w-full">Não recebeste nada?</p>
                        <button type="submit" class="text-[10px] font-black uppercase tracking-widest text-emerald-600 hover:text-emerald-500 transition-colors">
                            Reenviar Código de Segurança
                        </button>
                    </form>
                </div>
            </div>

            
            <div class="text-center">
                <form action="<?php echo e(route('logout')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="text-[10px] font-black uppercase tracking-widest text-zinc-400 hover:text-red-500 transition-colors">
                        🚪 Sair da Conta
                    </button>
                </form>
            </div>

        </div>

        
        <p class="mt-12 text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em] opacity-50">
            Finance Pro · Protocolo de Segurança Ativo
        </p>
    </div>
</body>
</html>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/auth/verify-email.blade.php ENDPATH**/ ?>