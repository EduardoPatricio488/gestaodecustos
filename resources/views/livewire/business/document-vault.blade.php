<div class="space-y-10 pb-24">

    {{-- 1. HEADER DE ARQUIVO --}}
    <div class="relative">
        <div class="absolute -top-10 left-0 size-64 bg-brand-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 relative z-10 px-2">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="absolute inset-0 bg-brand-500/20 blur-2xl rounded-full group-hover:bg-brand-500/40 transition-all duration-700"></div>
                    <div class="relative p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl">
                        <flux:icon name="folder-open" class="w-10 h-10 text-brand-600" />
                    </div>
                </div>

                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black uppercase tracking-tighter italic leading-none text-zinc-900 dark:text-white">
                            Arquivo Digital
                        </h1>
                        <flux:badge variant="neutral" class="bg-zinc-100 dark:bg-zinc-800 text-[9px] font-black uppercase tracking-widest border-none px-3 py-1 text-zinc-500">
                            Document Vault
                        </flux:badge>
                    </div>

                    <p class="text-sm text-zinc-500 font-medium italic mt-2 text-zinc-400">
                        Custódia centralizada de <span class="text-brand-600 font-bold uppercase tracking-tighter">Contratos, Seguros e Ativos</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-2.5 rounded-[1.8rem] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <flux:modal.trigger name="document-modal">
                    <flux:button variant="primary" icon="document-plus" class="rounded-2xl px-6 font-black uppercase tracking-widest shadow-lg shadow-brand-500/20">
                        Novo Documento
                    </flux:button>
                </flux:modal.trigger>

                <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 mx-1"></div>
                <flux:button href="{{ route('hub.business.dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate title="Voltar" class="rounded-xl" />
            </div>
        </div>
    </div>

    {{-- 2. KPIs DE CUSTÓDIA --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Total --}}
        <div class="relative overflow-hidden bg-zinc-950 p-8 rounded-[2.5rem] shadow-2xl border border-zinc-800 group">
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4 text-brand-400">
                    <flux:icon name="archive-box" variant="outline" class="size-6" />
                    <span class="text-[9px] font-black text-white/50 border border-white/10 px-2 py-1 rounded-lg uppercase tracking-widest italic">Inventário Digital</span>
                </div>
                <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-1">Total de Itens em Custódia</p>
                <h3 class="text-4xl font-black text-white tracking-tighter">
                    {{ $totalDocs }} <span class="text-xs text-zinc-500 uppercase font-bold ml-2 tracking-widest italic">Arquivos</span>
                </h3>
            </div>
        </div>

        {{-- Expirar --}}
        <div class="glass-card relative bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group hover:border-orange-500/30 transition-all">
            <flux:icon name="clock" variant="outline" class="size-6 text-orange-600 mb-4" />
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Validade Próxima (30 dias)</p>
            <h3 class="text-4xl font-black text-orange-500 tracking-tighter">{{ $expiringSoonCount }}</h3>
        </div>

        {{-- Expirados --}}
        <div class="glass-card relative bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm group hover:border-red-500/30 transition-all">
            <flux:icon name="exclamation-circle" variant="outline" class="size-6 text-red-600 mb-4" />
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-1">Documentos Fora de Prazo</p>
            <h3 class="text-4xl font-black text-red-600 tracking-tighter">{{ $expiredCount }}</h3>
        </div>
    </div>

    {{-- 3. DIRETÓRIO DE DOCUMENTOS (VAULT) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        @forelse($documents as $doc)
            @php
                // Lógica de validade
                $isExpired = $doc->expiry_date ? \Carbon\Carbon::parse($doc->expiry_date)->isPast() : false;
                $isExpiringSoon = false;
                if ($doc->expiry_date && !$isExpired) {
                    $isExpiringSoon = \Carbon\Carbon::parse($doc->expiry_date)->diffInDays(now()) <= 30;
                }
            @endphp

            <div wire:key="document-{{ $doc->id }}"
                class="glass-card p-6 bg-white dark:bg-zinc-900 border-2 rounded-[2.2rem] transition-all duration-300 group hover:scale-[1.02] relative
                {{ $isExpired ? 'border-red-500/30 bg-red-50/5' : ($isExpiringSoon ? 'border-orange-500/30 bg-orange-50/5' : 'border-zinc-100 dark:border-zinc-800 hover:border-brand-500/40') }}">

                <div class="flex justify-between items-start mb-6">
                    {{-- Ícone Dinâmico --}}
                    <div class="p-3 rounded-2xl transition-colors
                        {{ $isExpired ? 'bg-red-500/10 text-red-500' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 group-hover:text-brand-500' }}">
                        <flux:icon name="document-text" variant="outline" class="size-7" />
                    </div>

                    {{-- DROPDOWN CORRIGIDO --}}
                    <flux:dropdown>
                        <flux:button variant="ghost" icon="ellipsis-horizontal" size="sm"
                            class="rounded-xl text-zinc-400 opacity-50 group-hover:opacity-100 transition-opacity" />

                        <flux:menu class="w-48">
                            @if($doc->file_path)
                                <flux:menu.item href="{{ Storage::url($doc->file_path) }}" target="_blank" icon="eye">
                                    Visualizar
                                </flux:menu.item>
                                <flux:menu.item href="{{ Storage::url($doc->file_path) }}" download icon="arrow-down-tray">
                                    Download
                                </flux:menu.item>
                            @endif

                            <flux:menu.separator />

                            <flux:menu.item wire:click="delete({{ $doc->id }})"
                                wire:confirm="Tens a certeza que desejas remover este documento do cofre?"
                                variant="danger" icon="trash">
                                Remover
                            </flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                </div>

                <div class="space-y-1">
                    <h4 class="font-black dark:text-white uppercase text-xs tracking-tight truncate leading-none">
                        {{ $doc->name }}
                    </h4>

                    <div class="flex items-center gap-2">
                        <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">
                            {{ $doc->category }}
                        </span>
                        <div class="size-1 rounded-full bg-zinc-300 dark:bg-zinc-700"></div>
                        <span class="text-[9px] font-bold text-zinc-500 uppercase italic">v1.0</span>
                    </div>
                </div>

                {{-- Indicador de Validade --}}
                @if($doc->expiry_date)
                    <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.2em]">Expiração</span>
                            <span class="text-[10px] font-black tracking-tighter
                                {{ $isExpired ? 'text-red-500' : ($isExpiringSoon ? 'text-orange-500' : 'text-zinc-600 dark:text-zinc-300') }}">
                                {{ \Carbon\Carbon::parse($doc->expiry_date)->format('d/m/Y') }}
                            </span>
                        </div>
                        <div class="h-1 w-full bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                            <div class="h-full {{ $isExpired ? 'bg-red-500' : ($isExpiringSoon ? 'bg-orange-500' : 'bg-emerald-500') }}"
                                 style="width: 100%"></div>
                        </div>
                    </div>
                @else
                    <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                        <span class="text-[9px] font-black text-zinc-300 dark:text-zinc-600 uppercase tracking-[0.2em] italic">Sem validade</span>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full py-24 text-center">
                <div class="flex flex-col items-center justify-center gap-4 opacity-30">
                    <flux:icon name="folder-open" class="size-12" />
                    <p class="text-xs font-black uppercase tracking-widest">Cofre Vazio</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- 4. MODAL: ARQUIVAR DOCUMENTO --}}
    <flux:modal name="document-modal" position="center" class="md:w-[550px] !p-0 overflow-visible" wire:ignore.self>
        <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-10 shadow-2xl border border-zinc-200 dark:border-zinc-800">

            {{-- Botão Fechar --}}
            <div class="absolute top-6 right-6">
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm" icon="x-mark" class="rounded-full" />
                </flux:modal.close>
            </div>

            {{-- Cabeçalho do Modal --}}
            <div class="flex items-center gap-4">
                <div class="p-3 bg-brand-600 rounded-2xl text-white shadow-lg shadow-brand-500/20">
                    <flux:icon name="document-plus" class="size-6" />
                </div>
                <div>
                    <flux:heading size="xl" class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">
                        Arquivar Documento
                    </flux:heading>
                    <p class="text-xs text-zinc-400 font-medium italic">Custódia segura de contratos e certidões.</p>
                </div>
            </div>

            <div class="space-y-8">
                {{-- Nome do Documento --}}
                <div class="space-y-2">
                    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Identificação do Arquivo</flux:label>
                    <flux:input wire:model="name" placeholder="Ex: Contrato de Arrendamento 2024"
                        class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-2xl h-14 shadow-inner" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Categoria --}}
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Categoria</flux:label>
                        <flux:select wire:model="category" class="font-black uppercase text-[10px] !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-xl h-12 shadow-inner">
                            <option value="Legal">⚖️ Legal / Contratos</option>
                            <option value="RH">👥 Recursos Humanos</option>
                            <option value="Seguros">🛡️ Seguros</option>
                            <option value="Impostos">💼 Impostos</option>
                            <option value="Outros">📦 Outros Ativos</option>
                        </flux:select>
                    </div>

                    {{-- Data de Expiração --}}
                    <div class="space-y-2">
                        <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Expiração (Opcional)</flux:label>
                        <flux:input wire:model="expiry_date" type="date"
                            class="font-bold !bg-zinc-50 dark:!bg-zinc-900 !border-none rounded-xl h-12 shadow-inner" />
                    </div>
                </div>

                {{-- Zona de Upload com Barra de Progresso --}}
                <div x-data="{ uploading: false, progress: 0 }"
                    x-on:livewire-upload-start="uploading = true"
                    x-on:livewire-upload-finish="uploading = false"
                    x-on:livewire-upload-error="uploading = false"
                    x-on:livewire-upload-progress="progress = $event.detail.progress"
                    class="relative p-10 bg-zinc-50 dark:bg-zinc-900/50 rounded-[2rem] border-2 border-dashed border-zinc-200 dark:border-zinc-800 text-center transition-all hover:border-brand-500/50 group/upload">

                    <input type="file" wire:model="file" id="file-vault-upload" class="hidden" accept=".pdf,image/*">

                    <label for="file-vault-upload" class="cursor-pointer">
                        <div class="size-16 bg-white dark:bg-zinc-900 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm border border-zinc-100 dark:border-zinc-800 group-hover/upload:scale-110 transition-transform">
                            <flux:icon name="cloud-arrow-up" class="size-8 text-zinc-400 group-hover/upload:text-brand-500 transition-colors" />
                        </div>

                        <div class="space-y-1">
                            <p class="text-sm font-black dark:text-white uppercase">
                                {{ $file ? 'Ficheiro Pronto' : 'Selecionar Documento' }}
                            </p>
                            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">
                                {{ $file ? $file->getClientOriginalName() : 'PDF ou Imagem (Máx 10MB)' }}
                            </p>
                        </div>
                    </label>

                    {{-- Barra de Progresso Real-time --}}
                    <div x-show="uploading" x-cloak class="absolute inset-x-6 bottom-6 space-y-2">
                        <div class="h-1.5 w-full bg-zinc-200 dark:bg-zinc-800 rounded-full overflow-hidden">
                            <div class="h-full bg-brand-500 shadow-[0_0_10px_#3b82f6] transition-all duration-300"
                                 x-bind:style="'width: ' + progress + '%'"></div>
                        </div>
                        <p class="text-[9px] font-black text-brand-600 uppercase tracking-widest animate-pulse">
                            A encriptar: <span x-text="progress"></span>%
                        </p>
                    </div>

                    @error('file')
                        <p class="mt-4 text-[10px] text-red-500 font-black uppercase italic">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Ações --}}
            <div class="flex gap-4 pt-4">
                <flux:modal.close>
                    <flux:button variant="ghost" class="flex-1 font-black uppercase text-[10px] text-zinc-400">Descartar</flux:button>
                </flux:modal.close>

                <flux:button wire:click="save" variant="primary"
                    class="flex-[2] font-black uppercase tracking-widest shadow-xl shadow-brand-500/20 h-14 rounded-2xl"
                    wire:loading.attr="disabled">

                    <span wire:loading.remove wire:target="save">Confirmar Arquivo</span>
                    <span wire:loading wire:target="save" class="flex items-center gap-2">
                        <div class="size-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                        A Processar...
                    </span>
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- 6. RODAPÉ DE PÁGINA --}}
    <footer class="pt-20 pb-10 text-center border-t border-zinc-100 dark:border-zinc-800 mt-20 opacity-50">
        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">
            © {{ date('Y') }} {{ config('app.name') }} · Protocolo de Custódia Digital · Vault v1.0
        </p>
    </footer>

</div> {{-- FIM DO ROOT --}}
