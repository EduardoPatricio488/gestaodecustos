<?php

namespace App\Livewire\Admin;

use App\Models\SiteSetting as Setting;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class SiteSettings extends Component
{
    public $site_name;
    public $default_currency;
    public $maintenance_mode;
    public $allow_registration;
    public $xp_system;

    /**
     * Carrega os dados da BD
     */
    public function mount()
    {
        $this->site_name = Setting::get('site_name', 'Finance Pro IA');
        $this->default_currency = Setting::get('default_currency', 'EUR');
        $this->maintenance_mode = (bool) Setting::get('maintenance_mode', 0);
        $this->allow_registration = (bool) Setting::get('allow_registration', 1);
        $this->xp_system = (bool) Setting::get('xp_system', 1);
    }

    /**
     * Alterna o Modo de Manutenção (Chamado pelo Modal)
     */
    public function toggleMaintenance()
    {
        $this->maintenance_mode = !$this->maintenance_mode;
        Setting::set('maintenance_mode', $this->maintenance_mode ? '1' : '0');

        $this->dispatch('modal-close', name: 'confirm-maintenance');
        $status = $this->maintenance_mode ? 'ATIVADO' : 'DESATIVADO';
        $this->dispatch('toast', text: "Modo de Manutenção $status!");
    }

    /**
     * Alterna os Novos Registos (Chamado pelo Modal)
     */
    public function toggleRegistration()
    {
        $this->allow_registration = !$this->allow_registration;
        Setting::set('allow_registration', $this->allow_registration ? '1' : '0');

        $this->dispatch('modal-close', name: 'confirm-registration');
        $status = $this->allow_registration ? 'ABERTOS' : 'FECHADOS';
        $this->dispatch('toast', text: "Registos de utilizador $status!");
    }

    /**
     * Grava as definições gerais
     */
    public function save()
    {
        Setting::set('site_name', $this->site_name);
        Setting::set('default_currency', $this->default_currency);
        Setting::set('xp_system', $this->xp_system ? '1' : '0');

        $this->dispatch('toast', text: 'Configurações guardadas!');
    }

    public function render()
    {
        return view('livewire.admin.site-settings');
    }
}
