<div
    x-data="{
        privacy: false,
        audioPlaying: false,
        audioSrc: 'https://www.soundjay.com/misc/sounds/bell-ringing-05.mp3',
        breakGlassOpen: false,
        unlockHolding: false,
        unlockDone: false,
        seconds: <?php echo e($sessionSeconds); ?>,
        xp: <?php echo e($xpEarned); ?>,

        init() {
            // Listener da tecla P para privacidade
            window.addEventListener('keydown', (e) => {
                if (e.key === 'p' || e.key === 'P') {
                    this.privacy = !this.privacy;
                }
            });

            // Atualizar o timer a cada segundo no frontend
            setInterval(() => { this.seconds++; }, 1000);
        },

        formatTime(s) {
            const h = Math.floor(s / 3600);
            const m = Math.floor((s % 3600) / 60);
            const sec = s % 60;
            return [
                h > 0 ? String(h).padStart(2,'0') + 'h' : null,
                String(m).padStart(2,'0') + 'm',
                String(sec).padStart(2,'0') + 's'
            ].filter(Boolean).join(' ');
        },

        xpProgress() {
            return ((this.seconds % 600) / 600) * 100;
        },

        theme: localStorage.getItem('lockin_theme') || (document.documentElement.getAttribute('data-lockin-theme') || 'dark'),

        toggleTheme() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('lockin_theme', this.theme);
            document.documentElement.setAttribute('data-lockin-theme', this.theme);
            document.documentElement.classList.toggle('dark', this.theme === 'dark');
        },

        startHold() {
            this.unlockHolding = true;
        },
        stopHold() {
            if (!this.unlockDone) this.unlockHolding = false;
        }
    }"
    :class="{ 'privacy-active': privacy }"
    class="relative min-h-screen w-full flex flex-col overflow-hidden select-none"
    wire:poll.1s="tick"
>

    
    
    
    <div class="anim-1 flex items-center justify-between px-6 py-3 border-b" style="border-color: var(--li-sep);">

        
        <div class="flex items-center gap-3">
            <div class="relative flex items-center justify-center w-8 h-8">
                <div class="pulse-ring relative w-3 h-3 rounded-full bg-emerald-400"></div>
            </div>
            <div>
                <span class="text-[10px] font-black uppercase tracking-[0.35em] text-emerald-400">Lock In Mode</span>
                <div class="text-[9px] text-zinc-600 tracking-widest uppercase">Foco Máximo Ativo</div>
            </div>
        </div>

        
        <div class="flex items-center gap-6">
            
            <button
                @click="
                    audioPlaying = !audioPlaying;
                    if (audioPlaying) {
                        $refs.audio.play();
                    } else {
                        $refs.audio.pause();
                    }
                "
                class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-white/5 hover:border-white/10 glass transition-all text-zinc-400 hover:text-white"
                title="Zen Focus Player"
            >
                <svg x-show="!audioPlaying" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                <svg x-show="audioPlaying" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                <span class="text-[9px] font-bold uppercase tracking-widest" x-text="audioPlaying ? 'Zen ON' : 'Zen'"></span>
            </button>
            <audio x-ref="audio" loop>
                <source src="https://assets.mixkit.co/music/preview/mixkit-tech-house-vibes-130.mp3" type="audio/mpeg">
            </audio>

            
            <div class="text-center">
                <div class="text-[10px] text-zinc-600 uppercase tracking-widest mb-0.5">Tempo de Foco</div>
                <div class="tabular text-sm font-black text-white" x-text="formatTime(seconds)">00m 00s</div>
            </div>

            
            <div class="text-center hidden sm:block">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[9px] text-zinc-600 uppercase tracking-widest">Próximo XP</span>
                    <span class="text-[10px] font-black text-emerald-400" x-text="'+' + (Math.floor(seconds/600) * 20 + 20) + ' XP'"></span>
                </div>
                <div class="w-24 h-1 bg-zinc-900 rounded-full overflow-hidden border border-white/5">
                    <div
                        class="h-full bg-gradient-to-r from-emerald-500 to-emerald-300 rounded-full transition-all duration-1000"
                        :style="'width:' + xpProgress() + '%'"
                    ></div>
                </div>
            </div>

            
            <button
                @click="toggleTheme()"
                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border glass transition-all"
                :class="theme === 'dark' ? 'text-zinc-400 border-white/5 hover:border-white/10 hover:text-white' : 'text-amber-500 border-amber-400/20 hover:border-amber-400/40'"
                title="Alternar Tema Claro/Escuro"
            >
                
                <svg x-show="theme === 'dark'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                </svg>
                
                <svg x-show="theme === 'light'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>

            
            <button
                @click="privacy = !privacy"
                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-white/5 hover:border-white/10 glass transition-all"
                :class="privacy ? 'text-amber-400 border-amber-500/20' : 'text-zinc-500 hover:text-white'"
                title="Modo Privacidade (tecla P)"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!privacy" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path x-show="!privacy" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    <path x-show="privacy" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                </svg>
                <span class="text-[9px] font-bold uppercase tracking-widest" x-text="privacy ? 'P' : 'P'"></span>
            </button>

            
            <div class="relative overflow-hidden">
                <button
                    @mousedown="startHold()"
                    @mouseup="stopHold()"
                    @mouseleave="stopHold()"
                    @touchstart.prevent="startHold()"
                    @touchend="stopHold()"
                    wire:click="unlock"
                    class="relative overflow-hidden flex items-center gap-2 px-4 py-2 rounded-xl border border-red-500/20 glass text-red-400 hover:text-red-300 hover:border-red-500/40 transition-all text-[10px] font-black uppercase tracking-widest"
                    :class="{ 'hold-active': unlockHolding }"
                    title="Segura 3 segundos para sair"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                    </svg>
                    Unlock
                    
                    <span class="hold-bar absolute bottom-0 left-0 h-0.5 bg-red-400/60 rounded-full" style="width:0%"></span>
                </button>
            </div>
        </div>
    </div>

    
    
    
    <div class="flex-1 overflow-y-auto px-4 py-4 lg:px-6 lg:py-5">
        <div class="grid grid-cols-12 gap-4 max-w-[1600px] mx-auto">

            
            <div class="col-span-12 lg:col-span-4 flex flex-col gap-4">

                
                <div class="anim-2 glass rounded-2xl border p-4 glow-blue">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-5 bg-blue-500 rounded-full"></div>
                            <span class="text-[9px] font-black uppercase tracking-[0.3em] text-blue-400">Painel Pessoal</span>
                        </div>
                        <span class="text-[8px] text-zinc-600 uppercase tracking-widest"><?php echo e(now()->format('M Y')); ?></span>
                    </div>

                    
                    <div class="mb-4">
                        <div class="text-[9px] text-zinc-500 uppercase tracking-widest mb-1">Saldo Consolidado</div>
                        <div class="tabular text-3xl font-black privacy-val <?php echo e($personalBalance >= 0 ? 'text-white num-highlight' : 'text-red-400'); ?>">
                            <?php echo e(number_format($personalBalance, 2, ',', '.')); ?>€
                        </div>
                    </div>

                    
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-emerald-500/5 border border-emerald-500/15 rounded-xl p-3">
                            <div class="text-[8px] text-zinc-500 uppercase tracking-widest mb-1">Receitas <?php echo e(now()->format('M')); ?></div>
                            <div class="tabular text-base font-black text-emerald-400 privacy-val">+<?php echo e(number_format($personalIncomeMonth, 2, ',', '.')); ?>€</div>
                        </div>
                        <div class="bg-orange-500/5 border border-orange-500/15 rounded-xl p-3">
                            <div class="text-[8px] text-zinc-500 uppercase tracking-widest mb-1">Gastos <?php echo e(now()->format('M')); ?></div>
                            <div class="tabular text-base font-black text-orange-400 privacy-val">-<?php echo e(number_format($personalExpenseMonth, 2, ',', '.')); ?>€</div>
                        </div>
                    </div>

                    
                    <div class="mt-3 pt-3 border-t border-white/5 flex items-center justify-between">
                        <span class="text-[9px] text-zinc-500 uppercase tracking-widest">Saldo do Mês</span>
                        <span class="tabular text-sm font-black privacy-val <?php echo e($personalNetMonth >= 0 ? 'text-emerald-400' : 'text-red-400'); ?>">
                            <?php echo e($personalNetMonth >= 0 ? '+' : ''); ?><?php echo e(number_format($personalNetMonth, 2, ',', '.')); ?>€
                        </span>
                    </div>
                </div>

                
                <div class="anim-2 glass rounded-2xl border border-white/5 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-5 bg-emerald-500 rounded-full"></div>
                            <span class="text-[9px] font-black uppercase tracking-[0.3em] text-emerald-400">Meta de Poupança</span>
                        </div>
                        <span class="text-[10px] font-black text-white"><?php echo e($goalProgress); ?>%</span>
                    </div>

                    <div class="text-sm font-bold text-zinc-300 mb-3 truncate"><?php echo e($goalName); ?></div>

                    
                    <div class="w-full h-2 bg-zinc-900 rounded-full overflow-hidden border border-white/5 mb-2">
                        <div
                            class="h-full rounded-full transition-all duration-700 <?php echo e($goalProgress >= 80 ? 'bg-gradient-to-r from-emerald-500 to-emerald-300' : ($goalProgress >= 40 ? 'bg-gradient-to-r from-amber-500 to-amber-300' : 'bg-gradient-to-r from-red-600 to-red-400')); ?>"
                            style="width: <?php echo e($goalProgress); ?>%"
                        ></div>
                    </div>

                    <div class="flex justify-between text-[9px] text-zinc-500">
                        <span class="privacy-val"><?php echo e(number_format($goalCurrent, 2, ',', '.')); ?>€ atual</span>
                        <span class="privacy-val">Meta: <?php echo e(number_format($goalTarget, 2, ',', '.')); ?>€</span>
                    </div>
                </div>

                
                <div class="anim-3 glass rounded-2xl border border-white/5 p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-1.5 h-5 bg-orange-500 rounded-full"></div>
                        <span class="text-[9px] font-black uppercase tracking-[0.3em] text-orange-400">Próximas Faturas</span>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($upcomingBills->isNotEmpty()): ?>
                        <div class="space-y-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $upcomingBills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="flex items-center justify-between py-2 border-b border-white/5 last:border-0">
                                    <div>
                                        <div class="text-xs font-bold text-zinc-200"><?php echo e($bill->name); ?></div>
                                        <div class="text-[9px] text-zinc-600 uppercase">
                                            Dia <?php echo e($bill->billing_day ?? '—'); ?> · <?php echo e($bill->cycle ?? 'mensal'); ?>

                                        </div>
                                    </div>
                                    <span class="tabular text-sm font-black text-orange-400 privacy-val">
                                        <?php echo e(number_format($bill->amount, 2, ',', '.')); ?>€
                                    </span>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <div class="text-2xl mb-1">✅</div>
                            <div class="text-[10px] text-zinc-600">Sem assinaturas registadas</div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="col-span-12 lg:col-span-4 flex flex-col gap-4">

                
                <div class="anim-3 glass rounded-2xl border p-6 text-center relative overflow-hidden
                    <?php echo e($runwayMonths >= 6 ? 'glow-green' : ($runwayMonths >= 3 ? 'glow-orange' : 'glow-red')); ?>"
                >
                    
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-5">
                        <div class="w-64 h-64 rounded-full border-[40px]
                            <?php echo e($runwayMonths >= 6 ? 'border-emerald-500' : ($runwayMonths >= 3 ? 'border-orange-500' : 'border-red-500')); ?>">
                        </div>
                    </div>

                    <div class="relative z-10">
                        <div class="text-[8px] font-black uppercase tracking-[0.4em] text-zinc-500 mb-4">
                            ⚡ Sobrevivência Financeira
                        </div>

                        <div class="tabular font-black leading-none mb-2 privacy-val
                            <?php echo e($runwayMonths >= 6 ? 'runway-safe' : ($runwayMonths >= 3 ? 'runway-warning' : 'runway-critical')); ?>"
                            style="font-size: clamp(2.5rem, 5vw, 4rem);"
                        >
                            <?php echo e($runwayMonths); ?>

                        </div>
                        <div class="text-sm font-bold text-zinc-400 mb-1">
                            meses
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($runwayDays > 0): ?>
                                <span class="text-zinc-600">e <?php echo e($runwayDays); ?> dias</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="text-[9px] text-zinc-600 mt-2 mb-5">
                            Saldo total / Média de gastos mensais
                        </div>

                        
                        <div class="flex items-center justify-center gap-1 mb-4">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < 12; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="h-4 w-1.5 rounded-full transition-all duration-500
                                    <?php echo e($i < $runwayMonths
                                        ? ($runwayMonths >= 6 ? 'bg-emerald-400' : ($runwayMonths >= 3 ? 'bg-orange-400' : 'bg-red-400'))
                                        : 'bg-zinc-800'); ?>"
                                    style="transform: scaleY(<?php echo e(0.5 + ($i / 12) * 0.8); ?>)"
                                ></div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-left">
                            <div class="bg-white/3 rounded-xl p-3 border border-white/5">
                                <div class="text-[8px] text-zinc-600 uppercase tracking-widest mb-1">Saldo Total</div>
                                <div class="tabular text-sm font-black text-white privacy-val"><?php echo e(number_format($totalBalance, 2, ',', '.')); ?>€</div>
                            </div>
                            <div class="bg-white/3 rounded-xl p-3 border border-white/5">
                                <div class="text-[8px] text-zinc-600 uppercase tracking-widest mb-1">Média Mensal</div>
                                <div class="tabular text-sm font-black text-white privacy-val"><?php echo e(number_format($avgMonthlySpend, 2, ',', '.')); ?>€</div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="anim-4 glass rounded-2xl border border-white/5 p-4">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-5 bg-red-500 rounded-full"></div>
                            <span class="text-[9px] font-black uppercase tracking-[0.3em] text-red-400">Burn Rate</span>
                        </div>
                        <span class="text-[8px] text-zinc-600 uppercase">Consumo Diário</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <div class="text-[8px] text-zinc-600 uppercase tracking-widest mb-1">Histórico (Média)</div>
                            <div class="tabular text-xl font-black text-zinc-300 privacy-val">
                                <?php echo e(number_format($burnRateDaily, 2, ',', '.')); ?>€
                            </div>
                            <div class="text-[8px] text-zinc-700">/dia</div>
                        </div>
                        <div class="text-center">
                            <div class="text-[8px] text-zinc-600 uppercase tracking-widest mb-1">Este Mês (Real)</div>
                            <div class="tabular text-xl font-black privacy-val
                                <?php echo e($burnRateActual > $burnRateDaily * 1.2 ? 'text-red-400' : ($burnRateActual > $burnRateDaily ? 'text-orange-400' : 'text-emerald-400')); ?>">
                                <?php echo e(number_format($burnRateActual, 2, ',', '.')); ?>€
                            </div>
                            <div class="text-[8px] text-zinc-700">/dia</div>
                        </div>
                    </div>

                    <?php
                        $burnDiff = $burnRateActual > 0 && $burnRateDaily > 0
                            ? round((($burnRateActual - $burnRateDaily) / $burnRateDaily) * 100)
                            : 0;
                    ?>
                    <div class="mt-3 pt-3 border-t border-white/5 flex items-center justify-center gap-2">
                        <span class="text-[9px] <?php echo e($burnDiff > 0 ? 'text-red-400' : 'text-emerald-400'); ?>">
                            <?php echo e($burnDiff > 0 ? '▲' : '▼'); ?> <?php echo e(abs($burnDiff)); ?>%
                        </span>
                        <span class="text-[9px] text-zinc-600">vs histórico</span>
                    </div>
                </div>

                
                <div class="anim-5 glass rounded-2xl border border-emerald-500/15 p-4 bg-emerald-500/3">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-5 h-5 rounded-full bg-emerald-500/20 flex items-center justify-center">
                            <svg class="w-3 h-3 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <span class="text-[9px] font-black uppercase tracking-[0.3em] text-emerald-400">CFO IA · Análise do Dia</span>
                    </div>

                    <p class="text-xs font-bold text-white leading-relaxed mb-2">
                        🔴 <?php echo e($aiInsight['risk']); ?>

                    </p>
                    <p class="text-[11px] text-zinc-400 leading-relaxed">
                        💡 <?php echo e($aiInsight['suggest']); ?>

                    </p>
                </div>
            </div>

            
            <div class="col-span-12 lg:col-span-4 flex flex-col gap-4">

                
                <div class="anim-2 glass rounded-2xl border p-4 <?php echo e($hasBusinessWs ? 'glow-green' : 'border-white/5'); ?>">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-5 bg-emerald-500 rounded-full"></div>
                            <span class="text-[9px] font-black uppercase tracking-[0.3em] text-emerald-400">Painel Business</span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$hasBusinessWs): ?>
                            <span class="text-[8px] text-zinc-600 uppercase tracking-widest">Sem empresa</span>
                        <?php else: ?>
                            <span class="text-[8px] text-zinc-600 uppercase tracking-widest"><?php echo e(now()->format('M Y')); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasBusinessWs): ?>
                        
                        <div class="mb-4">
                            <div class="text-[9px] text-zinc-500 uppercase tracking-widest mb-1">Resultado P&L do Mês</div>
                            <div class="tabular text-3xl font-black privacy-val <?php echo e($pnl >= 0 ? 'text-emerald-400' : 'text-red-400'); ?>">
                                <?php echo e($pnl >= 0 ? '+' : ''); ?><?php echo e(number_format($pnl, 2, ',', '.')); ?>€
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-emerald-500/5 border border-emerald-500/15 rounded-xl p-3">
                                <div class="text-[8px] text-zinc-500 uppercase tracking-widest mb-1">Receitas</div>
                                <div class="tabular text-base font-black text-emerald-400 privacy-val"><?php echo e(number_format($monthlyRevenue, 2, ',', '.')); ?>€</div>
                            </div>
                            <div class="bg-red-500/5 border border-red-500/15 rounded-xl p-3">
                                <div class="text-[8px] text-zinc-500 uppercase tracking-widest mb-1">Despesas</div>
                                <div class="tabular text-base font-black text-red-400 privacy-val"><?php echo e(number_format($monthlyExpenses, 2, ',', '.')); ?>€</div>
                            </div>
                        </div>

                        <div class="mt-3 pt-3 border-t border-white/5 flex items-center justify-between">
                            <span class="text-[9px] text-zinc-500 uppercase tracking-widest">Cash Flow</span>
                            <span class="tabular text-sm font-black privacy-val <?php echo e($cashFlow >= 0 ? 'text-emerald-400' : 'text-red-400'); ?>">
                                <?php echo e($cashFlow >= 0 ? '+' : ''); ?><?php echo e(number_format($cashFlow, 2, ',', '.')); ?>€
                            </span>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <div class="text-3xl mb-2">🏢</div>
                            <div class="text-[10px] text-zinc-600 mb-3">Nenhuma empresa ativa</div>
                            <a href="<?php echo e(route('hub.business.gateway')); ?>" wire:navigate
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[9px] font-black uppercase tracking-widest hover:bg-emerald-500/20 transition-all">
                                Criar Empresa
                            </a>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasBusinessWs): ?>
                <div class="anim-3 glass rounded-2xl border border-white/5 p-4 <?php echo e($overdueAR > 0 ? 'glow-orange' : ''); ?>">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-5 <?php echo e($overdueAR > 0 ? 'bg-orange-500' : 'bg-zinc-600'); ?> rounded-full"></div>
                            <span class="text-[9px] font-black uppercase tracking-[0.3em] <?php echo e($overdueAR > 0 ? 'text-orange-400' : 'text-zinc-500'); ?>">
                                Contas a Receber
                            </span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($overdueAR > 0): ?>
                            <span class="text-[8px] font-black text-red-400 bg-red-500/10 px-2 py-0.5 rounded-full animate-pulse">VENCIDO</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <div class="text-[8px] text-zinc-600 uppercase tracking-widest mb-1">Total Pendente</div>
                            <div class="tabular text-xl font-black privacy-val <?php echo e($accountsReceivable > 0 ? 'text-white' : 'text-zinc-600'); ?>">
                                <?php echo e(number_format($accountsReceivable, 2, ',', '.')); ?>€
                            </div>
                        </div>
                        <div>
                            <div class="text-[8px] text-zinc-600 uppercase tracking-widest mb-1">Em Atraso</div>
                            <div class="tabular text-xl font-black privacy-val <?php echo e($overdueAR > 0 ? 'text-orange-400' : 'text-zinc-600'); ?>">
                                <?php echo e(number_format($overdueAR, 2, ',', '.')); ?>€
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <div class="anim-4 glass rounded-2xl border border-red-500/15 p-4 bg-red-500/3">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-lg">🚨</span>
                        <span class="text-[9px] font-black uppercase tracking-[0.3em] text-red-400">Break Glass — Ações de Emergência</span>
                    </div>

                    <div class="space-y-2">
                        <button
                            wire:click="breakGlassAction('subscriptions')"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border border-red-500/15 hover:border-red-500/30 hover:bg-red-500/5 transition-all text-left group"
                        >
                            <span class="text-base">✂️</span>
                            <div>
                                <div class="text-[10px] font-black text-zinc-300 group-hover:text-red-300 transition-colors">Cortar Assinaturas</div>
                                <div class="text-[8px] text-zinc-600">Audita e cancela serviços desnecessários</div>
                            </div>
                        </button>

                        <button
                            wire:click="breakGlassAction('debts')"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border border-orange-500/15 hover:border-orange-500/30 hover:bg-orange-500/5 transition-all text-left group"
                        >
                            <span class="text-base">💸</span>
                            <div>
                                <div class="text-[10px] font-black text-zinc-300 group-hover:text-orange-300 transition-colors">Cobrar Dívidas</div>
                                <div class="text-[8px] text-zinc-600">Vai às dívidas e age agora</div>
                            </div>
                        </button>

                        <button
                            wire:click="breakGlassAction('reserve')"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border border-blue-500/15 hover:border-blue-500/30 hover:bg-blue-500/5 transition-all text-left group"
                        >
                            <span class="text-base">🏦</span>
                            <div>
                                <div class="text-[10px] font-black text-zinc-300 group-hover:text-blue-300 transition-colors">Mover para Reserva</div>
                                <div class="text-[8px] text-zinc-600">Transfere para fundo de emergência</div>
                            </div>
                        </button>
                    </div>
                </div>

                
                <div class="anim-5 glass rounded-2xl border border-white/5 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-base">🎯</span>
                            <span class="text-[9px] font-black uppercase tracking-[0.3em] text-zinc-400">Sessão de Foco</span>
                        </div>
                        <span class="text-[8px] text-zinc-600 uppercase tracking-widest">+20 XP / 10 min</span>
                    </div>

                    <div class="flex items-end gap-4">
                        <div>
                            <div class="text-[8px] text-zinc-600 uppercase tracking-widest mb-1">XP Ganho Hoje</div>
                            <div class="tabular text-2xl font-black text-emerald-400" x-text="'+' + (<?php echo e($xpEarned); ?> + Math.floor(seconds/600)*20 - <?php echo e($lastXpMilestone * 20); ?>)">
                                +<?php echo e($xpEarned); ?>

                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="text-[8px] text-zinc-600 uppercase tracking-widest mb-1.5">
                                Próximos <span x-text="20 - (Math.floor(seconds/600)*20 - <?php echo e($lastXpMilestone * 20); ?>)">20</span> XP em
                            </div>
                            <div class="w-full h-1.5 bg-zinc-900 rounded-full overflow-hidden border border-white/5">
                                <div
                                    class="h-full bg-gradient-to-r from-emerald-500 to-emerald-300 rounded-full transition-all duration-1000"
                                    :style="'width:' + xpProgress() + '%'"
                                ></div>
                            </div>
                            <div class="text-[8px] text-zinc-600 mt-1" x-text="Math.floor((600 - (seconds % 600)) / 60) + 'm ' + ((600 - (seconds % 600)) % 60) + 's restantes'"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    
    
    
    <div class="anim-6 px-6 py-2.5 border-t border-white/5 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <span class="text-[8px] text-zinc-700 uppercase tracking-widest"><?php echo e(auth()->user()->name); ?></span>
            <span class="text-zinc-800">·</span>
            <span class="text-[8px] text-zinc-700 uppercase tracking-widest"><?php echo e(now()->format('d/m/Y H:i')); ?></span>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-[8px] text-zinc-700 uppercase tracking-widest">Tecla <kbd class="text-zinc-500">P</kbd> = Privacidade</span>
            <span class="text-zinc-800">·</span>
            <span class="text-[8px] text-zinc-700 uppercase tracking-widest">Segura <strong class="text-zinc-500">Unlock</strong> para sair</span>
        </div>
    </div>

</div>
<?php /**PATH C:\Projetos\gestao-de-custos\resources\views/livewire/lock-in-hub.blade.php ENDPATH**/ ?>