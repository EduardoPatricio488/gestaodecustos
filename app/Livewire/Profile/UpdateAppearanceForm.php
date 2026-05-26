<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use App\Services\CurrencyService;
use Illuminate\Support\Facades\App;

class UpdateAppearanceForm extends Component
{
    public $currency;
    public $locale;

    /**
     * Carrega as definições atuais ao abrir a página.
     */
    public function mount()
    {
        $user = auth()->user();

        // 1. Vai buscar a moeda do Workspace ativo
        $this->currency = $user->currentWorkspace->currency ?? 'EUR';

        // 2. Vai buscar o idioma do Perfil do Utilizador
        $this->locale = $user->locale ?? 'pt';
    }

    /**
     * Grava as alterações de Moeda e Idioma.
     */
    public function updateSettings()
    {
        $user = auth()->user();
        $workspace = $user->currentWorkspace;

        // 1. Atualiza o Idioma no Utilizador (precisas da coluna 'locale' na tabela users)
        $user->update([
            'locale' => $this->locale,
        ]);

        // 2. Atualiza a Moeda no Workspace
        if ($workspace) {
            $workspace->update([
                'currency' => $this->currency,
            ]);
        }

        // 3. Aplica o idioma imediatamente para esta sessão
        App::setLocale($this->locale);

        $this->dispatch('toast', text: 'Definições de visualização atualizadas!');

        // Recarregamos a página para aplicar o novo idioma e símbolos em todo o lado
        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.profile.update-appearance-form', [
            'currencies' => CurrencyService::getSymbols(),
            'languages' => [
                'pt' => 'Português (PT)',
                'en' => 'English (US)',
                'es' => 'Español (ES)',
                'fr' => 'Français (FR)',
            ]
        ]);
    }
}
