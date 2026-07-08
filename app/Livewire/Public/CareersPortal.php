<?php

namespace App\Livewire\Public;

use App\Models\Workspace;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

class CareersPortal extends Component
{
    use WithFileUploads;

    // Dados da Candidatura
    public $selectedCompanyId;
    public $selectedCompanyName;
    public $role_applied, $cv, $phone, $notes;

    protected $rules = [
        'role_applied' => 'required|min:3',
        'phone' => 'required',
        'cv' => 'required|file|mimes:pdf|max:5120', // Máx 5MB
    ];

    /**
     * Abre o formulário para uma empresa específica
     */
    public function openApplication($companyId, $companyName)
    {
        $this->selectedCompanyId = $companyId;
        $this->selectedCompanyName = $companyName;
        $this->dispatch('modal-show', name: 'apply-form-modal');
    }

    /**
     * Submete a candidatura final
     */
    public function submitApplication()
    {
        $this->validate();

        // Lógica: Aqui guardarias na tabela 'job_applications' (que deves criar se necessário)
        // Exemplo:
        // DB::table('job_applications')->insert([
        //     'user_id' => auth()->id(),
        //     'workspace_id' => $this->selectedCompanyId,
        //     'role' => $this->role_applied,
        //     'phone' => $this->phone,
        //     'cv_path' => $this->cv->store('cvs', 'local'),
        //     'created_at' => now()
        // ]);

        $this->dispatch('modal-close', name: 'apply-form-modal');
        $this->dispatch('toast', text: 'Candidatura enviada para ' . $this->selectedCompanyName, variant: 'success');
        $this->reset(['role_applied', 'cv', 'phone', 'notes', 'selectedCompanyId', 'selectedCompanyName']);
    }

    #[Layout('layouts.guest')]
    public function render()
{
    return view('livewire.public.careers-portal', [
        // Procuramos por 'business' e também pelo erro 'bussiness' que está na tua BD
        'companies' => Workspace::whereIn('type', ['business', 'bussiness'])->get()
    ]);
}
}
