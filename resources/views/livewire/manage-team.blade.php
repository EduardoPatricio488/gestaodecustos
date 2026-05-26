<div class="max-w-4xl mx-auto space-y-8">
    <flux:heading size="xl">Gestão de Colaboração</flux:heading>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- SECÇÃO A: ENTRAR COM CÓDIGO (Para quem foi convidado) -->
        <flux:card class="space-y-4">
            <flux:heading>Entrar num Espaço Existente</flux:heading>
            <flux:subheading>Introduz o código que te enviaram para acederes a outra gestão.</flux:subheading>

            <div class="flex gap-2">
                <flux:input wire:model="inputInviteCode" placeholder="Ex: A1B2C3D4" class="uppercase" />
                <flux:button wire:click="joinWithCode" variant="primary">Entrar</flux:button>
            </div>
        </flux:card>

        <!-- SECÇÃO B: GERAR CÓDIGO (Para quem quer convidar) -->
        <flux:card class="space-y-4">
            <flux:heading>Partilhar Espaço Atual</flux:heading>
            <flux:subheading>Envia este código à pessoa que queres convidar.</flux:subheading>

            @if(auth()->user()->currentWorkspace->invite_code)
                <div class="flex items-center justify-between p-3 bg-zinc-100 dark:bg-zinc-800 rounded-xl border border-dashed border-zinc-300 dark:border-zinc-600">
                    <span class="text-xl font-mono font-black tracking-widest text-brand-600">
                        {{ auth()->user()->currentWorkspace->invite_code }}
                    </span>
                    <flux:button wire:click="generateCode" size="sm" variant="ghost" icon="arrow-path">Novo Código</flux:button>
                </div>
            @else
                <flux:button wire:click="generateCode" variant="filled" class="w-full">Gerar Código de Convite</flux:button>
            @endif
        </flux:card>

    </div>

    <!-- CRIAR NOVO ESPAÇO (O teu código anterior de criação...) -->
    <flux:card class="space-y-4">
        <flux:heading>Criar Novo Espaço</flux:heading>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <flux:input wire:model="newWorkspaceName" label="Nome do Espaço" />
            <flux:select wire:model="type" label="Tipo">
                <flux:select.option value="personal">Individual</flux:select.option>
                <flux:select.option value="couple">Casal</flux:select.option>
                <flux:select.option value="family">Família</flux:select.option>
            </flux:select>
            <flux:button wire:click="createWorkspace">Criar</flux:button>
        </div>
    </flux:card>
</div>
