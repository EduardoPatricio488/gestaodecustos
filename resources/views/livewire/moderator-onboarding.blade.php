<div>
    @if($isOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/95 backdrop-blur-2xl" x-transition>
            <div class="w-full max-w-3xl bg-zinc-900 border border-amber-500/20 rounded-[3rem] shadow-[0_0_50px_rgba(245,158,11,0.15)] relative overflow-hidden flex flex-col md:flex-row text-left">

                {{-- LADO ESQUERDO: STATUS --}}
                <div class="w-full md:w-72 bg-amber-500/5 p-8 border-b md:border-b-0 md:border-r border-white/5 flex flex-col justify-between">
                    <div class="space-y-6">
                        <div class="size-14 bg-amber-500 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/20">
                            <flux:icon name="shield-check" variant="solid" class="text-zinc-900 size-8" />
                        </div>
                        <div>
                            <h2 class="text-amber-500 font-black uppercase tracking-widest text-xs">Acesso Sentinela</h2>
                            <p class="text-zinc-500 text-[10px] font-mono mt-1 uppercase tracking-tighter">Cargo: Moderador</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @foreach(range(1, $totalSteps) as $i)
                            <div class="flex items-center gap-3">
                                <div class="size-2 rounded-full {{ $step >= $i ? 'bg-amber-500 shadow-[0_0_8px_#f59e0b]' : 'bg-zinc-800' }}"></div>
                                <span class="text-[9px] font-black uppercase tracking-widest {{ $step == $i ? 'text-white' : 'text-zinc-600' }}">Protocolo 0{{ $i }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- LADO DIREITO: CONTEÚDO --}}
                <div class="flex-1 p-10 relative">
                    <div class="absolute -right-20 -top-20 size-64 bg-amber-500/5 blur-[100px] rounded-full pointer-events-none"></div>

                    <div class="min-h-[350px] flex flex-col">
                        @if($step == 1)
                            <div class="space-y-6 animate-fade-in">
                                <span class="px-3 py-1 bg-amber-500/10 text-amber-500 text-[8px] font-black rounded-full uppercase tracking-[0.2em]">Sessão de Supervisão</span>
                                <h3 class="text-4xl font-black text-white leading-none italic uppercase tracking-tighter">Guardião da Comunidade</h3>
                                <p class="text-zinc-400 text-sm leading-relaxed">Bem-vindo à tua consola. O teu papel é garantir que o ecossistema <strong>Finance Pro</strong> permanece seguro, saudável e funcional para todos os utilizadores.</p>
                                <div class="p-4 bg-amber-500/5 rounded-2xl border border-amber-500/10 italic text-xs text-amber-200">
                                    "Manter a ordem é o primeiro passo para o crescimento global."
                                </div>
                            </div>
                        @elseif($step == 2)
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
                        @elseif($step == 3)
                            <div class="space-y-6 animate-fade-in">
                                <h3 class="text-3xl font-black text-white leading-none uppercase italic tracking-tighter">Central de Suporte</h3>
                                <p class="text-zinc-400 text-sm leading-relaxed">És o ponto de contacto direto. Gere os <strong>Tickets de Suporte</strong>, esclarece dúvidas financeiras e ajuda os utilizadores com dificuldades técnicas.</p>

                                <div class="p-4 bg-white/5 rounded-2xl border border-white/10 flex items-center gap-4">
                                    <flux:icon name="chat-bubble-left-right" class="text-amber-500 size-8" />
                                    <div>
                                        <p class="text-white font-black text-sm uppercase leading-none">Atendimento Ativo</p>
                                        <p class="text-[9px] text-zinc-500 uppercase mt-1">Tempo médio alvo: < 4 horas</p>
                                    </div>
                                </div>
                            </div>
                        @elseif($step == 4)
                            <div class="space-y-6 animate-fade-in">
                                <h3 class="text-3xl font-black text-white leading-none uppercase italic tracking-tighter">Comunicação de Serviço</h3>
                                <p class="text-zinc-400 text-sm leading-relaxed">Podes emitir notificações e dicas. Usa isto para educar a comunidade com boas práticas financeiras ou alertar sobre novidades.</p>

                                <div class="w-full bg-amber-500 p-4 rounded-xl flex items-center gap-4 shadow-lg shadow-amber-500/20">
                                    <flux:icon name="megaphone" class="size-5 text-zinc-900" />
                                    <span class="text-[10px] font-black text-zinc-950 uppercase tracking-widest leading-tight">Dica: O segredo da riqueza está na consistência, não no valor.</span>
                                </div>
                            </div>
                        @endif

                        <div class="mt-auto flex gap-4 pt-10">
                            <button wire:click="finish" class="px-6 h-14 border border-white/10 text-zinc-500 rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] hover:bg-white/5 transition-all">Sair</button>
                            <button wire:click="nextStep" class="flex-1 h-14 bg-amber-500 text-zinc-950 rounded-2xl font-black uppercase text-xs tracking-[0.2em] hover:bg-amber-400 transition-all shadow-2xl shadow-amber-500/20 active:scale-95">
                                {{ $step < $totalSteps ? 'Próximo Protocolo' : 'Ativar Vigilância 🛡️' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
