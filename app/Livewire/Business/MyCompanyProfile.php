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

    // Filtros de Assiduidade
    public $selectedMonth;
    public $selectedYear;

    public function mount()
    {
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;
    }

    /**
     * Permite ao colaborador descarregar os seus documentos (PDFs)
     */
    public function downloadDocument($id)
    {
        // 1. Procura o documento na base de dados
        $doc = DB::table('business_documents')
            ->where('id', $id)
            ->where('user_id', Auth::id()) // Segurança: Garante que só baixa os próprios ficheiros
            ->first();

        // 2. Verifica se o registo existe e se o ficheiro está fisicamente no disco
        if ($doc && Storage::disk('local')->exists($doc->file_path)) {
            // Retorna o download do ficheiro
            return Storage::disk('local')->download(
                $doc->file_path,
                $doc->title . '.pdf'
            );
        }

        // 3. Se houver erro, envia um aviso via toast
        $this->dispatch('toast', variant: 'error', heading: 'Erro', message: 'Ficheiro não encontrado ou acesso negado.');
    }

    /**
     * Envia o pedido de demissão para o CEO
     */
    public function requestResignation()
    {
        $this->validate([
            'resignationReason' => 'required|min:10'
        ]);

        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)
            ->where('workspace_id', $user->current_workspace_id)
            ->firstOrFail();

        $employee->update([
            'resignation_reason' => $this->resignationReason,
            'resignation_status' => 'pending'
        ]);

        // NOTIFICAR O CEO/ADMIN
        $owner = $user->currentWorkspace->users()->wherePivot('role', 'admin')->first();
        if ($owner) {
            DB::table('app_notifications')->insert([
                'user_id' => $owner->id,
                'title' => 'Pedido de Rescisão 🛑',
                'message' => $user->name . " solicitou a demissão da empresa.",
                'type' => 'warning',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->resignationReason = '';
        $this->dispatch('modal-close', name: 'resignation-modal');
        $this->dispatch('toast', variant: 'success', heading: 'Pedido Enviado');
    }

    public function render()
    {
        $user = Auth::user();
        $workspace = $user->currentWorkspace;

        $employee = Employee::where('user_id', $user->id)
            ->where('workspace_id', $user->current_workspace_id)
            ->first();

        $ceo = $workspace->users()->wherePivot('role', 'admin')->first();

        // 1. BUSCAR OS DOCUMENTOS DO UTILIZADOR
        $myDocuments = DB::table('business_documents')
            ->where('user_id', $user->id)
            ->where('workspace_id', $workspace->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. BUSCAR OS LOGS DE ASSIDUIDADE
        $myLogs = DB::table('attendance_logs')
            ->where('user_id', $user->id)
            ->where('workspace_id', $workspace->id)
            ->whereMonth('date', $this->selectedMonth)
            ->whereYear('date', $this->selectedYear)
            ->orderBy('date', 'desc')
            ->get();

        return view('livewire.business.my-company-profile', [
            'employee' => $employee,
            'workspace' => $workspace,
            'ceoName' => $ceo ? $ceo->name : 'Administrador',
            'attendanceLogs' => $myLogs,
            'myDocuments' => $myDocuments
        ]);
    }
}
