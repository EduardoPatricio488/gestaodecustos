<?php

namespace App\Livewire\Business;

use App\Models\{Employee, User, Workspace};
use App\Mail\HiredNotificationMail; // <--- IMPORTANTE: Importar o Mailable
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail; // <--- IMPORTANTE: Importar a Facade Mail

#[Layout('components.layouts.app')]
class RecruitmentHub extends Component
{
    use WithFileUploads;

    public $recActive, $recDesc, $recAnnounce, $recVacancies, $recExtraInfo;

    public function mount()
    {
        $workspace = auth()->user()->currentWorkspace;
        $this->recActive = (bool) $workspace->recruitment_active;
        $this->recExtraInfo = $workspace->recruitment_extra_info;
        $this->recDesc = $workspace->recruitment_description;
        $this->recAnnounce = $workspace->recruitment_announcement;
        $this->recVacancies = $workspace->recruitment_vacancies ?? 1;
    }

    public function toggleActive()
    {
        $this->recActive = !$this->recActive;
    }

    public function saveSettings()
    {
        $workspace = auth()->user()->currentWorkspace;
        $workspace->update([
            'recruitment_active'      => $this->recActive,
            'recruitment_description' => $this->recDesc,
            'recruitment_announcement' => $this->recAnnounce,
            'recruitment_vacancies'   => (int) $this->recVacancies,
            'recruitment_extra_info'  => $this->recExtraInfo,
        ]);
        session()->flash('published', true);
        $this->dispatch('toast', variant: 'success', text: 'Montra pública atualizada!');
    }



    public function rejectCandidate($id)
    {
        DB::table('job_applications')->where('id', $id)->update(['status' => 'rejected']);
        $this->dispatch('toast', variant: 'warning', text: 'Candidatura arquivada.');
    }

    /**
     * Converte um candidato num colaborador oficial e envia e-mail
     */
    public function acceptCandidate($id)
    {
        $app = DB::table('job_applications')->where('id', $id)->first();
        if (!$app) return;

        $user = User::find($app->user_id);
        $workspace = auth()->user()->currentWorkspace;

        // 1. ENVIAR E-MAIL DE CONTRATAÇÃO (Via MailHog)
        try {
            Mail::to($user->email)->send(new HiredNotificationMail($user->name, $workspace->name));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erro ao enviar email: " . $e->getMessage());
        }

        // 2. CRIAR FICHA DE COLABORADOR
        Employee::create([
            'workspace_id' => $app->workspace_id,
            'user_id'      => $app->user_id,
            'name'         => $user->name,
            'role'         => $app->role,
            'salary'       => 0,
            'pay_day'      => 25,
            'active'       => true,
            'cv_path'      => $app->cv_path // Copia o CV da candidatura para a ficha
        ]);

        // 3. ATUALIZAR STATUS DA CANDIDATURA
        DB::table('job_applications')->where('id', $id)->update(['status' => 'accepted']);

        $this->dispatch('toast', variant: 'success', text: 'Colaborador contratado e notificado!');

        return redirect()->route('hub.business.team');
    }

    public function reopenCandidate($id)
    {
        DB::table('job_applications')->where('id', $id)->update(['status' => 'pending']);
        $this->dispatch('toast', text: 'Candidatura reaberta.');
    }

    public function render()
    {
        $workspace = Auth::user()->currentWorkspace;
        $applications = DB::table('job_applications')
            ->join('users', 'job_applications.user_id', '=', 'users.id')
            ->where('job_applications.workspace_id', $workspace->id)
            ->whereIn('job_applications.status', ['pending', 'rejected'])
            ->select('job_applications.*', 'users.name', 'users.email')
            ->latest()
            ->get();

        return view('livewire.business.recruitment-hub', [
            'workspace'    => $workspace,
            'applications' => $applications
        ]);
    }
}
