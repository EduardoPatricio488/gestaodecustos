<div class="space-y-8 text-left pb-20">
    {{-- HEADER --}}
    <x-page-header title="Configurações do Ecossistema" description="Gere as regras globais, limites de IA e o estado técnico da plataforma.">
        <x-slot:actions>
            <flux:button wire:click="save" variant="primary" icon="check" class="shadow-lg shadow-brand-500/20 px-8 font-black uppercase tracking-widest text-[10px]">
                Gravar Alterações
            </flux:button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- COLUNA 1: IDENTIDADE & IA --}}
        <div class="space-y-8">
            {{-- Identidade --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2 px-2">
                    <flux:icon name="pencil-square" class="text-zinc-400 size-4" />
                    <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-500">Identidade</h2>
                </div>
                <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                    <flux:input wire:model="site_name" label="Nome do Site" />
                    <flux:input wire:model="support_email" label="E-mail de Suporte" icon="envelope" />
                    <flux:select wire:model="default_currency" label="Moeda do Sistema">
                        <option value="EUR">€ Euro (EUR)</option>
                        <option value="USD">$ Dólar (USD)</option>
                        <option value="GBP">£ Libra (GBP)</option>
                    </flux:select>
                </div>
            </div>

            {{-- Inteligência Artificial --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2 px-2">
                    <flux:icon name="sparkles" class="text-blue-500 size-4" />
                    <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-500">Inteligência IA</h2>
                </div>
                <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                    <flux:input type="number" wire:model="ai_daily_limit" label="Limite Diário de Mensagens" icon="chat-bubble-left-right" />
                    <flux:select wire:model="ai_model" label="Modelo de Linguagem">
                        <option value="gpt-3.5-turbo">GPT-3.5 Turbo (Rápido)</option>
                        <option value="gpt-4">GPT-4 (Inteligente)</option>
                        <option value="gpt-4o">GPT-4o (Elite)</option>
                    </flux:select>
                </div>
            </div>
        </div>

        {{-- COLUNA 2: ESTADO & GAMIFICAÇÃO --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- Estado da Plataforma --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2 px-2">
                    <flux:icon name="shield-check" class="text-zinc-400 size-4" />
                    <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-500">Controlo de Acesso</h2>
                </div>

                <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-4">
                    {{-- Manutenção --}}
                    <div class="p-6 rounded-3xl flex items-center justify-between {{ $maintenance_mode ? 'bg-red-50 dark:bg-red-900/10 border border-red-200' : 'bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-800' }}">
                        <div class="flex items-center gap-4 text-left">
                            <div class="p-3 rounded-2xl {{ $maintenance_mode ? 'bg-red-100 text-red-600' : 'bg-zinc-200 text-zinc-500' }}">
                                <flux:icon name="wrench-screwdriver" class="size-6" />
                            </div>
                            <div>
                                <flux:heading size="md" class="font-bold">Modo de Manutenção</flux:heading>
                                <p class="text-xs text-zinc-500">Status: <strong class="{{ $maintenance_mode ? 'text-red-600' : 'text-emerald-600' }}">{{ $maintenance_mode ? 'ATIVADO' : 'DESATIVADO' }}</strong></p>
                            </div>
                        </div>
                        <flux:modal.trigger name="confirm-maintenance">
                            <flux:button variant="{{ $maintenance_mode ? 'danger' : 'filled' }}" class="font-black uppercase text-[10px]">
                                {{ $maintenance_mode ? 'Desativar' : 'Ativar' }}
                            </flux:button>
                        </flux:modal.trigger>
                    </div>

                    {{-- Registos --}}
                    <div class="p-6 rounded-3xl flex items-center justify-between {{ !$allow_registration ? 'bg-amber-50 dark:bg-amber-900/10 border border-amber-200' : 'bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-800' }}">
                        <div class="flex items-center gap-4 text-left">
                            <div class="p-3 rounded-2xl {{ !$allow_registration ? 'bg-amber-100 text-amber-600' : 'bg-zinc-200 text-zinc-500' }}">
                                <flux:icon name="user-plus" class="size-6" />
                            </div>
                            <div>
                                <flux:heading size="md" class="font-bold">Inscrições de Utilizadores</flux:heading>
                                <p class="text-xs text-zinc-500">Status: <strong class="{{ $allow_registration ? 'text-emerald-600' : 'text-amber-600' }}">{{ $allow_registration ? 'ABERTO' : 'FECHADO' }}</strong></p>
                            </div>
                        </div>
                        <flux:modal.trigger name="confirm-registration">
                            <flux:button variant="filled" class="font-black uppercase text-[10px]">
                                {{ $allow_registration ? 'Bloquear' : 'Liberar' }}
                            </flux:button>
                        </flux:modal.trigger>
                    </div>
                </div>
            </div>

            {{-- Gamificação & Zona de Perigo --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- XP System --}}
                <div class="space-y-4">
                    <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-500 px-2 text-left">Engagement</h2>
                    <div class="glass-card p-8 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                        <flux:switch wire:model="xp_system" label="Ativar Sistema de XP" description="Habilita níveis e pontos." />
                        <flux:input type="number" wire:model="xp_multiplier" label="Multiplicador Global de XP" icon="bolt" />
                    </div>
                </div>

                {{-- Danger Zone --}}
                <div class="space-y-4">
                    <h2 class="text-xs font-black uppercase tracking-[0.2em] text-red-500 px-2 text-left">Zona Crítica</h2>
                    <div class="glass-card p-8 bg-red-500/5 border border-red-500/20 rounded-[2.5rem] space-y-4">
                        <p class="text-[10px] text-red-600 dark:text-red-400 font-bold uppercase leading-tight">Reiniciar Onboarding para todos os utilizadores.</p>
                        <flux:modal.trigger name="confirm-reset-onboarding">
                            <flux:button variant="danger" class="w-full font-black uppercase text-[10px]">Reset Global de Tutorial</flux:button>
                        </flux:modal.trigger>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- MODAIS DE CONFIRMAÇÃO --}}
    <flux:modal name="confirm-maintenance" variant="center" class="md:w-[450px] space-y-6 text-left">
        <div class="text-center space-y-4">
            <flux:icon name="exclamation-triangle" class="size-12 text-red-600 mx-auto" />
            <flux:heading size="lg" class="font-black uppercase italic text-center">Modo de Manutenção?</flux:heading>
            <p class="text-sm text-zinc-500 text-center px-4">Isto impedirá o acesso de todos os utilizadores (exceto admins) ao site de imediato.</p>
        </div>
        <div class="flex gap-3">
            <flux:modal.close><flux:button variant="ghost" class="flex-1">Cancelar</flux:button></flux:modal.close>
            <flux:button wire:click="toggleMaintenance" variant="danger" class="flex-1 font-black uppercase">Sim, Confirmar</flux:button>
        </div>
    </flux:modal>

    <flux:modal name="confirm-reset-onboarding" variant="center" class="md:w-[450px] space-y-6 text-left">
        <div class="text-center space-y-4">
            <flux:icon name="arrow-path" class="size-12 text-red-600 mx-auto" />
            <flux:heading size="lg" class="font-black uppercase italic text-center">Resetar Tutoriais?</flux:heading>
            <p class="text-sm text-zinc-500 text-center px-4 font-bold">Esta ação obrigará TODOS os utilizadores a verem o tutorial de boas-vindas novamente.</p>
        </div>
        <div class="space-y-4 px-4">
            <flux:input type="password" wire:model="adminPassword" label="Confirma a TUA password de Admin" />
        </div>
        <div class="flex gap-3">
            <flux:modal.close><flux:button variant="ghost" class="flex-1">Cancelar</flux:button></flux:modal.close>
            <flux:button wire:click="resetGlobalOnboarding" variant="danger" class="flex-1 font-black uppercase">Confirmar Reset</flux:button>
        </div>
    </flux:modal>
</div>
