<div
x-data="{
    listening:false,
    open: <?php if ((object) ('isOpen') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('isOpen'->value()); ?>')<?php echo e('isOpen'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('isOpen'); ?>')<?php endif; ?>,
    scrollToBottom() {
        this.$nextTick(() => {
            const el = this.$refs.messagesContainer;
            if (el) el.scrollTo({ top: el.scrollHeight, behavior: 'smooth' });
        });
    },
    startVoice() {
        const rec = new(window.SpeechRecognition || window.webkitSpeechRecognition)();
        rec.lang = 'pt-PT';
        rec.onresult = e => $wire.set('userInput', e.results[0][0].transcript);
        rec.start();
        this.listening = true;
        rec.onend = () => this.listening = false;
    }
}"
x-init="scrollToBottom(); $watch('open', value => value && scrollToBottom())"
x-on:message-sent.window="scrollToBottom()"
class="relative"
>

    
    <button
        wire:click="toggleChat"
        class="fixed bottom-6 right-6 z-[110] size-16 rounded-full bg-emerald-500 text-white shadow-[0_10px_40px_rgba(16,185,129,0.4)] flex items-center justify-center hover:scale-110 active:scale-95 transition-all group border-4 border-white dark:border-zinc-900"
    >
        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'sparkles','xShow' => '!open','class' => 'size-7 text-white group-hover:rotate-12 transition-transform']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'sparkles','x-show' => '!open','class' => 'size-7 text-white group-hover:rotate-12 transition-transform']); ?>
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
        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'x-mark','xShow' => 'open','xCloak' => true,'class' => 'size-7 text-white']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'x-mark','x-show' => 'open','x-cloak' => true,'class' => 'size-7 text-white']); ?>
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

        <div x-show="!open" class="absolute top-0 right-0 size-4 bg-emerald-400 border-2 border-white dark:border-zinc-900 rounded-full">
            <span class="absolute inset-0 rounded-full bg-emerald-400 animate-ping"></span>
        </div>
    </button>

    
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-y-10 scale-95 blur-sm"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100 blur-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100 blur-0"
        x-transition:leave-end="opacity-0 translate-y-10 scale-95 blur-sm"
        class="fixed bottom-24 right-6 z-[110]
               w-[480px] max-w-[calc(100vw-2rem)]
               h-[750px] max-h-[calc(100vh-6rem)]
               bg-white dark:bg-zinc-950 rounded-[3rem]
               border border-zinc-200 dark:border-zinc-800 shadow-[0_30px_90px_rgba(0,0,0,0.4)]
               flex flex-col overflow-hidden text-left"
    >

        
        <div class="p-7 bg-zinc-950 text-white flex items-center justify-between flex-shrink-0 border-b border-white/5 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 size-24 bg-brand-500/10 blur-2xl rounded-full"></div>

            <div class="flex items-center gap-4 relative z-10 text-left">
                <div class="size-11 rounded-2xl bg-brand-600 flex items-center justify-center shadow-lg shadow-brand-500/20">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'sparkles','class' => 'size-5 text-white']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'sparkles','class' => 'size-5 text-white']); ?>
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
                    <h3 class="text-sm font-black uppercase tracking-widest leading-none italic">Finance Pilot</h3>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="size-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                        <p class="text-[9px] text-zinc-400 font-black uppercase tracking-widest">IA Estratégica Ativa</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-1 relative z-10">
                <button wire:click="handleAction('menu:root')" title="Menu principal"
                        class="text-zinc-500 hover:text-white transition-colors p-2 rounded-xl hover:bg-white/5">
                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'squares-2x2','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'squares-2x2','class' => 'size-4']); ?>
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
                <button @click="open = false"
                        class="text-zinc-500 hover:text-white transition-colors p-2 rounded-xl hover:bg-white/5">
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
        </div>

        
        <div
            x-ref="messagesContainer"
            x-on:input="scrollToBottom()"
            class="flex-1 overflow-y-auto p-8 space-y-8 custom-scrollbar
                   bg-zinc-50/30 dark:bg-zinc-900/10 text-left"
            style="background-image: radial-gradient(circle at 2px 2px, rgba(0,0,0,0.02) 1px, transparent 0); background-size: 32px 32px;"
        >
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'msg-'.e($msg['id']).''; ?>wire:key="msg-<?php echo e($msg['id']); ?>"
                     class="flex flex-col <?php echo e($msg['role'] === 'user' ? 'items-end' : 'items-start'); ?> animate-in fade-in slide-in-from-bottom-2 duration-300">

                    <?php
                        $rendered = e($msg['content']);
                        $rendered = preg_replace('/\*\*(.*?)\*\*/s', '<strong class="font-black">$1</strong>', $rendered);
                        $rendered = nl2br($rendered);
                    ?>

                    <div class="max-w-[85%] p-5 rounded-[2rem] text-sm leading-relaxed shadow-sm text-left
                        <?php echo e($msg['role'] === 'user'
                            ? 'bg-emerald-600 text-white rounded-tr-none'
                            : 'bg-white dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 rounded-tl-none font-medium'); ?>">
                        <?php echo $rendered; ?>

                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($msg['options'])): ?>
                        <div class="flex flex-wrap gap-2 mt-4 max-w-full">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $msg['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <button
                                    wire:click="handleAction('<?php echo e($opt['action']); ?>')"
                                    class="px-5 py-2.5 bg-white dark:bg-zinc-900 border-2 border-zinc-100 dark:border-zinc-800
                                           rounded-2xl text-[10px] font-black uppercase tracking-widest
                                           hover:border-emerald-500 hover:text-emerald-600 transition-all shadow-sm active:scale-95"
                                >
                                    <?php echo e($opt['label']); ?>

                                </button>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isTyping): ?>
                <div class="flex items-start animate-in fade-in duration-300">
                    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700
                                rounded-3xl rounded-tl-none px-6 py-4 flex items-center gap-2 shadow-sm">
                        <div class="flex gap-1.5">
                            <span class="size-1.5 bg-zinc-400 dark:bg-zinc-500 rounded-full animate-bounce"></span>
                            <span class="size-1.5 bg-zinc-400 dark:bg-zinc-500 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                            <span class="size-1.5 bg-zinc-400 dark:bg-zinc-500 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400 ml-2">A analisar...</span>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="p-6 bg-white dark:bg-zinc-950 border-t dark:border-zinc-800 flex-shrink-0">

            
            <div class="relative mb-5 pb-1">
                <div x-ref="hints" class="flex gap-2 overflow-x-auto no-scrollbar whitespace-nowrap px-8">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                        'Resumo de hoje',
                        'Quanto gastei?',
                        'Minhas metas',
                        'Dicas de poupança',
                        'Subscrições',
                        'Investimentos',
                        'Metas anuais',
                        'Relatório semanal',
                        'Alertas',
                        'Tendências'
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hint): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <button
                           x-on:click="$wire.set('userInput', '<?php echo e($hint); ?>'); $wire.call('sendMessage')"

                            class="inline-block px-4 py-2 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 text-[10px] font-black uppercase text-zinc-500 hover:text-emerald-600 hover:border-emerald-500 transition-all shadow-sm active:scale-95"
                        >
                            <?php echo e($hint); ?>

                        </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>

                
                <button
                    x-on:click="$refs.hints.scrollBy({ left: -150, behavior: 'smooth' })"
                    class="absolute left-0 top-1/2 -translate-y-1/2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm rounded-full size-7 flex items-center justify-center hover:scale-110 transition"
                >
                    <svg class="size-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button
                    x-on:click="$refs.hints.scrollBy({ left: 150, behavior: 'smooth' })"
                    class="absolute right-0 top-1/2 -translate-y-1/2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm rounded-full size-7 flex items-center justify-center hover:scale-110 transition"
                >
                    <svg class="size-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent class="relative group">
    <div class="absolute inset-0 bg-emerald-500/5 blur-xl rounded-[2rem] opacity-0 group-focus-within:opacity-100 transition-opacity"></div>

    <div class="relative flex items-center bg-zinc-100 dark:bg-zinc-900 border-2 border-transparent focus-within:border-emerald-500/30 rounded-[2rem] p-2 transition-all shadow-inner">

        <input
    wire:model.defer="userInput"
    wire:keydown.enter.prevent="sendMessage"
    wire:loading.attr="disabled"
    wire:target="sendMessage"
    placeholder="Pergunta ao Pilot..."
    autocomplete="off"
    class="flex-1 bg-transparent border-none py-3 pl-4 pr-2
           text-sm font-medium focus:ring-0 text-zinc-800 dark:text-white
           placeholder:text-zinc-400 dark:placeholder:text-zinc-600"
/>

        
        <button
            type="button"
            wire:click="sendMessage"
            wire:loading.attr="disabled"
            wire:target="sendMessage"
            class="size-12 flex items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-lg shadow-emerald-500/20 hover:bg-emerald-500 hover:scale-105 active:scale-95 transition-all disabled:opacity-40"
        >
            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'paper-airplane','variant' => 'solid','class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'paper-airplane','variant' => 'solid','class' => 'size-5']); ?>
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

        
        <button
            type="button"
            @click="startVoice()"
            :class="listening ? 'bg-emerald-500 text-white' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300'"
            class="size-12 flex items-center justify-center rounded-2xl hover:scale-105 transition"
        >
            🎤
        </button>

    </div>
</form>

            <div class="mt-6 flex items-center justify-center gap-3 opacity-30">
                <div class="h-px bg-zinc-300 dark:bg-zinc-800 flex-1"></div>
                <p class="text-[8px] text-zinc-400 font-black uppercase tracking-[0.4em]">
                    Neural Engine v2.4
                </p>
                <div class="h-px bg-zinc-300 dark:bg-zinc-800 flex-1"></div>
            </div>
        </div>
    </div>

    
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.08); border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</div>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/livewire/finance-bot.blade.php ENDPATH**/ ?>