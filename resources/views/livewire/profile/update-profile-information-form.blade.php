<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = ''; // Mantemos para exibição, mas não para edição

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        // 1. Validamos apenas o nome (removemos o email daqui)
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        // 2. Atualizamos apenas o nome
        $user->fill([
            'name' => $validated['name'],
        ]);

        // O email nunca será alterado aqui, mesmo que o utilizador tente forçar pelo browser
        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
        $this->dispatch('toast', text: 'Perfil atualizado com sucesso!');
    }

    public function sendVerification(): void
    {
        $user = Auth::user();
        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }
        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section class="space-y-6 text-left">
    <header>
        <flux:heading size="lg" class="font-black">Informações do Perfil</flux:heading>
        <flux:subheading>Atualiza o teu nome de exibição e gere a tua identidade.</flux:subheading>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        {{-- NOME: EDITÁVEL --}}
        <flux:input
            wire:model="name"
            label="Nome Completo"
            type="text"
            required
            autofocus
            autocomplete="name"
            placeholder="Como queres ser chamado?"
        />

        {{-- EMAIL: BLOQUEADO (Readonly + Visual de Cadeado) --}}
        <div class="space-y-4">
            <div class="relative group">
                <flux:input
                    wire:model="email"
                    label="Endereço de Email (Identidade Única)"
                    type="email"
                    disabled
                    class="opacity-70 cursor-not-allowed bg-zinc-50 dark:bg-zinc-800/50"
                    description="O email de conta não pode ser alterado por questões de segurança."
                />
                <div class="absolute right-3 top-9 text-zinc-400">
                    <flux:icon name="lock-closed" variant="micro" class="size-4" />
                </div>
            </div>

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800">
                    <p class="text-xs text-amber-700 dark:text-amber-400 font-medium text-left">
                        O teu email ainda não foi verificado.
                        <button wire:click.prevent="sendVerification" class="underline font-bold hover:text-amber-800">
                            Clica aqui para reenviar o email de verificação.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-xs font-bold text-emerald-600 text-left">
                            Um novo link de verificação foi enviado.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary" class="font-bold">Guardar Alterações</flux:button>
        </div>
    </form>
</section>
