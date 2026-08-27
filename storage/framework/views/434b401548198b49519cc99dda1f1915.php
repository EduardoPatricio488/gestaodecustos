<div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOpen): ?>
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/95 backdrop-blur-2xl" x-transition>
            <div class="w-full max-w-3xl bg-zinc-900 border border-amber-500/20 rounded-[3rem] shadow-[0_0_50px_rgba(245,158,11,0.15)] relative overflow-hidden flex flex-col md:flex-row text-left">

                
                <div class="w-full md:w-72 bg-amber-500/5 p-8 border-b md:border-b-0 md:border-r border-white/5 flex flex-col justify-between">
                    <div class="space-y-6">
                        <div class="size-14 bg-amber-500 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/20">
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'shield-check','variant' => 'solid','class' => 'text-zinc-900 size-8']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shield-check','variant' => 'solid','class' => 'text-zinc-900 size-8']); ?>
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
                            <h2 class="text-amber-500 font-black uppercase tracking-widest text-xs">Acesso Sentinela</h2>
                            <p class="text-zinc-500 text-[10px] font-mono mt-1 uppercase tracking-tighter">Cargo: Moderador</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = range(1, $totalSteps); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="flex items-center gap-3">
                                <div class="size-2 rounded-full <?php echo e($step >= $i ? 'bg-amber-500 shadow-[0_0_8px_#f59e0b]' : 'bg-zinc-800'); ?>"></div>
                                <span class="text-[9px] font-black uppercase tracking-widest <?php echo e($step == $i ? 'text-white' : 'text-zinc-600'); ?>">Protocolo 0<?php echo e($i); ?></span>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>

                
                <div class="flex-1 p-10 relative">
                    <div class="absolute -right-20 -top-20 size-64 bg-amber-500/5 blur-[100px] rounded-full pointer-events-none"></div>

                    <div class="min-h-[350px] flex flex-col">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step == 1): ?>
                            <div class="space-y-6 animate-fade-in">
                                <span class="px-3 py-1 bg-amber-500/10 text-amber-500 text-[8px] font-black rounded-full uppercase tracking-[0.2em]">Sessão de Supervisão</span>
                                <h3 class="text-4xl font-black text-white leading-none italic uppercase tracking-tighter">Guardião da Comunidade</h3>
                                <p class="text-zinc-400 text-sm leading-relaxed">Bem-vindo à tua consola. O teu papel é garantir que o ecossistema <strong>Finance Pro</strong> permanece seguro, saudável e funcional para todos os utilizadores.</p>
                                <div class="p-4 bg-amber-500/5 rounded-2xl border border-amber-500/10 italic text-xs text-amber-200">
                                    "Manter a ordem é o primeiro passo para o crescimento global."
                                </div>
                            </div>
                        <?php elseif($step == 2): ?>
                            <div class="space-y-6 animate-fade-in">
                                <h3 class="text-3xl font-black text-white leading-none uppercase italic tracking-tighter">Supervisão de Utilizadores</h3>
                                <p class="text-zinc-400 text-sm leading-relaxed">Podes monitorizar o estado de cada conta, verificar atividades suspeitas e intervir quando as regras da comunidade forem violadas.</p>

                                <div class="bg-black/40 rounded-2xl p-5 border border-white/5 space-y-3">
                                    <div class="flex items-center justify-between border-b border-white/5 pb-3">
                                        <span class="text-[10px] font-black text-zinc-500 uppercase">Ações Rápidas:</span>
                                        <div class="flex gap-2">
                                            <span class="px-2 py-1 bg-amber-500/10 text-amber-500 text-[8px] font-black rounded uppercase">Advertir</span>
                                            <span class="px-2 py-1 bg-red-500/10 text-red-500 text-[8px] font-black rounded uppercase">Suspender</span>
                                        </div>
                                    </div>
                                    <p class="text-[10px] text-zinc-500 italic">Nota: Tens autoridade para moderar perfis e interações no Finance Connect.</p>
                                </div>
                            </div>
                        <?php elseif($step == 3): ?>
                            <div class="space-y-6 animate-fade-in">
                                <h3 class="text-3xl font-black text-white leading-none uppercase italic tracking-tighter">Central de Suporte</h3>
                                <p class="text-zinc-400 text-sm leading-relaxed">És o ponto de contacto direto. Gere os <strong>Tickets de Suporte</strong>, esclarece dúvidas financeiras e ajuda os utilizadores com dificuldades técnicas.</p>

                                <div class="p-4 bg-white/5 rounded-2xl border border-white/10 flex items-center gap-4">
                                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'chat-bubble-left-right','class' => 'text-amber-500 size-8']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chat-bubble-left-right','class' => 'text-amber-500 size-8']); ?>
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
                                    <div>
                                        <p class="text-white font-black text-sm uppercase leading-none">Atendimento Ativo</p>
                                        <p class="text-[9px] text-zinc-500 uppercase mt-1">Tempo médio alvo: < 4 horas</p>
                                    </div>
                                </div>
                            </div>
                        <?php elseif($step == 4): ?>
                            <div class="space-y-6 animate-fade-in">
                                <h3 class="text-3xl font-black text-white leading-none uppercase italic tracking-tighter">Comunicação de Serviço</h3>
                                <p class="text-zinc-400 text-sm leading-relaxed">Podes emitir notificações e dicas. Usa isto para educar a comunidade com boas práticas financeiras ou alertar sobre novidades.</p>

                                <div class="w-full bg-amber-500 p-4 rounded-xl flex items-center gap-4 shadow-lg shadow-amber-500/20">
                                    <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'megaphone','class' => 'size-5 text-zinc-900']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'megaphone','class' => 'size-5 text-zinc-900']); ?>
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
                                    <span class="text-[10px] font-black text-zinc-950 uppercase tracking-widest leading-tight">Dica: O segredo da riqueza está na consistência, não no valor.</span>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-auto flex gap-4 pt-10">
                            <button wire:click="finish" class="px-6 h-14 border border-white/10 text-zinc-500 rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] hover:bg-white/5 transition-all">Sair</button>
                            <button wire:click="nextStep" class="flex-1 h-14 bg-amber-500 text-zinc-950 rounded-2xl font-black uppercase text-xs tracking-[0.2em] hover:bg-amber-400 transition-all shadow-2xl shadow-amber-500/20 active:scale-95">
                                <?php echo e($step < $totalSteps ? 'Próximo Protocolo' : 'Ativar Vigilância 🛡️'); ?>

                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH C:\Projetos\gestao-de-custos\resources\views\livewire\moderator-onboarding.blade.php ENDPATH**/ ?>