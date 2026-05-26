<div class="space-y-8">
    {{-- HEADER --}}
    <x-page-header title="Log de Atividades" description="Monitoriza todas as alterações feitas neste espaço de trabalho.">
        <x-slot:actions>
            <flux:button href="{{ route('dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate>Voltar</flux:button>
        </x-slot:actions>
    </x-page-header>

    <div class="max-w-3xl mx-auto relative">
        {{-- LINHA VERTICAL DA TIMELINE --}}
        <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-zinc-200 dark:bg-zinc-800"></div>

        <div class="space-y-8">
            @forelse($logs as $log)
                <div class="relative pl-16">
                    {{-- ÍCONE LATERAL BASEADO NA AÇÃO --}}
                    <div class="absolute left-0 top-0 w-12 h-12 rounded-2xl flex items-center justify-center border-4 border-zinc-50 dark:border-zinc-950 z-10
                        {{ $log->action === 'created' ? 'bg-emerald-500 text-white' : '' }}
                        {{ $log->action === 'updated' ? 'bg-amber-500 text-white' : '' }}
                        {{ $log->action === 'deleted' ? 'bg-red-500 text-white' : '' }}">

                        @if($log->action === 'created') <flux:icon name="plus" class="w-5 h-5" />
                        @elseif($log->action === 'updated') <flux:icon name="pencil-square" class="w-5 h-5" />
                        @else <flux:icon name="trash" class="w-5 h-5" />
                        @endif
                    </div>

                    {{-- CARD DO LOG --}}
                    <div class="glass-card p-5 bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center gap-3">
                                {{-- Avatar do Utilizador --}}
                                <div class="w-7 h-7 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-[10px] font-black uppercase text-zinc-500 border dark:border-zinc-700">
                                    {{ substr($log->user->name, 0, 2) }}
                                </div>
                                <p class="text-sm font-bold text-zinc-900 dark:text-white">
                                    {{ $log->user->name }}
                                </p>
                            </div>
                            <span class="text-[10px] font-medium text-zinc-400 uppercase tracking-tighter">
                                {{ $log->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <div class="space-y-2">
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                <span class="font-black uppercase text-[10px] px-1.5 py-0.5 rounded {{ $log->action === 'created' ? 'text-emerald-600 bg-emerald-50' : ($log->action === 'updated' ? 'text-amber-600 bg-amber-50' : 'text-red-600 bg-red-50') }}">
                                    {{ $log->action }}
                                </span>
                                {{ $log->description }}
                                <span class="font-bold text-zinc-900 dark:text-zinc-200">({{ $log->model_type }})</span>
                            </p>

                            {{-- MOSTRAR ALTERAÇÕES SE FOR UM 'UPDATE' --}}
                            @if($log->action === 'updated' && isset($log->changes['new']))
                                <div class="mt-3 p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-100 dark:border-zinc-800 text-[11px] font-medium space-y-1">
                                    @foreach($log->changes['new'] as $field => $newValue)
                                        <div class="flex items-center gap-2">
                                            <span class="text-zinc-400 uppercase font-black text-[9px]">{{ $field }}:</span>
                                            <span class="text-zinc-500 line-through">{{ $log->changes['old'][$field] ?? 'N/A' }}</span>
                                            <flux:icon name="arrow-long-right" class="w-3 h-3 text-zinc-300" />
                                            <span class="text-brand-600 font-bold">{{ $newValue }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-20 text-center">
                    <flux:icon name="clock" class="w-12 h-12 text-zinc-200 mx-auto mb-4" />
                    <p class="text-zinc-500 italic">Nenhuma atividade registada neste espaço.</p>
                </div>
            @endforelse
        </div>

        {{-- PAGINAÇÃO --}}
        <div class="mt-10 pl-16">
            {{ $logs->links() }}
        </div>
    </div>
</div>
