<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\{Employee, User, Workspace};
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Mail\BusinessPlanActivatedMail;
use App\Mail\EmployeeDataUpdatedMail; // Importa o novo mail
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class TeamHub extends Component
{
    use WithFileUploads;

    // Propriedades do Formulário Principal
    public $name, $role, $salary, $pay_day = 25;
    public $editingId = null;
    public bool $showingRaiseModal = false;
     public $selectedMonth;
     public $employeeDocuments = [];
     public $selectedEmployee = null;
public $selectedEmployeeName = '';
    public $selectedYear;
    public $generatedToken = '';
public $tokenEmployeeId = null;
    public $viewingAttendanceId = null;

public $attendanceEmployeeName = '';
    public $photo = null;
    public $employee = null;
      public $docFile; // Para o upload
    public $docTitle = '';
    public $docType = 'recibo';
    public $selectedEmpIdForUpload = null;

    // Propriedades de Filtro e Aumento
    public $raiseEmployeeId = null;
    public $statusFilter = 'all';
    public $raiseAmount = 0;

    public function mount()
    {
        $this->selectedMonth = now()->month;
        $this->selectedYear  = now()->year;
    }

    protected $rules = [
        'name' => 'required|string|max:255',
        'role' => 'required|string|max:255',
        'salary' => 'required|numeric|min:0',
        'pay_day' => 'required|integer|between:1,31',
    ];

    /**
     * Helper para criar notificações no sistema
     */
    protected function notifyUser($userId, $title, $message, $type = 'info')
{
    if (!$userId) return;

    \DB::table('app_notifications')->insert([
        'user_id'    => $userId,
        'title'      => $title,
        'message'    => $message,
        'type'       => $type,
        // Removido o 'is_read' que estava a causar o erro
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

public function viewDocuments($employeeId)
{
    // 1. Carregamos o colaborador para aceder ao cv_path
    $this->selectedEmployee = Employee::findOrFail($employeeId);

    $this->selectedEmpIdForUpload = $employeeId;
    $this->selectedEmployeeName = $this->selectedEmployee->name;

    // 2. Buscamos os restantes documentos
    $this->employeeDocuments = DB::table('business_documents')
        ->where('user_id', $this->selectedEmployee->user_id)
        ->where('workspace_id', auth()->user()->current_workspace_id)
        ->orderBy('created_at', 'desc')
        ->get();

    $this->dispatch('modal-show', name: 'view-documents-modal');
}

// 3. Método para eliminar um documento (Opcional, mas recomendado)
public function deleteDocument($docId)
{
    $doc = DB::table('business_documents')->where('id', $docId)->first();

    if ($doc) {
        \Storage::disk('local')->delete($doc->file_path);
        DB::table('business_documents')->where('id', $docId)->delete();

        // Refresh da lista
        $this->viewDocuments($this->selectedEmpIdForUpload);
        $this->dispatch('toast', variant: 'success', heading: 'Documento Eliminado');
    }
}
public function viewAttendance($id)
{
    $emp = Employee::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);
    $this->viewingAttendanceId = $id;
    $this->attendanceEmployeeName = $emp->name;

    $this->dispatch('modal-show', name: 'attendance-history-modal');
}
    /**
     * Gera um novo código de acesso para a empresa
     */

    public function updatedSelectedMonth($value)
{
    $this->selectedMonth = (int) $value;
}

public function updatedSelectedYear($value)
{
    $this->selectedYear = (int) $value;
}


    public function setFilter($status)
    {
        $this->statusFilter = ($this->statusFilter === $status) ? 'all' : $status;
    }
/**
     * Aceitar Pedido de Demissão
     */
    public function acceptResignation($id)
    {
        $emp = Employee::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);

        // 1. Marcar como despedido e limpar o status de pedido
        $emp->update([
            'active' => false,
            'terminated_at' => now(),
            'resignation_status' => null
        ]);

        // 2. Notificar o utilizador
        $this->notifyUser($emp->user_id, 'Rescisão Confirmada 🛑', 'O teu pedido de demissão foi aceite pela administração. O teu vínculo foi encerrado.', 'danger');

        $this->dispatch('toast', variant: 'success', heading: 'Rescisão Concluída', message: 'O colaborador foi removido das funções ativas.');
    }

    /**
     * Rejeitar Pedido de Demissão
     */

public function sendInviteEmail($id)
{
    $emp = Employee::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);
    $workspace = auth()->user()->currentWorkspace;

    // 1. Verificar se temos um e-mail para enviar
    // Se o colaborador já tiver conta vinculada, usamos o e-mail do User.
    // Se não, tentamos buscar na tabela de candidaturas (job_applications).
    $email = $emp->user?->email;

    if (!$email) {
        $application = DB::table('job_applications')
            ->where('workspace_id', $workspace->id)
            ->where('user_id', $emp->user_id) // Tentativa por user_id
            ->orWhere('name', 'like', '%' . $emp->name . '%') // Tentativa por nome
            ->first();

        $email = $application?->email;
    }

    if (!$email) {
        $this->dispatch('toast', variant: 'error', text: 'Não foi possível encontrar o e-mail deste colaborador.');
        return;
    }

    // 2. Enviar o E-mail
    try {
        Mail::to($email)->send(new \App\Mail\WorkspaceInviteMail(
            $workspace->name,
            $workspace->invite_code,
            $emp->name
        ));

        $this->dispatch('toast', variant: 'success', text: 'Convite enviado para ' . $email);
    } catch (\Exception $e) {
        $this->dispatch('toast', variant: 'error', text: 'Erro ao enviar e-mail.');
    }
}
   public function activateBusinessPlan($id)
{
    $emp = Employee::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);

    if (!$emp->user_id) {
        $this->dispatch('toast', variant: 'error', heading: 'Ação Bloqueada', message: 'Este colaborador não tem conta de utilizador vinculada.');
        return;
    }

    $user = \App\Models\User::find($emp->user_id);
    $workspace = auth()->user()->currentWorkspace;

    // 1. Upgrade na Base de Dados
    $user->update(['plan' => 'pro']);

    // 2. Envio do Email Real
    try {
        Mail::to($user->email)->send(new BusinessPlanActivatedMail($user->name, $workspace->name));
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error("Erro ao enviar email de upgrade: " . $e->getMessage());
    }

    // 3. Notificação no Site
    $this->notifyUser(
        $user->id,
        'Upgrade de Conta! 💎',
        'O Plano Business foi ativado. Já tens acesso a todas as ferramentas premium.',
        'success'
    );

    $this->dispatch('toast', variant: 'success', heading: 'Plano Ativado', message: "Upgrade efetuado e e-mail enviado a {$user->name}.");
}
    public function rejectResignation($id)
    {
        $emp = Employee::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);

        // 1. Limpar o status de pedido e manter ativo
        $emp->update([
            'resignation_status' => 'rejected',
            'resignation_reason' => null
        ]);

        // 2. Notificar o utilizador
        $this->notifyUser($emp->user_id, 'Pedido Rejeitado ⚠️', 'O teu pedido de demissão foi analisado e rejeitado pela administração. Por favor, contacta os RH.', 'warning');

        $this->dispatch('toast', variant: 'info', heading: 'Pedido Rejeitado', message: 'O colaborador foi notificado da decisão.');
    }
   public function save()
    {
        $this->validate();

        $data = [
            'workspace_id' => Auth::user()->current_workspace_id,
            'name' => $this->name,
            'role' => $this->role,
            'salary' => $this->salary,
            'pay_day' => $this->pay_day,
            'active' => true,
        ];

        if ($this->photo) {
            $data['photo_path'] = $this->photo->store('employees', 'public');
        }

        // 1. Guardar/Atualizar na Base de Dados
        $emp = Employee::updateOrCreate(['id' => $this->editingId], $data);

        // 2. Lógica de Notificação por E-mail (Apenas se for uma EDIÇÃO)
        if ($this->editingId && $emp->user_id) {
            $userAccount = User::find($emp->user_id);
            $companyName = Auth::user()->currentWorkspace->name;

            if ($userAccount) {
                // Envio de E-mail Real
                try {
                    Mail::to($userAccount->email)->send(new EmployeeDataUpdatedMail($emp, $companyName));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Falha ao notificar colaborador: " . $e->getMessage());
                }

                // Notificação interna (Balão no site)
                $this->notifyUser($emp->user_id, 'Ficha Atualizada 📝', 'A administração atualizou os teus parâmetros contratuais.');
            }
        }

        $this->reset(['name', 'role', 'salary', 'pay_day', 'editingId', 'photo', 'employee']);
        $this->dispatch('modal-close', name: 'add-employee-modal');
        $this->dispatch('toast', heading: 'Sucesso', message: 'Ficha atualizada e colaborador notificado.');
    }

public function openRaiseModal($id)
{
    $emp = Employee::where('workspace_id', Auth::user()->current_workspace_id)->findOrFail($id);

    $this->raiseEmployeeId = $id;
    $this->selectedEmployeeName = $emp->name;
    $this->raiseAmount = 0;

    // 2. Ativa a visibilidade do modal
    $this->showingRaiseModal = true;
}
    public function applyRaise()
    {
        if ($this->raiseAmount <= 0) return;

        $emp = Employee::where('workspace_id', Auth::user()->current_workspace_id)->findOrFail($this->raiseEmployeeId);
        $emp->salary += (float) $this->raiseAmount;
        $emp->save();

        // NOTIFICAÇÃO DE AUMENTO
        $this->notifyUser(
            $emp->user_id,
            'Aumento Salarial! 💰',
            'Boas notícias! O teu vencimento mensal foi atualizado para ' . number_format((float) $emp->salary, 2, ',', ' ') . '€.',
            'success'
        );

        $this->reset(['raiseEmployeeId', 'raiseAmount']);
        $this->dispatch('modal-close', name: 'raise-salary-modal');
        $this->dispatch('toast', heading: 'Aumento Aplicado', message: 'Vencimento atualizado com sucesso.');
         $this->showingRaiseModal = false;
    $this->dispatch('toast', heading: 'Sucesso', message: 'Aumento aplicado.');
    }
public function generateNewInviteCode()
    {
        $workspace = Auth::user()->currentWorkspace;
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $workspace->name), 0, 3));
        $random = strtoupper(bin2hex(random_bytes(3)));
        $workspace->invite_code = $prefix . '-' . $random;
        $workspace->save();
        $this->dispatch('toast', variant: 'success', text: 'Token interno atualizado.');
    }
    public function edit($id)
    {
        $emp = Employee::where('workspace_id', Auth::user()->current_workspace_id)->findOrFail($id);
        $this->editingId = $emp->id;
        $this->name = $emp->name;
        $this->role = $emp->role;
        $this->salary = $emp->salary;
        $this->pay_day = $emp->pay_day;
        $this->employee = $emp;
        $this->dispatch('modal-show', name: 'add-employee-modal');
    }

    public function activate($id)
    {
        $emp = Employee::where('workspace_id', Auth::user()->current_workspace_id)->findOrFail($id);

        // Remove suspensão, ativa e limpa data de término caso existisse
        $emp->update([
            'active' => true,
            'suspended' => false,
            'terminated_at' => null
        ]);

        // Enviar E-mail se houver utilizador vinculado
        if ($emp->user) {
            try {
                Mail::to($emp->user->email)->send(new \App\Mail\EmployeeReactivatedMail($emp->name, Auth::user()->currentWorkspace->name));

                // Notificação interna no site
                $this->notifyUser($emp->user_id, 'Conta Reativada ✅', 'O teu acesso foi restabelecido pela administração.', 'success');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Erro ao enviar email de reativação: " . $e->getMessage());
            }
        }

        $this->dispatch('toast', variant: 'success', message: 'Colaborador reativado e notificado.');
    }

    public function suspend($id)
    {
        $emp = Employee::where('workspace_id', Auth::user()->current_workspace_id)->findOrFail($id);
        $emp->update(['suspended' => true]);

        if ($emp->user) {
            Mail::to($emp->user->email)->send(new \App\Mail\EmployeeSuspendedMail($emp->name, Auth::user()->currentWorkspace->name));
            $this->notifyUser($emp->user_id, 'Conta Suspensa ⚠️', 'O teu acesso foi suspenso pela administração.', 'warning');
        }

        $this->dispatch('toast', message: 'Vínculo suspenso e colaborador notificado.');
    }

    public function terminate($id)
    {
        $emp = Employee::where('workspace_id', Auth::user()->current_workspace_id)->findOrFail($id);
        $emp->update(['active' => false, 'terminated_at' => now()]);

        if ($emp->user) {
            Mail::to($emp->user->email)->send(new \App\Mail\EmployeeTerminatedMail($emp->name, Auth::user()->currentWorkspace->name));
            $this->notifyUser($emp->user_id, 'Vínculo Terminado 🛑', 'A tua ligação com a empresa foi encerrada.', 'danger');
        }

        $this->dispatch('toast', message: 'Vínculo terminado e e-mail enviado.');
    }

    public function deleteEmployee($id)
    {
        $emp = Employee::where('workspace_id', Auth::user()->current_workspace_id)->findOrFail($id);

        // Guardamos os dados ANTES de apagar o funcionário da base de dados
        $email = $emp->user?->email;
        $name = $emp->name;

        $emp->delete();

        if ($email) {
            Mail::to($email)->send(new \App\Mail\EmployeeDeletedMail($name));
        }

        $this->dispatch('toast', variant: 'success', heading: 'Eliminado', message: 'Registo removido e colaborador avisado.');
    }
    public function openUploadModal($employeeId)
    {
        $this->selectedEmpIdForUpload = $employeeId;
        $this->docTitle = '';
        $this->docFile = null;
        $this->dispatch('modal-show', name: 'upload-doc-modal');
    }

    /**
     * Guarda o documento na base de dados e no disco
     */
    public function saveDocument()
    {
        $this->validate([
            'docFile' => 'required|file|max:5120', // Máx 5MB
            'docTitle' => 'required|string|max:100',
            'docType' => 'required|in:contrato,recibo,outro',
        ]);

        $employee = Employee::findOrFail($this->selectedEmpIdForUpload);

        // Guardar o ficheiro na pasta privada (storage/app/business-docs)
        $path = $this->docFile->store('business-docs', 'local');

        DB::table('business_documents')->insert([
            'workspace_id' => Auth::user()->current_workspace_id,
            'user_id'      => $employee->user_id,
            'title'        => $this->docTitle,
            'file_path'    => $path,
            'type'         => $this->docType,
            'file_size'    => round($this->docFile->getSize() / 1024, 2) . ' KB',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // Notifica o colaborador
        $this->notifyUser($employee->user_id, 'Novo Documento 📂', 'A administração carregou o ficheiro: ' . $this->docTitle);

        $this->reset(['docFile', 'docTitle', 'docType', 'selectedEmpIdForUpload']);
        $this->dispatch('modal-close', name: 'upload-doc-modal');
        $this->dispatch('toast', variant: 'success', heading: 'Ficheiro Enviado');
    }
    /**
     * MUDANÇA: Entrar em "Modo Visualização"
     * Este método não troca o utilizador logado, apenas guarda na sessão
     * quem o CEO quer "espreitar".
     */
    public function switchToEmployee($id)
    {
        $employee = Employee::where('workspace_id', auth()->user()->current_workspace_id)
            ->findOrFail($id);

        if (!$employee->user_id) {
            $this->dispatch('toast', variant: 'error', text: 'Este colaborador não tem conta associada.');
            return;
        }

        // 1. Guardamos o ID do colaborador na sessão para o "Shadow Mode"
        session()->put('viewing_as_collaborator_id', $employee->id);

        // 2. Redirecionamos para o dashboard.
        // A tua rota inteligente (web.php) vai ler esta sessão e mostrar o terminal operacional.
        return redirect()->route('hub.business.dashboard');
    }
  public function generateEmployeeAccessCode($id)
{
    $emp = Employee::where('workspace_id', auth()->user()->current_workspace_id)->findOrFail($id);

    // Gera um código único (ex: EMP-A1B2C3)
    $token = 'EMP-' . strtoupper(\Illuminate\Support\Str::random(6));

    $emp->update([
        'portal_token' => $token
    ]);

    $this->generatedToken = $token;
    $this->selectedEmployeeName = $emp->name;
    $this->tokenEmployeeId = $id;

    $this->dispatch('modal-show', name: 'employee-token-modal');
    $this->dispatch('toast', variant: 'success', heading: 'Chave Gerada', message: 'O acesso ao portal foi configurado.');
}
  public function render()
    {
        $workspace = Auth::user()->currentWorkspace;

        // 1. Garante que a empresa tem um código de convite
        if (!$workspace->invite_code) {
            $this->generateNewInviteCode();
            $workspace->refresh();
        }

        // 2. Query do Diretório de Colaboradores (com filtros de estado)
        $query = Employee::where('workspace_id', $workspace->id);

        if ($this->statusFilter === 'active') {
            $query->where('active', true)->where('suspended', false)->whereNull('terminated_at');
        } elseif ($this->statusFilter === 'suspended') {
            $query->where('suspended', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('active', false)->whereNull('terminated_at');
        } elseif ($this->statusFilter === 'terminated') {
            $query->whereNotNull('terminated_at');
        }

        // 3. NOVO: Lógica para buscar os logs de assiduidade de TODA a equipa (para as abas dentro dos cards)
        // Agrupamos por user_id para que no Blade possamos fazer: $allLogs->get($emp->user_id)
        $allAttendanceLogs = DB::table('attendance_logs')
            ->where('workspace_id', $workspace->id)
            ->whereMonth('date', $this->selectedMonth)
            ->whereYear('date', $this->selectedYear)
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy('user_id');

        // 4. Coleções para KPIs
        $allEmployees = Employee::where('workspace_id', $workspace->id)->get();
        $activeEmployees = $allEmployees->where('active', true)->where('suspended', false)->whereNull('terminated_at');

        // 5. Cálculos de KPIs
        $totalPayroll = $activeEmployees->sum('salary');
        $nextPayDay = $allEmployees->count() > 0 ? $allEmployees->min('pay_day') : 25;

        return view('livewire.business.team-hub', [
            'workspace'           => $workspace,
            'employees'           => $query->latest()->get(),
            'allLogs'             => $allAttendanceLogs, // Variável crucial para a aba interna do card
            'totalPayroll'        => $totalPayroll,
            'employeeCount'       => $allEmployees->count(),
            'nextPayDay'          => $nextPayDay,
            'daysUntilNext'       => (now()->day <= $nextPayDay) ? ($nextPayDay - now()->day) : (now()->daysInMonth - now()->day + $nextPayDay),
            'liquidity'           => $totalPayroll * 1.23,
            'avgCost'             => $allEmployees->count() > 0 ? $totalPayroll / $allEmployees->count() : 0,
            'low'                 => $allEmployees->where('salary', '<=', 1000)->count(),
            'mid'                 => $allEmployees->whereBetween('salary', [1001, 2500])->count(),
            'high'                => $allEmployees->where('salary', '>', 2500)->count(),
            'active'              => $activeEmployees->count(),
            'inactive'            => $allEmployees->where('active', false)->whereNull('terminated_at')->count(),
            'suspended'           => $allEmployees->where('suspended', true)->count(),
            'turnover'            => $allEmployees->whereNotNull('terminated_at')->count(),
            'turnoverRate'        => $allEmployees->count() > 0 ? number_format(($allEmployees->whereNotNull('terminated_at')->count() / $allEmployees->count()) * 100, 1) . '%' : '0%',
            'individualLogs'      => $this->viewingAttendanceId
                ? DB::table('attendance_logs')
                    ->where('workspace_id', $workspace->id)
                    ->where('user_id', Employee::find($this->viewingAttendanceId)?->user_id)
                    ->whereMonth('date', $this->selectedMonth)
                    ->whereYear('date', $this->selectedYear)
                    ->orderBy('date', 'desc')
                    ->get()
                : collect(),
        ]);
    }
}
