<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 p-6 md:p-12 text-left">
    <div class="max-w-[1400px] mx-auto space-y-10">

        {{-- 1. HEADER: TITULO --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-10 rounded-[3rem] shadow-xl relative overflow-hidden text-left w-full">
            <div class="absolute top-0 right-0 w-64 h-64 bg-brand-500/5 blur-[100px] rounded-full -mr-32 -mt-32"></div>

            <div class="flex flex-col md:flex-row justify-between items-center gap-8 relative z-10">
                <div>
                    <h1 class="text-4xl font-black dark:text-white uppercase italic tracking-tighter leading-none">Oportunidades no Grupo</h1>
                    <p class="text-sm text-zinc-500 mt-2 font-medium italic">Explora as empresas do nosso ecossistema e submete o teu perfil.</p>
                </div>

                {{-- SAIR DA CONTA DE CANDIDATO --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-8 py-3 bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition-all rounded-2xl text-[10px] font-black uppercase tracking-widest italic">
                        Encerrar Sessão
                    </button>
                </form>
            </div>
        </div>

        {{-- 2. GRELHA DE EMPRESAS (Puxa da BD) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($companies as $company)
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-8 rounded-[3rem] shadow-sm hover:border-brand-500 transition-all flex flex-col group relative">

                    <div class="flex justify-between items-start mb-8">
                        {{-- Logo ou Letra --}}
                        <div class="size-16 rounded-2xl bg-zinc-50 dark:bg-zinc-950 flex items-center justify-center text-brand-600 font-black text-2xl shadow-inner border border-zinc-100 dark:border-zinc-800">
                             @if($company->logo_url) <img src="{{ $company->logo_url }}" class="w-full h-full object-cover rounded-2xl">
                             @else {{ substr($company->name, 0, 1) }} @endif
                        </div>
                        <span class="px-3 py-1 bg-brand-500/10 text-brand-600 text-[8px] font-black uppercase tracking-widest rounded-lg border border-brand-500/20">Vagas Abertas</span>
                    </div>

                    <div class="space-y-4 flex-1 text-left">
                        <h3 class="text-2xl font-black dark:text-white uppercase tracking-tight leading-tight">{{ $company->name }}</h3>

                        <div class="flex flex-col gap-2">
                            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest flex items-center gap-2">
                                <flux:icon name="building-office" variant="micro" class="size-3" />
                                {{ $company->industry ?? 'Serviços & Operações' }}
                            </p>
                            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest flex items-center gap-2">
                                <flux:icon name="map-pin" variant="micro" class="size-3" />
                                Sede: Portugal
                            </p>
                        </div>

                        <p class="text-xs text-zinc-500 leading-relaxed line-clamp-3 italic">
                            Esta unidade de negócio foca-se na excelência operacional e crescimento estratégico do grupo.
                        </p>
                    </div>

                    <div class="mt-10">
                        <button
                            wire:click="openApplication({{ $company->id }}, '{{ $company->name }}')"
                            class="w-full py-4 bg-brand-600 hover:bg-brand-500 text-white rounded-2xl font-black uppercase text-xs tracking-[0.1em] shadow-lg shadow-brand-500/20 active:scale-95 transition-all"
                        >
                            Preencher Candidatura
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-24 text-center opacity-30">
                    <flux:icon name="building-office" class="size-16 mx-auto mb-4" />
                    <p class="text-xs font-black uppercase">Nenhuma empresa registada para recrutamento.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 3. MODAL: FORMULÁRIO DE CANDIDATURA --}}
    <flux:modal name="apply-form-modal" position="center" class="md:w-[550px] !p-0 overflow-visible" wire:ignore.self>
        <div class="relative p-10 bg-white dark:bg-zinc-950 rounded-[2.5rem] space-y-8 shadow-2xl border dark:border-zinc-800 text-left">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-brand-600 rounded-2xl text-white shadow-lg shadow-brand-500/20"><flux:icon name="user-plus" class="size-6" /></div>
                <div>
                    <flux:heading size="xl" class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white leading-none">Candidatura Oficial</flux:heading>
                    <p class="text-[10px] font-black text-brand-600 uppercase mt-1 tracking-widest">{{ $selectedCompanyName }}</p>
                </div>
            </div>

            <form wire:submit.prevent="submitApplication" class="space-y-6">
                <div class="space-y-4">
                    <flux:input wire:model="role_applied" label="Cargo Pretendido" placeholder="Ex: Gestor de Projectos / Developer" class="font-bold" />
                    <flux:input wire:model="phone" label="Telemóvel de Contacto" placeholder="9xx xxx xxx" class="font-bold" />
                    <flux:textarea wire:model="notes" label="Apresentação (Opcional)" placeholder="Diz-nos porque queres fazer parte do grupo..." rows="3" />

                    {{-- Upload CV --}}
                    <div class="space-y-2">
                        <label class="text-[9px] font-black uppercase text-zinc-400 tracking-widest">Currículo Vitae (PDF)</label>
                        <div class="relative group">
                            <input type="file" wire:model="cv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="p-8 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl text-center group-hover:border-brand-500 transition-all bg-zinc-50 dark:bg-zinc-950">
                                <flux:icon name="cloud-arrow-up" class="size-8 text-zinc-300 mx-auto mb-2" />
                                <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">
                                    @if($cv) <span class="text-brand-600">{{ $cv->getClientOriginalName() }}</span> @else Carregar PDF @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 pt-2">
                    <flux:modal.close class="flex-1"><flux:button variant="ghost" class="w-full font-black uppercase text-[10px]">Cancelar</flux:button></flux:modal.close>
                    <flux:button type="submit" variant="primary" class="flex-[2] h-14 bg-brand-600 text-white rounded-2xl font-black uppercase text-xs shadow-xl shadow-brand-500/20 border-none">Submeter Perfil</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
