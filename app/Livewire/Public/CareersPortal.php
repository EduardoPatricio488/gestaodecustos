<?php

namespace App\Livewire\Public;

use App\Models\{Workspace, User};
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

class CareersPortal extends Component
{
    use WithFileUploads;


    public $selectedCompanyId, $selectedCompanyName;
    public $viewingCompany = null; // ✅ Para o modal de detalhes
    public $role_applied, $cv, $phone, $notes;
    public $hasApplied = false;





    // Estado para saber se estamos a rever ou a criar

    public $existingApplication = null;

    /**
     * Abre o modal e preenche os dados automaticamente
     */

     public function openDetails($companyId)
    {
        $this->viewingCompany = Workspace::find($companyId);
        $this->dispatch('modal-show', name: 'company-details-modal');
    }
     public function openApplication($companyId, $companyName)
    {
        $this->selectedCompanyId = $companyId;
        $this->selectedCompanyName = $companyName;

        $existing = DB::table('job_applications')
            ->where('user_id', auth()->id())
            ->where('workspace_id', $companyId)
            ->first();

        if ($existing) {
            $this->hasApplied = true;
            $this->role_applied = $existing->role;
            $this->phone = $existing->phone;
            $this->notes = $existing->notes;
        } else {
            $this->hasApplied = false;
            $this->role_applied = ''; $this->phone = ''; $this->notes = ''; $this->cv = null;
        }

        $this->dispatch('modal-show', name: 'apply-form-modal');
    }


    public function submitApplication()
    {
        $this->validate([
            'role_applied' => 'required|min:3',
            'phone' => 'required',
            'cv' => 'required|file|mimes:pdf|max:5120',
        ]);

        DB::table('job_applications')->insert([
            'user_id' => auth()->id(),
            'workspace_id' => $this->selectedCompanyId,
            'role' => $this->role_applied,
            'phone' => $this->phone,
            'notes' => $this->notes,
            'cv_path' => $this->cv->store('cvs', 'public'),
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->dispatch('modal-close', name: 'apply-form-modal');
        $this->dispatch('toast', text: 'Candidatura submetida com sucesso!', variant: 'success');
    }

     #[Layout('layouts.guest')]
    public function render()
    {
        $userAppliedIds = DB::table('job_applications')
            ->where('user_id', auth()->id())
            ->pluck('workspace_id')->toArray();

        return view('livewire.public.careers-portal', [
            'companies' => Workspace::whereIn('type', ['business', 'bussiness'])->get(),
            'userAppliedIds' => $userAppliedIds
        ]);
    }
}
