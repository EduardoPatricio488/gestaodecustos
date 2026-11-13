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
    public ?string $date_of_birth = null;
    public string $country_of_origin = 'Portugal';
    public ?string $work_country = null;

    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->date_of_birth = $user->date_of_birth?->toDateString();
        $this->country_of_origin = $user->country_of_origin ?: 'Portugal';
        $this->work_country = $user->work_country;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'country_of_origin' => ['required', 'string', 'max:100', Rule::in([...array_values(config('countries')), $user->country_of_origin])],
            'work_country' => ['nullable', Rule::in(array_keys(config('countries')))],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'date_of_birth' => $validated['date_of_birth'] ?: null,
            'country_of_origin' => $validated['country_of_origin'],
            'work_country' => $validated['work_country'] ?: null,
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
        <flux:subheading>Atualiza os teus dados de exibição e gere a tua identidade.</flux:subheading>
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

        <x-birth-date-age :initial-date="$date_of_birth">
            <flux:input wire:model="date_of_birth" label="Data de nascimento" type="date" x-ref="birthDate" aria-describedby="birth-date-age" max="{{ now()->toDateString() }}" />
        </x-birth-date-age>

        <flux:select wire:model="country_of_origin" label="País de origem" required>
            @if ($country_of_origin && ! in_array($country_of_origin, config('countries'), true))
                <option value="{{ $country_of_origin }}">{{ $country_of_origin }}</option>
            @endif
            <x-country-options :use-names="true" />
        </flux:select>

        <flux:select wire:model="work_country" label="País de trabalho" description="Usado para contextualizar a comparação salarial.">
            <option value="">Seleciona o país de trabalho</option>
            <x-country-options />
        </flux:select>

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
