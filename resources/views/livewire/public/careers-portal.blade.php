<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 p-6 md:p-12 text-left">
    <div class="max-w-[1400px] mx-auto space-y-10">

        {{-- 1. HEADER --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-8 rounded-[3rem] shadow-xl relative overflow-hidden flex flex-col md:flex-row justify-between items-center gap-6 text-left w-full">
            <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/5 blur-[100px] rounded-full -mr-32 -mt-32"></div>
            <div class="text-left relative z-10">
                <h1 class="text-4xl font-black dark:text-white uppercase italic tracking-tighter leading-none text-left">Oportunidades no Grupo</h1>
                <p class="text-sm text-zinc-500 mt-2 font-medium italic text-left">
                    Olá, <span class="text-emerald-600 font-black">{{ auth()->user()->name }}</span>. Gere aqui as tuas candidaturas.
                </p>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="z-10">
                @csrf
                <button type="submit" class="px-8 py-3 bg-red-500/10 text-red-500 hover:bg-red-600 hover:text-white transition-all rounded-2xl text-[10px] font-black uppercase tracking-widest italic shadow-sm">
                    Sair da Conta
                </button>
            </form>
        </div>

      {{-- 2. GRELHA DE EMPRESAS --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 items-start">
    @foreach($companies as $company)
        @php $alreadyApplied = in_array($company->id, $userAppliedIds); @endphp

        <div wire:key="company-{{ $company->id }}"
             class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[3rem] shadow-sm hover:border-brand-500 transition-all flex flex-col group relative overflow-hidden h-full text-left">

            {{-- ✅ INFORMAÇÃO DE ENVIO: TOPO E CENTRO --}}
            @if($alreadyApplied)
                <div class="absolute top-4 left-1/2 -translate-x-1/2 z-20">
                    <div class="px-4 py-1.5 bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 rounded-full flex items-center gap-2 shadow-sm backdrop-blur-md animate-in slide-in-from-top-2 duration-500">
                        <flux:icon name="check-circle" variant="solid" class="size-3" />
                        <span class="text-[8px] font-black uppercase tracking-[0.2em]">Candidatura Enviada</span>
                    </div>
                </div>
            @endif

            <div class="p-8 space-y-6 flex-1 flex flex-col">
                <div class="flex justify-between items-start">
                    {{-- Logo --}}
                    <div class="size-16 rounded-2xl bg-zinc-50 dark:bg-zinc-950 flex items-center justify-center text-brand-600 font-black text-2xl shadow-inner border border-zinc-100">
                         @if($company->logo_path)
                            <img src="{{ asset('storage/' . $company->logo_path) }}" class="w-full h-full object-cover rounded-2xl">
                         @else
                            {{ substr($company->name, 0, 1) }}
                         @endif
                    </div>

                    @if($company->recruitment_active)
                        <span class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 text-[8px] font-black uppercase tracking-widest rounded-lg border border-zinc-200 dark:border-zinc-700">Vagas Abertas</span>
                    @endif
                </div>

                <div class="text-left space-y-4 flex-1">
                    <h3 class="text-2xl font-black dark:text-white uppercase tracking-tight leading-tight group-hover:text-brand-600 transition-colors">{{ $company->name }}</h3>

                    @if($company->recruitment_announcement)
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-brand-600 text-white rounded-xl shadow-lg">
                            <flux:icon name="megaphone" variant="micro" class="size-3 animate-pulse" />
                            <span class="text-[9px] font-black uppercase tracking-tighter">{{ $company->recruitment_announcement }}</span>
                        </div>
                    @endif

                    <p class="text-xs text-zinc-500 leading-relaxed italic border-t border-zinc-50 dark:border-zinc-800/50 pt-4 line-clamp-4">
                        {{ $company->recruitment_description ?? 'Unidade de negócio focada em excelência e inovação estratégica.' }}
                    </p>
                </div>

                <button wire:click="openDetails({{ $company->id }})" class="text-[9px] font-black uppercase tracking-[0.2em] text-zinc-400 hover:text-brand-600 transition-colors text-left w-fit mt-4">
                    [+] Mais Informação
                </button>
            </div>

            <div class="p-8 pt-0 mt-auto">
                <button wire:click="openApplication({{ $company->id }}, '{{ $company->name }}')"
                        class="w-full py-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-lg active:scale-95 transition-all">
                    {{ $alreadyApplied ? 'Rever Candidatura ' : 'Enviar Candidatura' }}
                </button>
            </div>
        </div>
    @endforeach
</div>
    </div>

    {{-- 4. MODAL: DOSSIÊ COMPLETO (FIX: CENTRADO E COM SCROLL) --}}
    <flux:modal name="company-details-modal" variant="center" position="center" class="md:w-[750px] !p-0 overflow-hidden" wire:ignore.self>
        {{-- Container com Max-Height e Scroll --}}
        <div class="relative bg-white dark:bg-zinc-950 rounded-[3rem] shadow-2xl border dark:border-zinc-800 text-left flex flex-col max-h-[90vh]">

            @if($viewingCompany)
                {{-- Botão Fechar Fixo no topo --}}
                <div class="absolute top-8 right-8 z-50">
                    <flux:modal.close><flux:button variant="ghost" size="sm" icon="x-mark" class="rounded-full shadow-sm bg-white/50 backdrop-blur-md" /></flux:modal.close>
                </div>

                {{-- Conteúdo com Scroll --}}
                <div class="p-12 overflow-y-auto custom-scrollbar space-y-10">

                    {{-- Header Modal --}}
                    <div class="flex items-center gap-8 border-b border-zinc-100 dark:border-zinc-800 pb-10">
                        <div class="size-24 rounded-[2rem] bg-zinc-50 dark:bg-zinc-900 flex items-center justify-center text-brand-600 font-black text-4xl shadow-inner border border-zinc-100 dark:border-zinc-800 shrink-0">
                            @if($viewingCompany->logo_path) <img src="{{ asset('storage/' . $viewingCompany->logo_path) }}" class="w-full h-full object-cover rounded-[2rem]">
                            @else {{ substr($viewingCompany->name, 0, 1) }} @endif
                        </div>
                        <div>
                            <h2 class="text-4xl font-black dark:text-white uppercase italic tracking-tighter leading-none">{{ $viewingCompany->name }}</h2>
                            <div class="flex items-center gap-4 mt-4">
                                <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest flex items-center gap-2">
                                    <flux:icon name="building-office" variant="micro" class="size-3" /> {{ $viewingCompany->industry ?? 'Empresa do Grupo' }}
                                </span>
                                <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest flex items-center gap-2">
                                    <flux:icon name="map-pin" variant="micro" class="size-3" /> Portugal
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        {{-- Anúncio --}}
                        @if($viewingCompany->recruitment_announcement)
                            <div class="p-6 bg-brand-600 rounded-[1.5rem] shadow-xl shadow-brand-500/20 flex items-start gap-4">
                                <flux:icon name="megaphone" variant="solid" class="size-6 text-white shrink-0 mt-1 animate-pulse" />
                                <p class="text-sm font-black text-white uppercase tracking-tight leading-relaxed italic">{{ $viewingCompany->recruitment_announcement }}</p>
                            </div>
                        @endif

                        {{-- Descrição --}}
                        <div class="space-y-4">
                            <h4 class="text-[10px] font-black uppercase text-zinc-400 tracking-[0.4em]">Biografia Corporativa</h4>
                            <p class="text-base text-zinc-600 dark:text-zinc-400 leading-loose italic">
                                {{ $viewingCompany->recruitment_description }}
                            </p>
                        </div>

                        {{-- Info Extra (Dossiê) --}}
                        @if($viewingCompany->recruitment_extra_info)
                            <div class="p-8 bg-zinc-50 dark:bg-zinc-900/50 rounded-[2.5rem] border border-zinc-100 dark:border-zinc-800 space-y-4">
                                <h4 class="text-[10px] font-black text-brand-600 uppercase tracking-[0.3em]">Dossiê de Recrutamento & Benefícios</h4>
                                <div class="text-sm text-zinc-600 dark:text-zinc-400 leading-loose italic">
                                    {!! nl2br(e($viewingCompany->recruitment_extra_info)) !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Footer Modal Fixo --}}
                <div class="p-10 border-t border-zinc-100 dark:border-zinc-800 bg-white/50 dark:bg-zinc-900/50 backdrop-blur-md flex gap-4">
                    <flux:modal.close class="flex-1"><flux:button variant="ghost" class="w-full h-14 rounded-2xl font-black uppercase text-[10px]">Fechar Dossiê</flux:button></flux:modal.close>
                    @if($viewingCompany->recruitment_active)
                        <flux:button wire:click="openApplication({{ $viewingCompany->id }}, '{{ $viewingCompany->name }}')" variant="primary" class="flex-[2] h-14 bg-brand-600 text-white rounded-2xl font-black uppercase shadow-xl border-none active:scale-95">
                            Preencher Candidatura
                        </flux:button>
                    @endif
                </div>
            @endif
        </div>
    </flux:modal>

   {{-- 5. MODAL: FORMULÁRIO DE CANDIDATURA (COMPLETO) --}}
<flux:modal name="apply-form-modal" variant="center" position="center" class="md:w-[650px] !p-0 overflow-hidden" wire:ignore.self>
    <div class="relative bg-white dark:bg-zinc-950 rounded-[2.5rem] shadow-2xl border dark:border-zinc-800 text-left flex flex-col max-h-[90vh]">

        {{-- Botão Fechar --}}
        <div class="absolute top-6 right-6 z-50">
            <flux:modal.close><flux:button variant="ghost" size="sm" icon="x-mark" class="rounded-full shadow-sm bg-white/50 backdrop-blur-md" /></flux:modal.close>
        </div>

        <div class="p-10 overflow-y-auto custom-scrollbar space-y-8">

            {{-- CABEÇALHO --}}
            <div class="flex items-center gap-4 text-left">
                <div class="p-3 {{ $hasApplied ? 'bg-zinc-900' : 'bg-brand-600' }} rounded-2xl text-white shadow-lg shadow-brand-500/20">
                    <flux:icon name="{{ $hasApplied ? 'eye' : 'user-plus' }}" class="size-6" />
                </div>
                <div>
                    <flux:heading size="xl" class="font-black uppercase italic tracking-tighter text-zinc-900 dark:text-white leading-none">
                        {{ $hasApplied ? 'Tua Candidatura' : 'Nova Candidatura' }}
                    </flux:heading>
                    <p class="text-[10px] font-black text-brand-600 uppercase mt-1 tracking-widest text-left">{{ $selectedCompanyName }}</p>
                </div>
            </div>

            @if($hasApplied)
                {{-- --- VISTA DE REVISÃO --- --}}
                <div class="space-y-6">
                    <div class="p-8 bg-zinc-50 dark:bg-zinc-900/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-800 space-y-6 text-left">

                        {{-- DADOS DA CONTA --}}
                        <div class="space-y-3">
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.3em]">Dados da tua Conta</p>
                            <div class="grid grid-cols-2 gap-4">
                                <div><p class="text-[8px] font-black text-zinc-400 uppercase">Nome</p><p class="text-sm font-bold dark:text-white">{{ auth()->user()->name }}</p></div>
                                <div><p class="text-[8px] font-black text-zinc-400 uppercase">Email</p><p class="text-sm font-bold dark:text-white">{{ auth()->user()->email }}</p></div>
                            </div>
                        </div>

                        <div class="h-px bg-zinc-200 dark:bg-zinc-800"></div>

                        {{-- DADOS DA CANDIDATURA --}}
                        <div class="space-y-4">
                            <p class="text-[9px] font-black text-brand-600 uppercase tracking-[0.3em]">Detalhes da Submissão</p>
                            <div><p class="text-[8px] font-black text-zinc-400 uppercase">Cargo / Vaga</p><p class="text-sm font-black dark:text-white uppercase">{{ $role_applied }}</p></div>
                            <div><p class="text-[8px] font-black text-zinc-400 uppercase">Contacto</p><p class="text-sm font-black dark:text-white">{{ $phone }}</p></div>
                            <div><p class="text-[8px] font-black text-zinc-400 uppercase">Apresentação</p><p class="text-xs text-zinc-500 italic leading-relaxed">"{{ $notes ?: 'Sem nota de apresentação.' }}"</p></div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-4 bg-emerald-500/10 rounded-2xl border border-emerald-500/20">
                        <div class="size-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Estado: Candidatura em análise oficial</p>
                    </div>

                    <flux:button x-on:click="$dispatch('modal-close', {name: 'apply-form-modal'})" variant="ghost" class="w-full font-black uppercase text-[10px] h-14 rounded-2xl">Fechar Dossiê</flux:button>
                </div>
            @else
                {{-- --- VISTA DE FORMULÁRIO --- --}}
                <form wire:submit.prevent="submitApplication" class="space-y-8">

                    {{-- DADOS DA CONTA (SINCRO AUTOMÁTICA) --}}
                    <div class="p-6 bg-zinc-50 dark:bg-zinc-900 rounded-[2rem] border border-zinc-100 dark:border-zinc-800 space-y-4">
                        <div class="flex items-center gap-2">
                            <flux:icon name="user" variant="micro" class="size-3 text-zinc-400" />
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Informação do teu Perfil</p>
                        </div>
                        <div class="grid grid-cols-2 gap-6">
                            <div><p class="text-[8px] font-black text-zinc-400 uppercase mb-1">Nome Completo</p><p class="text-xs font-bold dark:text-zinc-200">{{ auth()->user()->name }}</p></div>
                            <div><p class="text-[8px] font-black text-zinc-400 uppercase mb-1">Email de Registo</p><p class="text-xs font-bold dark:text-zinc-200">{{ auth()->user()->email }}</p></div>
                        </div>
                    </div>

                    {{-- CAMPOS INTERATIVOS --}}
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <flux:input wire:model="role_applied" label="Cargo / Função Pretendida" placeholder="Ex: Gestor de Projectos" class="font-bold h-12" />
                            <flux:input wire:model="phone" label="Telemóvel de Contacto" placeholder="9xxxxxxxx" class="font-bold h-12" />
                        </div>

                        <flux:textarea wire:model="notes" label="Porquê esta Unidade de Negócio?" placeholder="Explica a tua motivação..." rows="3" />

                        {{-- ZONA DE UPLOAD --}}
                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase text-zinc-400 tracking-widest ml-1">Anexar Currículo (PDF)</label>
                            <div class="relative group">
                                <input type="file" wire:model="cv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                                <div class="p-10 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-[2rem] text-center group-hover:border-brand-500 transition-all bg-zinc-50 dark:bg-zinc-950 shadow-inner">
                                    <flux:icon name="cloud-arrow-up" class="size-10 text-zinc-300 mx-auto mb-3" />
                                    <p class="text-[10px] font-black uppercase text-zinc-500">
                                        @if($cv) <span class="text-brand-600 italic">{{ $cv->getClientOriginalName() }}</span> @else Selecionar Ficheiro Profissional @endif
                                    </p>
                                </div>
                            </div>
                            @error('cv') <p class="text-[9px] text-red-500 font-bold uppercase mt-1 italic">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <flux:button type="submit" variant="primary" class="w-full h-16 bg-emerald-600 text-white rounded-2xl font-black uppercase shadow-xl shadow-brand-500/20 border-none transition-all active:scale-95">
                        Confirmar Submissão de Talento
                    </flux:button>
                </form>
            @endif
        </div>
    </div>
</flux:modal>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e4e4e7; border-radius: 10px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #27272a; }
</style>
