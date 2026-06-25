<?php

namespace App\Livewire\Admin;

use App\Models\SiteSetting as Setting;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Hash;

#[Layout('components.layouts.app')]
class SiteSettings extends Component
{
    // Identidade
    public $site_name;
    public $default_currency;
    public $support_email;

    // Estado e Segurança
    public $maintenance_mode;
    public $allow_registration;

    // Inteligência IA
    public $ai_daily_limit;
    public $ai_model;

    // Gamificação
    public $xp_system;
    public $xp_multiplier;

    // Confirmação
    public $adminPassword = '';

    public function mount()
    {
        $this->site_name = Setting::get('site_name', 'Finance Pro IA');
        $this->default_currency = Setting::get('default_currency', 'EUR');
        $this->support_email = Setting::get('support_email', 'suporte@financepro.com');

        $this->maintenance_mode = (bool) Setting::get('maintenance_mode', 0);
        $this->allow_registration = (bool) Setting::get('allow_registration', 1);

        $this->ai_daily_limit = Setting::get('ai_daily_limit', 50);
        $this->ai_model = Setting::get('ai_model', 'gpt-3.5-turbo');

        $this->xp_system = (bool) Setting::get('xp_system', 1);
        $this->xp_multiplier = Setting::get('xp_multiplier', 1);
    }

    public function toggleMaintenance()
    {
        $this->maintenance_mode = !$this->maintenance_mode;
        Setting::set('maintenance_mode', $this->maintenance_mode ? '1' : '0');
        auth()->user()->logActivity(($this->maintenance_mode ? "Ativou" : "Desativou") . " manutenção", "seguranca");
        $this->dispatch('modal-close', name: 'confirm-maintenance');
        $this->dispatch('toast', text: "Modo de Manutenção atualizado!");
    }

    public function toggleRegistration()
    {
        $this->allow_registration = !$this->allow_registration;
        Setting::set('allow_registration', $this->allow_registration ? '1' : '0');
        auth()->user()->logActivity(($this->allow_registration ? "Abriu" : "Fechou") . " registos", "seguranca");
        $this->dispatch('modal-close', name: 'confirm-registration');
        $this->dispatch('toast', text: "Estado dos registos alterado!");
    }

    /**
     * Reseta o onboarding de TODOS os utilizadores (Ação em Massa)
     */
    public function resetGlobalOnboarding()
    {
        if (!Hash::check($this->adminPassword, auth()->user()->password)) {
            $this->addError('adminPassword', 'Password incorreta.');
            return;
        }

        User::query()->update(['onboarding_completed' => false]);
        auth()->user()->logActivity("Resetou o tutorial de todos os utilizadores", "seguranca");

        $this->dispatch('modal-close', name: 'confirm-reset-onboarding');
        $this->dispatch('toast', text: "Tutorial reiniciado para todos!");
        $this->adminPassword = '';
    }

    public function save()
    {
        Setting::set('site_name', $this->site_name);
        Setting::set('default_currency', $this->default_currency);
        Setting::set('support_email', $this->support_email);
        Setting::set('ai_daily_limit', $this->ai_daily_limit);
        Setting::set('ai_model', $this->ai_model);
        Setting::set('xp_multiplier', $this->xp_multiplier);
        Setting::set('xp_system', $this->xp_system ? '1' : '0');

        auth()->user()->logActivity("Atualizou configurações globais", "configuracao");
        $this->dispatch('toast', text: 'Configurações guardadas com sucesso!');
    }

    public function render()
    {
        return view('livewire.admin.site-settings');
    }
}
