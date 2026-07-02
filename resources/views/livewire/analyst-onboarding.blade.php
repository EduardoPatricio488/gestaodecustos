<div>
    @if($isOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/95 backdrop-blur-2xl" x-transition>
            <div class="w-full max-w-3xl bg-zinc-900 border border-cyan-500/20 rounded-[3rem] shadow-[0_0_50px_rgba(6,182,212,0.15)] relative overflow-hidden flex flex-col md:flex-row text-left">

                {{-- LADO ESQUERDO: STATUS --}}
                <div class="w-full md:w-72 bg-cyan-500/5 p-8 border-b md:border-b-0 md:border-r border-white/5 flex flex-col justify-between">
                    <div class="space-y-6">
                        <div class="size-14 bg-cyan-500 rounded-2xl flex items-center justify-center shadow-lg shadow-cyan-500/20">
                            <flux:icon name="chart-bar" variant="solid" class="text-zinc-900 size-8" />
                        </div>
                        <div>
                            <h2 class="text-cyan-500 font-black uppercase tracking-widest text-xs">Cérebro Analítico</h2>
                            <p class="text-zinc-500 text-[10px] font-mono mt-1 uppercase tracking-tighter">Nível: Analista de Dados</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @foreach(range(1, $totalSteps) as $i)
                            <div class="flex items-center gap-3">
                                <div class="size-2 rounded-full {{ $step >= $i ? 'bg-cyan-500 shadow-[0_0_8px_#06b6d4]' : 'bg-zinc-800' }}"></div>
                                <span class="text-[9px] font-black uppercase tracking-widest {{ $step == $i ? 'text-white' : 'text-zinc-600' }}">Métrica 0{{ $i }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- LADO DIREITO: CONTEÚDO --}}
                <div class="flex-1 p-10 relative">
                    <div class="absolute -right-20 -top-20 size-64 bg-cyan-500/5 blur-[100px] rounded-full pointer-events-none"></div>

                    <div class="min-h-[350px] flex flex-col">
                        @if($step == 1)
                            <div class="space-y-6 animate-fade-in">
                                <span class="px-3 py-1 bg-cyan-500/10 text-cyan-400 text-[8px] font-black rounded-full uppercase tracking-[0.2em]">Sessão de Inteligência</span>
                                <h3 class="text-4xl font-black text-white leading-none italic uppercase tracking-tighter">Observatório de Dados</h3>
                                <p class="text-zinc-400 text-sm leading-relaxed">Bem-vindo, Analista. O teu papel é converter dados brutos em decisões estratégicas. Tens acesso privilegiado a todas as estatísticas de crescimento do <strong>Finance Pro</strong>.</p>
                                <div class="p-4 bg-cyan-500/5 rounded-2xl border border-cyan-500/10 italic text-xs text-cyan-200">
                                    "O que não se mede, não se gere."
                                </div>
                            </div>
                        @elseif($step == 2)
                            <div class="space-y-6 animate-fade-in">
                                <h3 class="text-3xl font-black text-white leading-none uppercase italic tracking-tighter">Monitor de Crescimento</h3>
                                <p class="text-zinc-400 text-sm leading-relaxed">Na aba <strong>Estatísticas</strong>, podes acompanhar o fluxo de novos utilizadores, a taxa de retenção e o volume financeiro global processado pela plataforma.</p>

                                <div class="bg-black/40 rounded-2xl p-5 border border-white/5 space-y-3">
                                    <div class="flex justify-between items-end">
                                        <div>
                                            <p class="text-[8px] text-zinc-500 uppercase font-black">Volume Mensal</p>
                                            <p class="text-xl font-black text-cyan-400">+12.4%</p>
                                        </div>
                                        <div class="flex gap-1 items-end">
                                            @foreach([30, 50, 40, 80, 60] as $h)
                                                <div class="w-1.5 bg-cyan-500/40 rounded-t-sm" style="height: {{ $h }}px"></div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($step == 3)
                            <div class="space-y-6 animate-fade-in">
                                <h3 class="text-3xl font-black text-white leading-none uppercase italic tracking-tighter">Produtividade & Gamificação</h3>
                                <p class="text-zinc-400 text-sm leading-relaxed">Analisa como os utilizadores interagem com o sistema. Quais as metas mais comuns? Quem são os utilizadores mais ativos? Estas métricas ajudam-nos a definir o futuro do produto.</p>

                                <div class="p-4 bg-white/5 rounded-2xl border border-white/10 flex items-center gap-4">
                                    <flux:icon name="bolt" class="text-cyan-500 size-8" />
                                    <div>
                                        <p class="text-white font-black text-sm uppercase leading-none">Taxa de Conclusão</p>
                                        <p class="text-[9px] text-zinc-500 uppercase mt-1">Média global: 68% das metas atingidas</p>
                                    </div>
                                </div>
                            </div>
                        @elseif($step == 4)
                            <div class="space-y-6 animate-fade-in">
                                <h3 class="text-3xl font-black text-white leading-none uppercase italic tracking-tighter">Saúde da IA</h3>
                                <p class="text-zinc-400 text-sm leading-relaxed">Monitoriza as interações com o CFO Inteligente. Identifica padrões de dúvidas que podem ser resolvidos com novas automações e melhorias no modelo de IA.</p>

                                <div class="w-full bg-cyan-500 p-4 rounded-xl flex items-center gap-4 shadow-lg shadow-cyan-500/20">
                                    <flux:icon name="sparkles" class="size-5 text-zinc-900" />
                                    <span class="text-[10px] font-black text-zinc-950 uppercase tracking-widest leading-tight">Insight: 80% dos pedidos de IA focam-se em "Redução de Custos Fixos".</span>
                                </div>
                            </div>
                        @endif

                        <div class="mt-auto flex gap-4 pt-10">
                            <button wire:click="finish" class="px-6 h-14 border border-white/10 text-zinc-500 rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] hover:bg-white/5 transition-all">Sair</button>
                            <button wire:click="nextStep" class="flex-1 h-14 bg-cyan-500 text-zinc-950 rounded-2xl font-black uppercase text-xs tracking-[0.2em] hover:bg-cyan-400 transition-all shadow-2xl shadow-cyan-500/20 active:scale-95">
                                {{ $step < $totalSteps ? 'Próxima Métrica' : 'Iniciar Monitorização 📈' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
