<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\{Auth, DB};
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class MyCompanyProfile extends Component
{
    public $resignationReason = '';
    public $selectedMonth;
    public $selectedYear;

    public function mount()
    {
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;
    }

    /**
     * Download de documentos da tabela business_documents
     */
    public function downloadDocument($id)
    {
        $doc = DB::table('business_documents')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($doc && Storage::disk('local')->exists($doc->file_path)) {
            return Storage::disk('local')->download($doc->file_path, $doc->title . '.pdf');
        }

        $this->dispatch('toast', variant: 'error', text: 'Ficheiro não encontrado.');
    }

    /**
     * NOVO: Método para descarregar o CV vindo da tabela job_applications
     */
    public function downloadCV($path)
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->download($path, 'O-Meu-Curriculo.pdf');
        }

        $this->dispatch('toast', variant: 'error', text: 'O currículo original não foi encontrado.');
    }

    public function requestResignation()
    {
        $this->validate(['resignationReason' => 'required|min:10']);

        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)
            ->where('workspace_id', $user->current_workspace_id)
            ->firstOrFail();

        $employee->update([
            'resignation_reason' => $this->resignationReason,
            'resignation_status' => 'pending'
        ]);

        $this->resignationReason = '';
        $this->dispatch('modal-close', name: 'resignation-modal');
        $this->dispatch('toast', variant: 'success', text: 'Pedido de demissão enviado.');
    }

    public function render()
    {
        $user = Auth::user();
        $workspace = $user->currentWorkspace;

        $employee = Employee::where('user_id', $user->id)
            ->where('workspace_id', $user->current_workspace_id)
            ->first();

        $ceo = $workspace->users()->wherePivot('role', 'admin')->first();

        // BUSCAR DOCUMENTOS
        $myDocuments = DB::table('business_documents')
            ->where('user_id', $user->id)
            ->where('workspace_id', $workspace->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // BUSCAR LOGS
        $myLogs = DB::table('attendance_logs')
            ->where('user_id', $user->id)
            ->where('workspace_id', $workspace->id)
            ->whereMonth('date', $this->selectedMonth)
            ->whereYear('date', $this->selectedYear)
            ->orderBy('date', 'desc')
            ->get();

        // --- NOVO: BUSCAR O CV NA TABELA job_applications ---
        $application = DB::table('job_applications')
            ->where('user_id', $user->id)
            ->where('workspace_id', $workspace->id)
            ->where('status', 'accepted') // Só o que foi aceite
            ->first();

        return view('livewire.business.my-company-profile', [
            'employee' => $employee,
            'workspace' => $workspace,
            'ceoName' => $ceo ? $ceo->name : 'Administrador',
            'attendanceLogs' => $myLogs,
            'myDocuments' => $myDocuments,
            'userCV' => $application ? $application->cv_path : null // Passa o caminho do ficheiro
        ]);
    }
}
