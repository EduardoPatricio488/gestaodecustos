<div x-data x-on:ai-page-context.window="$wire.setPageContext($event.detail)" class="contents">
    <button type="button" wire:click="toggle" aria-label="Abrir Finance Pro AI Copilot" class="fixed bottom-5 right-5 z-[180] flex h-14 w-14 items-center justify-center rounded-2xl border border-white/10 bg-zinc-950 text-white shadow-2xl shadow-black/30 transition hover:-translate-y-0.5 hover:bg-zinc-900 dark:bg-white dark:text-zinc-950">
        <span class="text-xl">✦</span>
        @if($pendingActions)<span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-amber-500 px-1 text-[10px] font-black text-white">{{ count($pendingActions) }}</span>@endif
    </button>

    @if($isOpen)
        <div class="fixed inset-0 z-[170] bg-zinc-950/20 backdrop-blur-[2px]" wire:click="toggle"></div>
        <section class="fixed bottom-5 right-5 z-[190] flex h-[min(760px,calc(100vh-40px))] w-[min(430px,calc(100vw-40px))] flex-col overflow-hidden rounded-[2rem] border border-zinc-200 bg-white shadow-2xl dark:border-zinc-800 dark:bg-zinc-950" aria-label="Finance Pro AI Copilot">
            <header class="flex items-center justify-between border-b border-zinc-200 px-5 py-4 dark:border-zinc-800">
                <div class="min-w-0"><div class="flex items-center gap-2"><span class="flex h-8 w-8 items-center justify-center rounded-xl bg-zinc-950 text-white dark:bg-white dark:text-zinc-950">✦</span><div><h2 class="text-sm font-black tracking-tight text-zinc-950 dark:text-white">Finance Pro AI</h2><p class="text-[10px] font-bold uppercase tracking-[0.18em] text-zinc-400">Financial Copilot</p></div></div></div>
                <div class="flex items-center gap-1"><button wire:click="newConversation" type="button" class="rounded-lg px-2 py-1.5 text-[10px] font-black uppercase tracking-wider text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-900">Novo</button><button wire:click="archiveConversation" type="button" class="rounded-lg px-2 py-1.5 text-[10px] font-black uppercase tracking-wider text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-900">Arquivar</button><button wire:click="toggle" type="button" class="rounded-lg px-2 py-1.5 text-lg text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900">×</button></div>
            </header>

            <div class="border-b border-zinc-100 bg-zinc-50 px-5 py-3 dark:border-zinc-900 dark:bg-zinc-900/60"><div class="flex items-center justify-between gap-3"><div class="min-w-0"><p class="text-[9px] font-black uppercase tracking-[0.18em] text-zinc-400">Contexto</p><p class="truncate text-xs font-bold text-zinc-700 dark:text-zinc-200">{{ $pageContext['module'] ?? 'Finance Pro AI' }}</p></div><span class="rounded-full border border-zinc-200 bg-white px-2 py-1 text-[9px] font-black uppercase text-zinc-500 dark:border-zinc-800 dark:bg-zinc-950">{{ in_array(auth()->user()?->currentWorkspace?->type, ['business', 'company'], true) ? 'Business' : 'Personal' }}</span></div></div>

            <div class="flex-1 space-y-4 overflow-y-auto p-4" id="finance-pro-ai-messages">
                @forelse($messages as $message)
                    <div class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}"><div class="max-w-[88%] rounded-2xl px-4 py-3 {{ $message['role'] === 'user' ? 'bg-zinc-950 text-white dark:bg-white dark:text-zinc-950' : 'bg-zinc-100 text-zinc-800 dark:bg-zinc-900 dark:text-zinc-100' }}"><div class="whitespace-pre-wrap text-sm leading-6">{{ $message['content'] }}</div></div></div>
                @empty
                    <div class="flex h-full min-h-64 flex-col items-center justify-center px-8 text-center"><div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-zinc-100 text-2xl dark:bg-zinc-900">✦</div><h3 class="text-base font-black text-zinc-950 dark:text-white">O teu copiloto financeiro</h3><p class="mt-2 text-xs leading-5 text-zinc-500">Pergunta-me sobre as tuas despesas, objetivos, investimentos ou, no workspace empresarial, sobre a performance da empresa.</p></div>
                @endforelse
                @if($isLoading)<div class="flex justify-start"><div class="rounded-2xl bg-zinc-100 px-4 py-3 text-xs font-bold text-zinc-500 dark:bg-zinc-900">A analisar os teus dados…</div></div>@endif
            </div>

            @if($pendingActions)
                <div class="max-h-56 space-y-3 overflow-y-auto border-t border-amber-200 bg-amber-50 p-4 dark:border-amber-900/50 dark:bg-amber-950/20">
                    @foreach($pendingActions as $action)
                        <div class="rounded-2xl border border-amber-200 bg-white p-4 dark:border-amber-900/50 dark:bg-zinc-950"><p class="text-[9px] font-black uppercase tracking-[0.18em] text-amber-600">Confirmação necessária</p><p class="mt-1 text-sm font-black text-zinc-900 dark:text-white">{{ $action['title'] }}</p><p class="mt-1 text-xs text-zinc-500">{{ $action['summary'] }}</p><div class="mt-3 space-y-1 text-[11px] text-zinc-600 dark:text-zinc-300">@foreach(($action['details'] ?? []) as $key => $value) @if(is_scalar($value))<div class="flex justify-between gap-3"><span class="font-bold">{{ $key }}</span><span class="text-right">{{ $value }}</span></div>@endif @endforeach</div><button type="button" wire:click="confirmAction({{ (int) $action['id'] }})" class="mt-4 w-full rounded-xl bg-zinc-950 px-4 py-2.5 text-xs font-black uppercase tracking-wider text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950">Confirmar ação</button></div>
                    @endforeach
                </div>
            @endif

            <form wire:submit="sendMessage" class="border-t border-zinc-200 p-3 dark:border-zinc-800"><div class="flex items-end gap-2 rounded-2xl border border-zinc-200 bg-zinc-50 p-2 dark:border-zinc-800 dark:bg-zinc-900"><textarea wire:model="input" rows="1" placeholder="Pergunta ao teu copiloto…" class="min-h-10 flex-1 resize-none border-0 bg-transparent px-2 py-2 text-sm text-zinc-900 outline-none ring-0 placeholder:text-zinc-400 focus:border-0 focus:ring-0 dark:text-white" @keydown.enter.exact.prevent="$wire.sendMessage()"></textarea><button type="submit" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-zinc-950 text-white transition hover:scale-105 dark:bg-white dark:text-zinc-950" wire:loading.attr="disabled">↑</button></div></form>
        </section>
    @endif

    @script
    <script>
        (() => {
            const publish = () => {
                const path = window.location.pathname;
                const module = path === '/dashboard' ? 'dashboard' : path.split('/').filter(Boolean)[0] || 'finance-pro-ai';
                window.dispatchEvent(new CustomEvent('ai-page-context', {
                    detail: { path, module, period: new URLSearchParams(window.location.search).get('period') },
                }));
            };
            publish();
            window.addEventListener('livewire:navigated', publish);
        })();
    </script>
    @endscript
</div>
