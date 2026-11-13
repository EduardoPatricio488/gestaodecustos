<div class="space-y-4">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Dados guardados pela IA</h3>
            <p class="mt-1 text-xs leading-5 text-zinc-500">Consulta os dados ativos deste workspace e os dados gerais. Apagar toda a memória afeta todos os teus workspaces, mas não apaga as conversas.</p>
        </div>
        @if($hasMemories)
            <flux:button wire:click="clearMemories" wire:confirm="Queres apagar toda a memória da IA de todos os teus workspaces?" variant="danger" size="sm">Apagar toda a memória</flux:button>
        @endif
    </div>
    @if($memoryError)
        <p role="alert" class="rounded-xl border border-red-200 bg-red-50 p-3 text-xs text-red-700 dark:border-red-900 dark:bg-red-950/30 dark:text-red-300">{{ $memoryError }}</p>
    @endif
    @forelse($memories as $memory)
        <div wire:key="memory-{{ $memory->id }}" class="rounded-xl border border-zinc-200 p-3 dark:border-zinc-800">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <p class="text-xs font-bold text-zinc-800 dark:text-zinc-200">{{ str($memory->type)->replace('_', ' ')->headline() }} · {{ str($memory->key)->replace('_', ' ') }}</p>
                    <p class="mt-1 text-xs text-zinc-500">{{ $memory->workspace_id ? 'Workspace atual' : 'Memória geral' }} @if($memory->expires_at) · Expira {{ $memory->expires_at->format('d/m/Y') }} @endif</p>
                </div>
                <flux:button wire:click="forgetMemory({{ $memory->id }})" wire:confirm="Apagar esta memória?" variant="ghost" size="sm">Apagar</flux:button>
            </div>
            <details class="mt-3">
                <summary class="cursor-pointer text-xs font-semibold text-zinc-500">Ver dados guardados</summary>
                <pre class="mt-2 max-h-72 overflow-auto whitespace-pre-wrap break-words text-xs leading-5 text-zinc-600 dark:text-zinc-300">{{ json_encode($memory->value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
            </details>
        </div>
    @empty
        <p class="text-xs text-zinc-500">Não existem dados ativos para este workspace.</p>
    @endforelse
</div>
