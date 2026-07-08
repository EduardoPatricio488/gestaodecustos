<div class="w-full max-w-lg bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-8 sm:p-12 rounded-[3.5rem] shadow-2xl backdrop-blur-md space-y-8 relative mx-auto text-left">
    <div class="text-center space-y-4">
        <div class="size-16 bg-brand-600 rounded-2xl mx-auto flex items-center justify-center text-white shadow-xl">
            <flux:icon name="user-plus" variant="solid" class="size-8" />
        </div>
        <h1 class="text-3xl font-black text-zinc-900 dark:text-white uppercase italic tracking-tighter">{{ $isRegistering ? 'Criar Perfil de Candidato' : 'Acesso Candidato' }}</h1>
    </div>

    <form wire:submit.prevent="authenticate" class="space-y-5">
        @if($isRegistering)
            <flux:input wire:model="name" label="Nome Completo" placeholder="Como te chamas?" />
        @endif

        <flux:input wire:model="email" label="Email" type="email" placeholder="teu@email.com" />
        <flux:input wire:model="password" label="Password" type="password" placeholder="********" />

        @if (session()->has('error'))
            <p class="text-[10px] text-red-500 font-black uppercase text-center italic">{{ session('error') }}</p>
        @endif

        <flux:button type="submit" variant="primary" class="w-full h-14 rounded-2xl font-black uppercase tracking-widest text-xs border-none text-white">
            {{ $isRegistering ? 'Criar Conta e Explorar Empresas' : 'Entrar no Portal' }}
        </flux:button>
    </form>

    <div class="text-center pt-4 border-t border-zinc-100 dark:border-zinc-800">
        <button wire:click="$toggle('isRegistering')" class="text-[10px] font-black text-zinc-400 uppercase tracking-widest hover:text-brand-600 transition-colors">
            {{ $isRegistering ? 'Já tenho conta de candidato' : 'Ainda não tenho conta' }}
        </button>
    </div>
</div>
