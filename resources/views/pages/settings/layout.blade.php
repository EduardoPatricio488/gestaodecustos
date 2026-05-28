<div class="flex items-start max-md:flex-col gap-10 pb-20">
    {{-- MENU LATERAL DE DEFINIÇÕES --}}
    <div class="w-full md:w-[240px] shrink-0">
        <div class="mb-6 px-2">
            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400 italic">Configurações</h3>
        </div>

        <flux:navlist aria-label="Menu de Definições" class="space-y-1">
            <flux:navlist.item
                :href="route('profile.edit')"
                :current="request()->routeIs('profile.edit')"
                wire:navigate.hover
                class="font-bold text-sm uppercase tracking-tight"
            >
                <flux:icon name="user-circle" variant="outline" class="mr-2" />
                {{ __('Perfil & Conta') }}
            </flux:navlist.item>

            {{-- Links que apontam para a mesma página âncora (Unificado) --}}
            <flux:navlist.item
                :href="route('profile.edit')"
                wire:navigate.hover
                class="font-bold text-sm uppercase tracking-tight opacity-60 hover:opacity-100"
            >
                <flux:icon name="shield-check" variant="outline" class="mr-2" />
                {{ __('Segurança') }}
            </flux:navlist.item>

            <flux:navlist.item
                :href="route('profile.edit')"
                wire:navigate.hover
                class="font-bold text-sm uppercase tracking-tight opacity-60 hover:opacity-100"
            >
                <flux:icon name="paint-brush" variant="outline" class="mr-2" />
                {{ __('Aparência') }}
            </flux:navlist.item>
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden my-6" />

    {{-- CONTEÚDO DINÂMICO --}}
    <div class="flex-1 w-full animate-fade-in">
        @if(isset($heading) || isset($subheading))
            <div class="mb-10">
                <flux:heading size="xl" class="font-black uppercase italic tracking-tighter">{{ $heading ?? '' }}</flux:heading>
                <flux:subheading class="text-sm font-medium italic mt-1">{{ $subheading ?? '' }}</flux:subheading>
            </div>
        @endif

        <div class="w-full">
            {{ $slot }}
        </div>
    </div>
</div>
