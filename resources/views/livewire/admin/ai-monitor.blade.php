<div class="space-y-8 text-left">
    <x-page-header title="🤖 Monitor de Inteligência Artificial" description="Visualize a utilização do chatbot, detete erros e analise as conversas dos utilizadores.">
        <x-slot:actions>
            <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Pesquisar em conversas..." class="w-80" />
        </x-slot:actions>
    </x-page-header>

    {{-- STATS DE IA --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="stat-card bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <p class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Total de Interações</p>
            <p class="text-3xl font-black text-zinc-900 dark:text-white mt-1">{{ number_format($stats['total_messages']) }}</p>
        </div>

        <div class="stat-card bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <p class="text-[10px] font-black uppercase text-brand-600 tracking-widest">Mensagens Hoje</p>
            <p class="text-3xl font-black text-brand-600 mt-1">{{ number_format($stats['messages_today']) }}</p>
        </div>

        <div class="stat-card bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <p class="text-[10px] font-black uppercase text-red-500 tracking-widest">Taxa de Erro API</p>
            <p class="text-3xl font-black text-red-500 mt-1">{{ $stats['error_rate'] }}%</p>
        </div>

        <div class="stat-card bg-white dark:bg-zinc-900 p-6 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <p class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Utilizadores Únicos</p>
            <p class="text-3xl font-black text-zinc-900 dark:text-white mt-1">{{ $stats['active_users'] }}</p>
        </div>
    </div>

    {{-- FLUXO DE CONVERSAS --}}
    <div class="glass-card bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
        <div class="p-6 border-b dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/20">
            <h3 class="text-xs font-black uppercase tracking-widest text-zinc-500">Histórico de Diálogos IA</h3>
        </div>

       {{-- Substitui o bloco do @forelse pelo código abaixo --}}
<div class="divide-y divide-zinc-100 dark:divide-zinc-800">
    @forelse($conversations as $msg)
        <div class="p-6 flex flex-col md:flex-row gap-6 hover:bg-zinc-50/50 dark:hover:bg-brand-500/[0.02] transition-all">
            {{-- Lado Esquerdo: Info do Utilizador --}}
            <div class="w-full md:w-56 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="size-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-black text-xs">
                        {{ substr($msg->user_name, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-xs font-black text-zinc-900 dark:text-white truncate">{{ $msg->user_name }}</p>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-tighter">{{ \Carbon\Carbon::parse($msg->created_at)->diffForHumans() }}</p>
                    </div>
                </div>

                <div class="mt-3 flex gap-2">
                    <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase border {{ $msg->role == 'user' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-emerald-50 text-emerald-600 border-emerald-200' }}">
                        {{ $msg->role }}
                    </span>
                    {{-- Link para o dossiê do utilizador (se quiseres) --}}
                    <button class="text-[9px] font-black text-zinc-400 hover:text-brand-500 uppercase tracking-widest transition-colors">Ver Perfil</button>
                </div>
            </div>

            {{-- Lado Direito: A Mensagem --}}
            <div class="flex-1 relative">
                <div class="p-5 rounded-3xl text-sm leading-relaxed shadow-sm
                    {{ $msg->role == 'assistant'
                        ? 'bg-emerald-500/5 border border-emerald-500/10 text-zinc-700 dark:text-emerald-50 font-medium'
                        : 'bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300'
                    }} {{ $msg->is_error ? 'bg-red-50 border-red-200 text-red-700' : '' }}">

                    @if($msg->is_error)
                        <flux:icon name="exclamation-triangle" class="size-4 mb-2" />
                    @endif

                    {{ $msg->content }}
                </div>

                @if($msg->tokens > 0)
                    <div class="absolute -bottom-5 right-2 flex items-center gap-1">
                        <flux:icon name="bolt" variant="micro" class="size-3 text-amber-500" />
                        <span class="text-[9px] font-black text-zinc-400 uppercase tracking-tighter">{{ number_format($msg->tokens) }} tokens</span>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="p-20 text-center text-zinc-400 italic font-black uppercase text-xs opacity-40">
            Nenhuma interação registada.
        </div>
    @endforelse
</div>

        <div class="p-4 border-t dark:border-zinc-800 bg-zinc-50/30">
            {{ $conversations->links() }}
        </div>
    </div>
</div>
