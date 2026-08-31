<div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOpen): ?>
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/95 backdrop-blur-2xl" x-transition>
            <div class="w-full max-w-3xl bg-zinc-900 border border-white/10 rounded-[3rem] shadow-[0_0_50px_rgba(0,0,0,0.5)] relative overflow-hidden flex flex-col md:flex-row text-left">

                
                <div class="w-full md:w-72 bg-black/20 p-8 border-b md:border-b-0 md:border-r border-white/5 flex flex-col justify-between">
                    <div class="space-y-6">
                        <div class="size-14 bg-brand-600 rounded-2xl flex items-center justify-center shadow-lg shadow-brand-500/20">
                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'shield-check','variant' => 'solid','class' => 'text-white size-8']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shield-check','variant' => 'solid','class' => 'text-white size-8']); ?>
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
                            <h2 class="text-white font-black uppercase tracking-widest text-xs">Acesso Nível 5</h2>
                            <p class="text-zinc-500 text-[10px] font-mono mt-1 uppercase tracking-tighter">Auth_Token: <?php echo e(substr(md5(auth()->id()), 0, 12)); ?></p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = range(1, $totalSteps); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="flex items-center gap-3">
                                <div class="size-2 rounded-full <?php echo e($step >= $i ? 'bg-brand-500 shadow-[0_0_8px_#10b981]' : 'bg-zinc-800'); ?>"></div>
                                <span class="text-[9px] font-black uppercase tracking-widest <?php echo e($step == $i ? 'text-white' : 'text-zinc-600'); ?>">Fase 0<?php echo e($i); ?></span>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>

                
                <div class="flex-1 p-10 relative">
                    <div class="absolute -right-20 -top-20 size-64 bg-brand-500/5 blur-[100px] rounded-full pointer-events-none"></div>

                    <div class="min-h-[350px] flex flex-col">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step == 1): ?>
                            <div class="space-y-6 animate-fade-in">
                                <span class="px-3 py-1 bg-brand-500/10 text-brand-400 text-[8px] font-black rounded-full uppercase tracking-[0.2em]">Sessão Iniciada</span>
                                <h3 class="text-4xl font-black text-white leading-none italic uppercase tracking-tighter">Bem-vindo ao Núcleo de Comando</h3>
                                <p class="text-zinc-400 text-sm leading-relaxed">Estás no controlo total do <strong>Finance Pro</strong>. Este dashboard permite monitorizar o ecossistema, auditar transações e gerir o ciclo de vida de cada utilizador e empresa na plataforma.</p>
                                <div class="grid grid-cols-2 gap-4 pt-4">
                                    <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                                        <p class="text-brand-500 font-black text-xl leading-none">Global</p>
                                        <p class="text-[9px] text-zinc-500 uppercase mt-1">Visão de Dados</p>
                                    </div>
                                    <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                                        <p class="text-brand-500 font-black text-xl leading-none">Security</p>
                                        <p class="text-[9px] text-zinc-500 uppercase mt-1">Audit Trail</p>
                                    </div>
                                </div>
                            </div>
                        <?php elseif($step == 2): ?>
                            <div class="space-y-6 animate-fade-in">
                                <h3 class="text-3xl font-black text-white leading-none uppercase italic tracking-tighter">Gestão de Utilizadores & Suporte</h3>
                                <p class="text-zinc-400 text-sm leading-relaxed">A ferramenta <strong>Impersonate</strong> é o teu recurso de suporte mais poderoso. Permite-te entrar na conta de um utilizador exatamente como ele a vê.</p>

                                <div class="bg-black/40 rounded-2xl p-5 border border-white/5 space-y-4">
                                    <div class="flex items-center justify-between border-b border-white/5 pb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="size-8 bg-zinc-800 rounded-lg flex items-center justify-center text-xs font-bold text-zinc-400">JD</div>
                                            <span class="text-xs font-bold text-white italic">João Duarte (Pro)</span>
                                        </div>
                                        <button class="px-3 py-1.5 bg-brand-600 text-white text-[9px] font-black rounded-lg uppercase shadow-lg pointer-events-none">Entrar na Conta</button>
                                    </div>
                                    <p class="text-[10px] text-zinc-500 italic">Dica: Usa isto apenas com autorização para diagnosticar erros reportados via Ticket.</p>
                                </div>
                            </div>
                        <?php elseif($step == 3): ?>
                            <div class="space-y-6 animate-fade-in">
                                <h3 class="text-3xl font-black text-white leading-none uppercase italic tracking-tighter">Broadcast Global</h3>
                                <p class="text-zinc-400 text-sm leading-relaxed">Com os <strong>Avisos Globais</strong>, podes comunicar com todos os utilizadores instantaneamente através de banners inteligentes no dashboard.</p>

                                <div class="space-y-3">
                                    <div class="p-3 bg-amber-500/20 border border-amber-500/30 rounded-xl flex items-center gap-3">
                                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'exclamation-triangle','class' => 'size-4 text-amber-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'exclamation-triangle','class' => 'size-4 text-amber-500']); ?>
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
                                        <span class="text-[9px] font-black text-amber-200 uppercase tracking-widest">Aviso: Atualização do sistema amanhã.</span>
                                    </div>
                                    <div class="p-3 bg-red-600 text-white rounded-xl flex items-center gap-3">
                                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'no-symbol','class' => 'size-4 text-white']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'no-symbol','class' => 'size-4 text-white']); ?>
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
                                        <span class="text-[9px] font-black uppercase tracking-widest">Erro Crítico: API do Banco em manutenção.</span>
                                    </div>
                                </div>
                            </div>
                        <?php elseif($step == 4): ?>
                            <div class="space-y-6 animate-fade-in">
                                <h3 class="text-3xl font-black text-white leading-none uppercase italic tracking-tighter">Auditoria em Tempo Real</h3>
                                <p class="text-zinc-400 text-sm leading-relaxed">Todos os eventos sensíveis (logins, deleções, upgrades) são registados no **Security Log**. Isto garante transparência e segurança absoluta.</p>

                                <div class="font-mono text-[10px] bg-black p-5 rounded-2xl border border-white/5 space-y-2 max-h-40 overflow-hidden">
                                    <p class="text-zinc-500"><span class="text-emerald-500">12:01:44</span> — <span class="text-white">Admin</span> marked user #22 as <span class="text-red-500">BANNED</span></p>
                                    <p class="text-zinc-500"><span class="text-emerald-500">12:05:12</span> — <span class="text-white">System</span> blocked IP <span class="text-amber-500">192.168.1.1</span></p>
                                    <p class="text-zinc-500"><span class="text-emerald-500">12:10:00</span> — <span class="text-white">User_9</span> changed currency to <span class="text-brand-400">USD</span></p>
                                </div>
                            </div>
                        <?php elseif($step == 5): ?>
                            <div class="space-y-6 animate-fade-in">
                                <h3 class="text-3xl font-black text-white leading-none uppercase italic tracking-tighter">Faturação & Subscrições</h3>
                                <p class="text-zinc-400 text-sm leading-relaxed">Gere os planos de pagamento, ativa cupões de desconto e monitoriza a faturação mensal global da plataforma na aba <strong>Billing</strong>.</p>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-white/5 p-4 rounded-2xl border border-white/10 flex items-center gap-3">
                                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'credit-card','class' => 'text-emerald-500 size-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'credit-card','class' => 'text-emerald-500 size-6']); ?>
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
                                        <div class="flex flex-col">
                                            <span class="text-white font-black text-sm">PRO Plan</span>
                                            <span class="text-[9px] text-zinc-500 uppercase">Ativar Manualmente</span>
                                        </div>
                                    </div>
                                    <div class="bg-white/5 p-4 rounded-2xl border border-white/10 flex items-center gap-3">
                                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'arrow-down-tray','class' => 'text-blue-500 size-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-down-tray','class' => 'text-blue-500 size-6']); ?>
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
                                        <div class="flex flex-col">
                                            <span class="text-white font-black text-sm">Invoices</span>
                                            <span class="text-[9px] text-zinc-500 uppercase">Gerar em PDF</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-auto flex gap-4 pt-10">
                            <button wire:click="finish" class="px-6 h-14 border border-white/10 text-zinc-500 rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] hover:bg-white/5 transition-all">Ignorar</button>
                            <button wire:click="nextStep" class="flex-1 h-14 bg-brand-600 text-white rounded-2xl font-black uppercase text-xs tracking-[0.2em] hover:bg-brand-500 transition-all shadow-2xl shadow-brand-500/20 active:scale-95">
                                <?php echo e($step < $totalSteps ? 'Avançar Protocolo' : 'Assumir Comando 🛡️'); ?>

                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/livewire/admin-onboarding.blade.php ENDPATH**/ ?>