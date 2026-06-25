<div class="space-y-8 text-left">
    <x-page-header title="📢 Centro de Comunicação" description="Envie avisos globais para todos os utilizadores do site.">
    </x-page-header>

    <div class="grid gap-8 lg:grid-cols-3">
        {{-- FORMULÁRIO --}}
        <div class="glass-card p-6 bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm h-fit">
            <h3 class="text-sm font-black uppercase tracking-widest text-zinc-400 mb-6">Criar Aviso</h3>

            <form wire:submit="send" class="space-y-4">
                <flux:input wire:model="title" label="Título" placeholder="Ex: Manutenção Programada" />
                <flux:textarea wire:model="message" label="Mensagem" placeholder="Escreve aqui..." rows="4" />

                <div class="grid grid-cols-2 gap-4">
                    <flux:select wire:model="type" label="Tipo">
                        <option value="info">Informativo</option>
                        <option value="success">Sucesso</option>
                        <option value="warning">Aviso</option>
                        <option value="danger">Urgente</option>
                    </flux:select>
                    <flux:input type="date" wire:model="expires_at" label="Expiração" />
                </div>

                <flux:button type="submit" variant="primary" class="w-full" icon="paper-airplane">Publicar no Site</flux:button>
            </form>
        </div>

        {{-- LISTA DE AVISOS --}}
        <div class="lg:col-span-2 space-y-4">
            <h3 class="text-sm font-black uppercase tracking-widest text-zinc-400 mb-2">Avisos Enviados</h3>

            @forelse($announcements as $ann)
                <div class="glass-card p-6 bg-white dark:bg-zinc-900 rounded-3xl border-l-4
                    {{ $ann->type == 'info' ? 'border-blue-500' : '' }}
                    {{ $ann->type == 'success' ? 'border-emerald-500' : '' }}
                    {{ $ann->type == 'warning' ? 'border-amber-500' : '' }}
                    {{ $ann->type == 'danger' ? 'border-red-500' : '' }}
                    shadow-sm flex justify-between items-start">

                    <div>
                        <h4 class="font-black text-zinc-900 dark:text-white uppercase text-xs">{{ $ann->title }}</h4>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">{{ $ann->message }}</p>
                        <p class="text-[9px] text-zinc-400 font-bold uppercase mt-2">Publicado em: {{ \Carbon\Carbon::parse($ann->created_at)->format('d/m H:i') }}</p>
                    </div>

                    <flux:button size="xs" variant="ghost" icon="trash" color="red" wire:click="delete({{ $ann->id }})" wire:confirm="Apagar para todos os utilizadores?" />
                </div>
            @empty
                <div class="p-12 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-3xl text-zinc-400 italic">
                    Ainda não enviaste nenhum aviso.
                </div>
            @endforelse
        </div>
    </div>
</div>
