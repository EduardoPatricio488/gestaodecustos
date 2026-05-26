<div class="space-y-8">
    {{-- HEADER --}}
    <x-page-header title="Definições do Site" description="Gere as regras globais, identidade e estado da plataforma.">
        <x-slot:actions>
            <flux:button wire:click="save" variant="primary" icon="check" class="shadow-lg shadow-brand-500/20 px-8 font-black uppercase tracking-widest">
                Guardar Configurações
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- COLUNA 1: IDENTIDADE --}}
        <div class="space-y-4">
            <div class="flex items-center gap-2 px-2">
                <flux:icon name="pencil-square" class="text-zinc-400 size-4" />
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-500">Identidade</h2>
            </div>
            <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <flux:input wire:model="site_name" label="Nome da Plataforma" />

                <flux:select wire:model="default_currency" label="Moeda Padrão">
                    <option value="EUR">€ Euro (Portugal)</option>
                    <option value="USD">$ Dólar Americano</option>
                    <option value="BRL">R$ Real Brasileiro</option>
                    <option value="GBP">£ Libra Esterlina</option>
                </flux:select>
            </div>
        </div>

        {{-- COLUNA 2: ESTADO DO SISTEMA --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center gap-2 px-2">
                <flux:icon name="shield-check" class="text-zinc-400 size-4" />
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-500">Estado da Plataforma</h2>
            </div>

            <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-4">

                {{-- 1. MODO DE MANUTENÇÃO --}}
                <div class="p-6 rounded-3xl flex items-center justify-between {{ $maintenance_mode ? 'bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800' : 'bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-800' }}">
                    <div class="flex items-center gap-4">
                        <div class="p-3 rounded-2xl {{ $maintenance_mode ? 'bg-red-100 dark:bg-red-500/20 text-red-600' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-500' }}">
                            <flux:icon name="wrench-screwdriver" variant="outline" class="size-6" />
                        </div>
                        <div>
                            <flux:heading size="md" class="font-bold">Modo de Manutenção</flux:heading>
                            <p class="text-xs text-zinc-500">Estado: <strong class="{{ $maintenance_mode ? 'text-red-600' : 'text-emerald-600' }}">{{ $maintenance_mode ? 'BLOQUEADO' : 'ONLINE' }}</strong></p>
                        </div>
                    </div>
                    <flux:modal.trigger name="confirm-maintenance">
                        <flux:button variant="{{ $maintenance_mode ? 'danger' : 'filled' }}" class="font-black uppercase tracking-widest text-[10px]">
                            {{ $maintenance_mode ? 'Desativar' : 'Ativar' }}
                        </flux:button>
                    </flux:modal.trigger>
                </div>

                {{-- 2. NOVOS REGISTOS --}}
                <div class="p-6 rounded-3xl flex items-center justify-between {{ !$allow_registration ? 'bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800' : 'bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-800' }}">
                    <div class="flex items-center gap-4">
                        <div class="p-3 rounded-2xl {{ !$allow_registration ? 'bg-amber-100 dark:bg-amber-500/20 text-amber-600' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-500' }}">
                            <flux:icon name="user-plus" variant="outline" class="size-6" />
                        </div>
                        <div>
                            <flux:heading size="md" class="font-bold">Novos Registos</flux:heading>
                            <p class="text-xs text-zinc-500">Estado: <strong class="{{ $allow_registration ? 'text-emerald-600' : 'text-amber-600' }}">{{ $allow_registration ? 'ABERTOS' : 'FECHADOS' }}</strong></p>
                        </div>
                    </div>
                    <flux:modal.trigger name="confirm-registration">
                        <flux:button variant="{{ !$allow_registration ? 'filled' : 'filled' }}" class="font-black uppercase tracking-widest text-[10px]">
                            {{ $allow_registration ? 'Fechar' : 'Abrir' }}
                        </flux:button>
                    </flux:modal.trigger>
                </div>


            </div>
        </div>
    </div>

    {{-- MODAL: MANUTENÇÃO --}}
    <flux:modal name="confirm-maintenance" class="md:w-[450px] space-y-6">
        <div class="text-center space-y-4">
            <div class="inline-flex p-4 bg-red-50 dark:bg-red-900/20 rounded-full">
                <flux:icon name="exclamation-triangle" class="size-10 text-red-600" />
            </div>
            <div>
                <flux:heading size="lg" class="font-black italic uppercase">Confirmar Manutenção?</flux:heading>
                <p class="text-sm text-zinc-500 mt-2">
                    Estás prestes a <strong>{{ $maintenance_mode ? 'DESATIVAR' : 'ATIVAR' }}</strong> o modo de manutenção global.
                </p>
            </div>
        </div>
        <div class="flex gap-3">
            <flux:modal.close><flux:button variant="ghost" class="flex-1">Cancelar</flux:button></flux:modal.close>
            <flux:button wire:click="toggleMaintenance" variant="danger" class="flex-1 font-black uppercase">Sim, Confirmar</flux:button>
        </div>
    </flux:modal>

    {{-- MODAL: REGISTOS --}}
    <flux:modal name="confirm-registration" class="md:w-[450px] space-y-6">
        <div class="text-center space-y-4">
            <div class="inline-flex p-4 bg-brand-50 dark:bg-brand-900/20 rounded-full">
                <flux:icon name="user-group" class="size-10 text-brand-600" />
            </div>
            <div>
                <flux:heading size="lg" class="font-black italic uppercase">Alterar Registos?</flux:heading>
                <p class="text-sm text-zinc-500 mt-2">
                    Estás prestes a <strong>{{ $allow_registration ? 'FECHAR' : 'ABRIR' }}</strong> o sistema para novos utilizadores.
                </p>
            </div>
        </div>
        <div class="flex gap-3">
            <flux:modal.close><flux:button variant="ghost" class="flex-1">Cancelar</flux:button></flux:modal.close>
            <flux:button wire:click="toggleRegistration" variant="primary" class="flex-1 font-black uppercase">Sim, Confirmar</flux:button>
        </div>
    </flux:modal>
</div>
