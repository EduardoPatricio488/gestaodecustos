<?php

namespace App\Livewire\Business;

use App\Mail\HiredNotificationMail;
use App\Models\Candidate;
use App\Models\CandidateNotification;
use App\Models\Employee;
use App\Models\RecruitmentJob;
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

    public function toggleActive() { $this->recActive = ! $this->recActive; }

    public function saveSettings()
    {
        $workspace = auth()->user()->currentWorkspace;
        $vacancies = max(0, (int) $this->recVacancies);
        $workspace->update([
            'recruitment_active' => $this->recActive,
            'recruitment_description' => $this->recDesc,
            'recruitment_announcement' => $this->recAnnounce,
            'recruitment_vacancies' => $vacancies,
            'recruitment_extra_info' => $this->recExtraInfo,
        ]);

        // Mantém a montra empresarial e o marketplace de candidatos sincronizados.
        $job = RecruitmentJob::firstOrNew(['workspace_id' => $workspace->id]);
        $job->fill([
            'title' => trim($this->recAnnounce ?: ($workspace->industry ? 'Oportunidade em '.$workspace->industry : 'Oportunidade profissional')),
            'description' => $this->recDesc,
            'requirements' => $this->recExtraInfo,
            'vacancies' => $vacancies,
            'is_active' => (bool) $this->recActive && $vacancies > 0,
            'published_at' => $job->published_at ?: now(),
        ]);
        $job->save();

        session()->flash('published', true);
        $this->dispatch('toast', variant: 'success', text: 'Montra pública e ofertas sincronizadas!');
    }

    public function rejectCandidate($id)
    {
        $workspace = auth()->user()->currentWorkspace;
        $app = DB::table('job_applications')->where('id', $id)->where('workspace_id', $workspace->id)->first();
        if (! $app) return;

        DB::table('job_applications')->where('id', $id)->where('workspace_id', $workspace->id)->update(['status' => 'rejected', 'updated_at' => now()]);
        if (! empty($app->candidate_id)) {
            CandidateNotification::create([
                'candidate_id' => $app->candidate_id,
                'type' => 'application_status',
                'title' => 'Atualização da candidatura',
                'message' => 'A empresa '.$workspace->name.' atualizou o estado da tua candidatura para rejeitada.',
                'url' => '/carreiras',
            ]);
        }
        $this->dispatch('toast', variant: 'warning', text: 'Candidatura arquivada.');
    }

    public function acceptCandidate($id)
    {
        $workspace = auth()->user()->currentWorkspace;
        $app = DB::table('job_applications')->where('id', $id)->where('workspace_id', $workspace->id)->first();
        if (! $app) return;

        if (! empty($app->candidate_id)) {
            $candidate = Candidate::find($app->candidate_id);
            if (! $candidate) return;

            try { Mail::to($candidate->email)->send(new HiredNotificationMail($candidate->name, $workspace->name)); }
            catch (\Throwable $e) { Log::error('Erro ao enviar email de contratação para candidato: '.$e->getMessage()); }

            DB::table('job_applications')->where('id', $id)->where('workspace_id', $workspace->id)->update(['status' => 'accepted', 'updated_at' => now()]);
            CandidateNotification::create(['candidate_id' => $candidate->id, 'type' => 'application_status', 'title' => 'Candidatura aceite', 'message' => 'Parabéns! '.$workspace->name.' aceitou a tua candidatura.', 'url' => '/carreiras']);
            $this->dispatch('toast', variant: 'success', text: 'Candidato aceite e notificado por email.');
            return;
        }

        $user = User::find($app->user_id);
        if (! $user) return;
        try { Mail::to($user->email)->send(new HiredNotificationMail($user->name, $workspace->name)); }
        catch (\Throwable $e) { Log::error('Erro ao enviar email de contratação: '.$e->getMessage()); }

        Employee::firstOrCreate(['workspace_id' => $app->workspace_id, 'user_id' => $app->user_id], ['name' => $user->name, 'role' => $app->role, 'salary' => 0, 'pay_day' => 25, 'active' => true, 'cv_path' => $app->cv_path]);
        DB::table('job_applications')->where('id', $id)->where('workspace_id', $workspace->id)->update(['status' => 'accepted', 'updated_at' => now()]);
        $this->dispatch('toast', variant: 'success', text: 'Colaborador contratado e notificado!');
        return redirect()->route('hub.business.team');
    }

    public function reopenCandidate($id)
    {
        $workspace = auth()->user()->currentWorkspace;
        $app = DB::table('job_applications')->where('id', $id)->where('workspace_id', $workspace->id)->first();
        if (! $app) return;
        DB::table('job_applications')->where('id', $id)->where('workspace_id', $workspace->id)->update(['status' => 'pending', 'updated_at' => now()]);
        if (! empty($app->candidate_id)) CandidateNotification::create(['candidate_id' => $app->candidate_id, 'type' => 'application_status', 'title' => 'Candidatura reaberta', 'message' => 'A empresa '.$workspace->name.' reabriu a tua candidatura.', 'url' => '/carreiras']);
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
            ->select('job_applications.*', DB::raw('COALESCE(candidates.name, users.name) as name'), DB::raw('COALESCE(candidates.email, users.email) as email'), 'candidates.headline as candidate_headline', 'candidates.location as candidate_location', 'candidates.preferred_area as candidate_preferred_area', 'candidates.phone as candidate_phone')
            ->latest('job_applications.created_at')->get();

        return view('livewire.business.recruitment-hub', ['workspace' => $workspace, 'applications' => $applications]);
    }
}
