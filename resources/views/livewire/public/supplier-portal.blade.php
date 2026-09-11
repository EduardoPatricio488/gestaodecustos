<div class="w-full max-w-lg bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-8 sm:p-14 rounded-[3.5rem] shadow-2xl backdrop-blur-md space-y-8 relative mx-auto">
    <div class="flex justify-center">
        <div class="size-20 bg-emerald-600 rounded-2xl shadow-xl shadow-emerald-500/20 flex items-center justify-center">
            <flux:icon name="{{ $isLoggedIn ? 'document-text' : 'building-storefront' }}" variant="solid" class="size-10 text-white" />
        </div>
    </div>

    <div class="text-center space-y-2">
        <h1 class="text-2xl font-black text-zinc-900 dark:text-white uppercase italic tracking-tighter leading-none">
            {{ $isLoggedIn ? 'Submeter Faturação' : 'Portal do Fornecedor' }}
        </h1>
        <p class="text-[11px] text-zinc-500 font-medium italic text-center w-full">
            {{ $isLoggedIn ? 'Parceiro: ' . $supplier->name : 'Acede à área privada do fornecedor.' }}
        </p>
    </div>

    @if (!$isLoggedIn)
        @if (session()->has('error'))
            <div class="p-3 bg-red-500/10 text-red-500 text-[10px] font-bold rounded-xl border border-red-500/20 text-center uppercase tracking-widest italic">
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit.prevent="login" class="space-y-5">
            <div class="space-y-2" x-data="{ nif: @entangle('tax_number'), formatNIF(value) { return (value || '').replace(/\D/g, '').replace(/(\d{3})(?=\d)/g, '$1 ').substring(0, 11); } }" x-init="nif = formatNIF(nif)">
                <label class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1">NIF da Empresa</label>
                <input type="text" x-model="nif" x-on:input="nif = formatNIF($event.target.value)" inputmode="numeric" maxlength="11" placeholder="000 000 000" class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-center font-mono font-bold text-sm tracking-[0.1em] focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none dark:text-white" />
            </div>

            <div class="space-y-2">
                <label class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 ml-1">Chave de Parceiro</label>
                <input wire:model="token" type="text" inputmode="numeric" maxlength="6" placeholder="000000" autocomplete="one-time-code" class="w-full h-12 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-center font-mono font-black text-lg tracking-[0.4em] focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none dark:text-white" />
            </div>

            <flux:button type="submit" variant="primary" class="w-full h-14 !bg-emerald-600 hover:!bg-emerald-500 rounded-2xl font-black uppercase tracking-widest text-[11px] shadow-lg shadow-emerald-500/20 text-white border-none">
                Entrar no Portal
            </flux:button>
        </form>

        <div class="pt-5 border-t border-zinc-100 dark:border-zinc-800/50" x-data="{ open: false }">
            <button type="button" @click="open = true" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 text-zinc-700 dark:text-zinc-300 hover:border-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all font-black uppercase tracking-widest text-[10px]">
                <flux:icon name="shield-check" class="size-4" />
                Solicitar acesso
            </button>
            <p class="text-center text-[9px] text-zinc-400 font-semibold mt-2">Ainda não tens credenciais? Envia um pedido à empresa.</p>

            <div x-show="open" x-cloak x-transition class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/70 backdrop-blur-sm" @keydown.escape.window="open = false">
                <div @click.stop class="w-full max-w-2xl max-h-[90vh] overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] shadow-2xl flex flex-col">
                    <div class="px-7 py-6 border-b border-zinc-100 dark:border-zinc-800 flex items-start justify-between shrink-0">
                        <div>
                            <p class="text-[9px] font-black uppercase tracking-[0.25em] text-emerald-600 dark:text-emerald-400">Acesso do fornecedor</p>
                            <h2 class="mt-1 text-2xl font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white">Solicitar acesso</h2>
                            <p class="mt-1 text-xs text-zinc-500">Seleciona a empresa e envia os teus dados para o email empresarial registado.</p>
                        </div>
                        <button type="button" @click="open = false" class="p-2 text-zinc-400 hover:text-zinc-900 dark:hover:text-white" aria-label="Fechar">
                            <flux:icon name="x-mark" class="size-5" />
                        </button>
                    </div>

                    <div class="overflow-y-auto p-7 space-y-6">
                        @if($requestSent)
                            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 dark:border-emerald-500/20 dark:bg-emerald-500/10 p-5 text-center">
                                <flux:icon name="check-circle" class="size-8 mx-auto text-emerald-600 dark:text-emerald-400" />
                                <p class="mt-3 text-sm font-black text-emerald-700 dark:text-emerald-300">Pedido enviado para a empresa</p>
                                <p class="mt-1 text-xs text-emerald-700/70 dark:text-emerald-300/70">A empresa foi notificada. Depois de validar o pedido, deverá fornecer as credenciais do portal.</p>
                            </div>
                        @endif

                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400">Pesquisar empresa</label>
                            <div class="relative">
                                <flux:icon name="magnifying-glass" class="absolute left-4 top-1/2 -translate-y-1/2 size-4 text-zinc-400" />
                                <input wire:model.live.debounce.300ms="companySearch" type="search" placeholder="Nome da empresa..." class="w-full h-12 pl-11 pr-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-sm font-semibold outline-none focus:ring-2 focus:ring-emerald-500 dark:text-white" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400">Empresas registadas</label>
                            <div class="max-h-52 overflow-y-auto rounded-2xl border border-zinc-200 dark:border-zinc-800 divide-y divide-zinc-100 dark:divide-zinc-800">
                                @forelse($this->companies as $company)
                                    <button type="button" wire:click="selectCompany({{ $company->id }})" class="w-full text-left px-5 py-4 flex items-center justify-between gap-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-colors {{ $selectedCompanyId === $company->id ? 'bg-emerald-50 dark:bg-emerald-500/10 ring-1 ring-inset ring-emerald-500/30' : '' }}">
                                        <div class="min-w-0">
                                            <p class="font-black text-sm text-zinc-900 dark:text-white truncate">{{ $company->legal_name ?: $company->name }}</p>
                                            @if($company->legal_name && $company->legal_name !== $company->name)
                                                <p class="text-[10px] text-zinc-500 truncate">{{ $company->name }}</p>
                                            @endif
                                        </div>
                                        @if($selectedCompanyId === $company->id)
                                            <flux:icon name="check-circle" class="size-5 shrink-0 text-emerald-600" />
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
                            <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 p-4">
                                <p class="text-[9px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Empresa selecionada</p>
                                <p class="mt-1 font-black text-zinc-900 dark:text-white">{{ $selectedCompany?->legal_name ?: $selectedCompany?->name }}</p>
                            </div>
                        @endif

                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400">Nome</label>
                                <input wire:model="requesterName" type="text" autocomplete="name" placeholder="O teu nome" class="w-full h-12 px-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-sm font-semibold outline-none focus:ring-2 focus:ring-emerald-500 dark:text-white" />
                                @error('requesterName') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400">Email</label>
                                <input wire:model="requesterEmail" type="email" autocomplete="email" placeholder="nome@empresa.pt" class="w-full h-12 px-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-sm font-semibold outline-none focus:ring-2 focus:ring-emerald-500 dark:text-white" />
                                @error('requesterEmail') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400">NIF (opcional)</label>
                            <input wire:model="requestTaxNumber" type="text" inputmode="numeric" maxlength="11" placeholder="000 000 000" class="w-full h-12 px-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-sm font-mono font-bold outline-none focus:ring-2 focus:ring-emerald-500 dark:text-white" />
                        </div>

                        <button type="button" wire:click="sendAccessRequest" wire:loading.attr="disabled" class="w-full h-14 rounded-2xl bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white font-black uppercase tracking-widest text-[10px] shadow-lg shadow-emerald-500/20 transition-all">
                            <span wire:loading.remove wire:target="sendAccessRequest">Enviar pedido à empresa</span>
                            <span wire:loading wire:target="sendAccessRequest">A enviar...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <form wire:submit.prevent="submitInvoice" class="space-y-6">
            <flux:button type="submit" variant="primary" class="w-full h-14 !bg-emerald-600 rounded-2xl font-black uppercase">Submeter Documento</flux:button>
            <button type="button" wire:click="$set('isLoggedIn', false)" class="w-full text-center text-[10px] font-black text-zinc-400 uppercase tracking-widest hover:text-red-500 transition-colors">Sair da Sessão</button>
        </form>
    @endif

    <div class="text-center pt-2 border-t border-zinc-100 dark:border-zinc-800/50">
        <a href="/" class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.3em] hover:text-emerald-600 transition-colors">← Voltar ao site principal</a>
    </div>
</div>
