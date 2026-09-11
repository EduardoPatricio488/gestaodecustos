<?php

namespace App\Livewire\Business;

use App\Mail\HiredNotificationMail;
use App\Models\Candidate;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class RecruitmentHub extends Component
{
    use WithFileUploads;

    public $recActive;
    public $recDesc;
    public $recAnnounce;
    public $recVacancies;
    public $recExtraInfo;

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
        $this->recActive = ! $this->recActive;
    }

    public function saveSettings()
    {
        $workspace = auth()->user()->currentWorkspace;
        $workspace->update([
            'recruitment_active' => $this->recActive,
            'recruitment_description' => $this->recDesc,
            'recruitment_announcement' => $this->recAnnounce,
            'recruitment_vacancies' => max(0, (int) $this->recVacancies),
            'recruitment_extra_info' => $this->recExtraInfo,
        ]);

        session()->flash('published', true);
        $this->dispatch('toast', variant: 'success', text: 'Montra pública atualizada!');
    }

    public function rejectCandidate($id)
    {
        $workspace = auth()->user()->currentWorkspace;
        DB::table('job_applications')
            ->where('id', $id)
            ->where('workspace_id', $workspace->id)
            ->update(['status' => 'rejected', 'updated_at' => now()]);

        $this->dispatch('toast', variant: 'warning', text: 'Candidatura arquivada.');
    }

    public function acceptCandidate($id)
    {
        $workspace = auth()->user()->currentWorkspace;
        $app = DB::table('job_applications')
            ->where('id', $id)
            ->where('workspace_id', $workspace->id)
            ->first();

        if (! $app) {
            return;
        }

        if (! empty($app->candidate_id)) {
            $candidate = Candidate::find($app->candidate_id);
            if (! $candidate) {
                return;
            }

            try {
                Mail::to($candidate->email)->send(new HiredNotificationMail($candidate->name, $workspace->name));
            } catch (\Throwable $e) {
                Log::error('Erro ao enviar email de contratação para candidato: '.$e->getMessage());
            }

            DB::table('job_applications')
                ->where('id', $id)
                ->where('workspace_id', $workspace->id)
                ->update(['status' => 'accepted', 'updated_at' => now()]);

            $this->dispatch('toast', variant: 'success', text: 'Candidato aceite e notificado por email.');

            return;
        }

        // Compatibilidade com candidaturas antigas associadas a utilizadores normais.
        $user = User::find($app->user_id);
        if (! $user) {
            return;
        }

        try {
            Mail::to($user->email)->send(new HiredNotificationMail($user->name, $workspace->name));
        } catch (\Throwable $e) {
            Log::error('Erro ao enviar email de contratação: '.$e->getMessage());
        }

        Employee::firstOrCreate(
            [
                'workspace_id' => $app->workspace_id,
                'user_id' => $app->user_id,
            ],
            [
                'name' => $user->name,
                'role' => $app->role,
                'salary' => 0,
                'pay_day' => 25,
                'active' => true,
                'cv_path' => $app->cv_path,
            ]
        );

        DB::table('job_applications')
            ->where('id', $id)
            ->where('workspace_id', $workspace->id)
            ->update(['status' => 'accepted', 'updated_at' => now()]);

        $this->dispatch('toast', variant: 'success', text: 'Colaborador contratado e notificado!');

        return redirect()->route('hub.business.team');
    }

    public function reopenCandidate($id)
    {
        $workspace = auth()->user()->currentWorkspace;
        DB::table('job_applications')
            ->where('id', $id)
            ->where('workspace_id', $workspace->id)
            ->update(['status' => 'pending', 'updated_at' => now()]);

        $this->dispatch('toast', text: 'Candidatura reaberta.');
    }

    public function render()
    {
        $workspace = Auth::user()->currentWorkspace;

        $applications = DB::table('job_applications')
            ->leftJoin('users', 'job_applications.user_id', '=', 'users.id')
            ->leftJoin('candidates', 'job_applications.candidate_id', '=', 'candidates.id')
            ->where('job_applications.workspace_id', $workspace->id)
            ->whereIn('job_applications.status', ['pending', 'rejected'])
            ->select(
                'job_applications.*',
                DB::raw('COALESCE(candidates.name, users.name) as name'),
                DB::raw('COALESCE(candidates.email, users.email) as email'),
                'candidates.headline as candidate_headline',
                'candidates.location as candidate_location',
                'candidates.preferred_area as candidate_preferred_area',
                'candidates.phone as candidate_phone'
            )
            ->latest('job_applications.created_at')
            ->get();

        return view('livewire.business.recruitment-hub', [
            'workspace' => $workspace,
            'applications' => $applications,
        ]);
    }
}
