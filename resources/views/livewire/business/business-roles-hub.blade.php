<div class="space-y-8 pb-20">
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <flux:badge variant="neutral" class="uppercase tracking-widest">Permissões empresariais</flux:badge>
            <h1 class="mt-3 text-3xl md:text-4xl font-black tracking-tight dark:text-white">Equipa & Acessos</h1>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Define o nível de acesso a dados financeiros, equipa e configurações. O proprietário não pode ser rebaixado nesta área.</p>
        </div>
        <flux:button href="{{ route('hub.business.dashboard') }}" variant="ghost" icon="arrow-left" wire:navigate>Painel Business</flux:button>
    </header>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach([
            ['role'=>'Administrador','text'=>'Acesso administrativo, sem alterar o proprietário.'],
            ['role'=>'Manager','text'=>'Gere operações e equipa sem acesso a configurações críticas.'],
            ['role'=>'Contabilista','text'=>'Acesso financeiro e fiscal, incluindo exportações.'],
            ['role'=>'Colaborador','text'=>'Pode registar as suas próprias despesas e usar ferramentas operacionais.'],
            ['role'=>'Leitor','text'=>'Acesso de consulta sem alterações.'],
        ] as $item)
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-xs font-black uppercase tracking-widest text-zinc-800 dark:text-white">{{ $item['role'] }}</p>
                <p class="mt-2 text-sm text-zinc-500">{{ $item['text'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-3xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-zinc-50 dark:bg-zinc-950/50">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-zinc-400">Utilizador</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-zinc-400">Papel atual</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-zinc-400">Novo papel</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($members as $member)
                        @php
                            $isOwner = (int) $member->id === (int) $workspace->owner_id;
                            $role = $isOwner ? 'owner' : app(\App\Services\BusinessAccessService::class)->role($member, $workspace);
                        @endphp
                        <tr>
                            <td class="px-6 py-5">
                                <div class="font-bold text-zinc-900 dark:text-white">{{ $member->name }}</div>
                                <div class="text-xs text-zinc-500">{{ $member->email }}</div>
                            </td>
                            <td class="px-6 py-5 text-sm font-bold text-zinc-600 dark:text-zinc-300">{{ $role === 'owner' ? 'Proprietário' : ($roles[$role] ?? ucfirst($role)) }}</td>
                            <td class="px-6 py-5">
                                @if($isOwner)
                                    <span class="text-xs font-bold text-zinc-400">Protegido</span>
                                @else
                                    <select wire:click="selectMember({{ $member->id }})" wire:model="selectedRole" class="rounded-xl border-zinc-200 bg-zinc-50 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                                        @foreach($roles as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                                    </select>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-right">
                                @if(! $isOwner)
                                    <flux:button wire:click="selectMember({{ $member->id }})" size="sm" variant="ghost">Selecionar</flux:button>
                                    @if($selectedUserId === $member->id)
                                        <flux:button wire:click="updateRole" size="sm" variant="primary">Guardar</flux:button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>