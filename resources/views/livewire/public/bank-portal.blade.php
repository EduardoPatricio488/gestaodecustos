{{-- CAIXA PRINCIPAL --}}
<div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-8 sm:p-10 rounded-[2.5rem] shadow-2xl backdrop-blur-md space-y-8 text-left">

    {{-- ÍCONE CENTRAL --}}
    <div class="flex justify-center">
        <div class="size-16 bg-zinc-900 rounded-2xl shadow-xl flex items-center justify-center border border-white/10">
            <flux:icon name="building-library" variant="solid" class="size-10 text-white" />
        </div>
    </div>

    <div class="text-center space-y-2">
        <h1 class="text-2xl font-black text-zinc-900 dark:text-white uppercase italic tracking-tighter leading-none">
            Acesso de Auditoria
        </h1>
        <p class="text-[11px] text-zinc-500 font-medium italic">
            Introduz as credenciais institucionais para verificação.
        </p>
    </div>

    {{-- FORMULÁRIO DE LOGIN --}}
    <form wire:submit.prevent="login" class="space-y-6">
        @if (session()->has('error'))
            <div class="p-3 bg-red-500/10 text-red-500 text-[10px] font-bold rounded-xl border border-red-500/20 text-center uppercase tracking-widest italic">
                {{ session('error') }}
            </div>
        @endif

        <div class="space-y-5">
            <div class="space-y-2" x-data="{
                nif: @entangle('company_nif'),
                formatNIF(v) { if (!v) return ''; return v.replace(/\D/g, '').replace(/(\d{3})(?=\d)/g, '$1 ').substring(0, 11); }
            }" x-init="nif = formatNIF(nif)">
                <label class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1">NIF da Empresa Auditada</label>
                <input type="text" x-model="nif" x-on:input="nif = formatNIF($event.target.value)" placeholder="000 000 000"
                       class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-center font-mono font-bold text-sm focus:ring-2 focus:ring-zinc-500 outline-none transition-all dark:text-white" />
            </div>

            <div class="space-y-2">
                <label class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1">Código de Auditoria (Token)</label>
                <input wire:model="token" type="text" maxlength="128" autocomplete="one-time-code" placeholder="Token de acesso"
                       class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-center font-mono font-black text-xl tracking-[0.6em] text-zinc-900 dark:text-white focus:ring-2 focus:ring-zinc-500 outline-none transition-all" />
            </div>

            <flux:button type="submit" variant="primary" class="w-full h-14 !bg-zinc-900 hover:!bg-zinc-800 text-white rounded-2xl font-black uppercase tracking-widest text-[11px] border-none mt-4 shadow-lg active:scale-95 transition-all">
                Autenticar Auditoria
            </flux:button>
        </div>
    </form>

    {{-- PEDIDO DE ACESSO --}}
    <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800/50">
        <button type="button" x-data x-on:click="$dispatch('open-bank-request')"
                class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 text-zinc-700 dark:text-zinc-300 hover:border-brand-500 hover:text-brand-600 dark:hover:text-brand-400 transition-all font-black uppercase tracking-widest text-[10px]">
            <flux:icon name="envelope" class="size-4" />
            Pedir acesso a uma empresa
        </button>
    </div>

    {{-- MODAL DE PEDIDO DE CREDENCIAIS --}}
    <div x-data="{ open: false }" x-on:open-bank-request.window="open = true" x-show="open" x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/70 backdrop-blur-sm">
        <div x-show="open" x-transition @click.outside="open = false"
             class="w-full max-w-2xl max-h-[90vh] overflow-hidden bg-white dark:bg-zinc-900 rounded-[2rem] shadow-2xl border border-zinc-200 dark:border-zinc-800 flex flex-col">

            <div class="px-7 py-6 border-b border-zinc-100 dark:border-zinc-800 flex items-start justify-between shrink-0">
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.25em] text-brand-600 dark:text-brand-400">Acesso bancário</p>
                    <h2 class="mt-1 text-2xl font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">Pedir credenciais</h2>
                    <p class="mt-1 text-xs text-zinc-500">Seleciona a empresa e indica o email para onde será enviado o pedido automático.</p>
                </div>
                <button type="button" @click="open = false" class="p-2 text-zinc-400 hover:text-zinc-900 dark:hover:text-white">
                    <flux:icon name="x-mark" class="size-5" />
                </button>
            </div>

            <div class="overflow-y-auto p-7 space-y-6">
                @if($requestSent)
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 dark:border-emerald-500/20 dark:bg-emerald-500/10 p-5 text-center">
                        <flux:icon name="check-circle" class="size-8 mx-auto text-emerald-600 dark:text-emerald-400" />
                        <p class="mt-3 text-sm font-black text-emerald-700 dark:text-emerald-300">Pedido enviado com sucesso</p>
                        <p class="mt-1 text-xs text-emerald-700/70 dark:text-emerald-300/70">A mensagem automática foi enviada para o email indicado.</p>
                    </div>
                @endif

                <div class="space-y-2">
                    <label class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400">Pesquisar empresa</label>
                    <div class="relative">
                        <flux:icon name="magnifying-glass" class="absolute left-4 top-1/2 -translate-y-1/2 size-4 text-zinc-400" />
                        <input wire:model.live.debounce.300ms="companySearch" type="search" placeholder="Nome da empresa..."
                               class="w-full h-12 pl-11 pr-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-sm font-semibold outline-none focus:ring-2 focus:ring-brand-500 dark:text-white" />
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400">Empresas registadas</label>
                    <div class="max-h-64 overflow-y-auto rounded-2xl border border-zinc-200 dark:border-zinc-800 divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse($this->companies as $company)
                            <button type="button" wire:click="selectCompany({{ $company->id }})"
                                    class="w-full text-left px-5 py-4 flex items-center justify-between gap-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-colors {{ $selectedCompanyId === $company->id ? 'bg-brand-50 dark:bg-brand-500/10 ring-1 ring-inset ring-brand-500/30' : '' }}">
                                <div class="min-w-0">
                                    <p class="font-black text-sm text-zinc-900 dark:text-white truncate">{{ $company->legal_name ?: $company->name }}</p>
                                    @if($company->legal_name && $company->legal_name !== $company->name)
                                        <p class="text-[10px] text-zinc-500 truncate">{{ $company->name }}</p>
                                    @endif
                                </div>
                                @if($selectedCompanyId === $company->id)
                                    <flux:icon name="check-circle" class="size-5 shrink-0 text-brand-600 dark:text-brand-400" />
                                @else
                                    <flux:icon name="chevron-right" class="size-4 shrink-0 text-zinc-300" />
                                @endif
                            </button>
                        @empty
                            <div class="p-8 text-center text-xs font-semibold text-zinc-400">Nenhuma empresa encontrada.</div>
                        @endforelse
                    </div>
                    @error('selectedCompanyId') <p class="text-xs text-red-500 font-semibold">{{ $message }}</p> @enderror
                </div>

                @if($selectedCompanyId)
                    @php($selectedCompany = $this->companies->firstWhere('id', $selectedCompanyId))
                    <div class="rounded-2xl bg-brand-50 dark:bg-brand-500/10 border border-brand-200 dark:border-brand-500/20 p-4">
                        <p class="text-[9px] font-black uppercase tracking-widest text-brand-600 dark:text-brand-400">Empresa selecionada</p>
                        <p class="mt-1 font-black text-zinc-900 dark:text-white">{{ $selectedCompany?->legal_name ?: $selectedCompany?->name }}</p>
                    </div>
                @endif

                <div class="space-y-2">
                    <label class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400">Email de destino</label>
                    <input wire:model="requestEmail" type="email" autocomplete="email" placeholder="exemplo@banco.pt"
                           class="w-full h-12 px-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-sm font-semibold outline-none focus:ring-2 focus:ring-brand-500 dark:text-white" />
                    @error('requestEmail') <p class="text-xs text-red-500 font-semibold">{{ $message }}</p> @enderror
                    <p class="text-[10px] text-zinc-400">O pedido automático será enviado para este endereço.</p>
                </div>

                <button type="button" wire:click="sendAccessRequest" wire:loading.attr="disabled"
                        class="w-full h-14 rounded-2xl bg-brand-600 hover:bg-brand-500 disabled:opacity-50 text-white font-black uppercase tracking-widest text-[10px] shadow-lg shadow-brand-500/20 transition-all">
                    <span wire:loading.remove wire:target="sendAccessRequest">Enviar pedido automático</span>
                    <span wire:loading wire:target="sendAccessRequest">A enviar...</span>
                </button>
            </div>
        </div>
    </div>

    <div class="text-center pt-2 border-t border-zinc-100 dark:border-zinc-800/50">
        <a href="/" class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.3em] hover:text-zinc-900 transition-colors">
            ← Voltar ao site principal
        </a>
    </div>
</div>
