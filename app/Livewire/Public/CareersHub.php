<?php

namespace App\Livewire\Public;

use App\Models\Candidate;
use App\Models\CandidateNotification;
use App\Models\RecruitmentJob;
use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class CareersHub extends Component
{
    use WithFileUploads;

    public $email = '';

    public $password = '';

    public $name = '';

    public $isRegistering = true;

    public $activeSection = 'overview';

    public $search = '';

    public $locationFilter = '';

    public $workModelFilter = '';

    public $contractFilter = '';

    public $areaFilter = '';

    public $sortBy = 'recent';

    public $companySearch = '';

    public $profileSaved = false;

    public $showPublicProfile = false;

    public $selectedJob = null;

    public $selectedCompany = null;

    public $applicationNotes = '';

    public $headline = '';

    public $phone = '';

    public $location = '';

    public $city = '';

    public $linkedin_url = '';

    public $github_url = '';

    public $portfolio_url = '';

    public $website_url = '';

    public $preferred_area = '';

    public $desired_location = '';

    public $remote_work = false;

    public $hybrid_work = false;

    public $on_site_work = true;

    public $employment_type = '';

    public $availability = '';

    public $salary_expectation = '';

    public $education = '';

    public $experience = '';

    public $skills = '';

    public $languages = '';

    public $certifications = '';

    public $projects = '';

    public $about = '';

    public $cv = null;

    public $profile_public = false;

    #[Layout('layouts.guest')]
    public function authenticate()
    {
        $guard = Auth::guard('candidate');

        if ($this->isRegistering) {
            $this->validate(['name' => 'required|string|min:3|max:120', 'email' => 'required|email|max:255|unique:candidates,email', 'password' => 'required|string|min:8']);
            $candidate = Candidate::create(['name' => trim($this->name), 'email' => strtolower(trim($this->email)), 'password' => $this->password]);
            $guard->login($candidate);
        } else {
            $this->validate(['email' => 'required|email', 'password' => 'required']);
            if (! $guard->attempt(['email' => $this->email, 'password' => $this->password])) {
                session()->flash('error', 'Credenciais inválidas.');

                return;
            }
        }

        $this->reset(['password']);

        return redirect()->route('careers.apply');
    }

    public function mount(): void
    {
        if (Auth::guard('candidate')->check()) {
            $this->loadProfile();
        }
    }

    protected function loadProfile(): void
    {
        $candidate = Auth::guard('candidate')->user();
        if (! $candidate) {
            return;
        }

        foreach (['name', 'email', 'headline', 'phone', 'location', 'city', 'linkedin_url', 'github_url', 'portfolio_url', 'website_url', 'preferred_area', 'desired_location', 'remote_work', 'hybrid_work', 'on_site_work', 'employment_type', 'availability', 'salary_expectation', 'education', 'experience', 'skills', 'languages', 'certifications', 'projects', 'about', 'profile_public'] as $field) {
            $this->{$field} = $candidate->{$field} ?? (in_array($field, ['remote_work', 'hybrid_work', 'profile_public']) ? false : '');
        }
        $this->on_site_work = (bool) $candidate->on_site_work;
    }

    public function saveProfile(): void
    {
        $candidate = Auth::guard('candidate')->user();
        abort_unless($candidate, 403);

        $this->validate([
            'name' => 'required|string|min:3|max:120',
            'email' => ['required', 'email', 'max:255', Rule::unique('candidates', 'email')->ignore($candidate->id)],
            'headline' => 'nullable|string|max:160', 'phone' => 'nullable|string|max:40', 'location' => 'nullable|string|max:120', 'city' => 'nullable|string|max:120',
            'linkedin_url' => 'nullable|url|max:255', 'github_url' => 'nullable|url|max:255', 'portfolio_url' => 'nullable|url|max:255', 'website_url' => 'nullable|url|max:255',
            'preferred_area' => 'nullable|string|max:120', 'desired_location' => 'nullable|string|max:120', 'employment_type' => 'nullable|string|max:80', 'availability' => 'nullable|string|max:80',
            'salary_expectation' => 'nullable|numeric|min:0|max:9999999', 'education' => 'nullable|string|max:10000', 'experience' => 'nullable|string|max:10000',
            'skills' => 'nullable|string|max:5000', 'languages' => 'nullable|string|max:5000', 'certifications' => 'nullable|string|max:5000', 'projects' => 'nullable|string|max:10000', 'about' => 'nullable|string|max:10000',
            'cv' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $data = collect([
            'name' => trim($this->name), 'email' => strtolower(trim($this->email)), 'headline' => $this->headline, 'phone' => $this->phone, 'location' => $this->location, 'city' => $this->city,
            'linkedin_url' => $this->linkedin_url, 'github_url' => $this->github_url, 'portfolio_url' => $this->portfolio_url, 'website_url' => $this->website_url,
            'preferred_area' => $this->preferred_area, 'desired_location' => $this->desired_location, 'remote_work' => $this->remote_work, 'hybrid_work' => $this->hybrid_work, 'on_site_work' => $this->on_site_work,
            'employment_type' => $this->employment_type, 'availability' => $this->availability, 'salary_expectation' => $this->salary_expectation ?: null, 'education' => $this->education,
            'experience' => $this->experience, 'skills' => $this->skills, 'languages' => $this->languages, 'certifications' => $this->certifications, 'projects' => $this->projects, 'about' => $this->about, 'profile_public' => $this->profile_public,
        ])->map(fn ($value) => is_string($value) && trim($value) === '' ? null : $value)->all();

        if ($this->cv) {
            if ($candidate->cv_path && Storage::disk('public')->exists($candidate->cv_path)) {
                Storage::disk('public')->delete($candidate->cv_path);
            }
            $data['cv_path'] = $this->cv->store('candidate-cvs', 'public');
        }

        $candidate->update($data);
        $this->cv = null;
        $this->profileSaved = true;
        $this->dispatch('toast', variant: 'success', text: 'Perfil profissional atualizado.');
    }

    public function removeCv(): void
    {
        $candidate = Auth::guard('candidate')->user();
        abort_unless($candidate, 403);
        if ($candidate->cv_path && Storage::disk('public')->exists($candidate->cv_path)) {
            Storage::disk('public')->delete($candidate->cv_path);
        }
        $candidate->update(['cv_path' => null]);
        $this->dispatch('toast', variant: 'success', text: 'CV removido.');
    }

    public function downloadCv()
    {
        $candidate = Auth::guard('candidate')->user();
        abort_unless($candidate, 403);
        abort_unless($candidate->cv_path && Storage::disk('public')->exists($candidate->cv_path), 404);

        return Storage::disk('public')->download($candidate->cv_path, 'CV-'.$candidate->name.'.pdf');
    }

    public function toggleSavedJob(int $jobId): void
    {
        $candidate = Auth::guard('candidate')->user();
        abort_unless($candidate, 403);
        RecruitmentJob::whereKey($jobId)->where('is_active', true)->firstOrFail();
        $query = DB::table('candidate_saved_jobs')->where('candidate_id', $candidate->id)->where('recruitment_job_id', $jobId);
        if ($query->exists()) {
            $query->delete();
            $this->dispatch('toast', text: 'Oferta removida das guardadas.', variant: 'info');
        } else {
            DB::table('candidate_saved_jobs')->insert(['candidate_id' => $candidate->id, 'recruitment_job_id' => $jobId, 'created_at' => now(), 'updated_at' => now()]);
            $this->dispatch('toast', text: 'Oferta guardada.', variant: 'success');
        }
    }

    public function openJob(int $jobId): void
    {
        $this->selectedJob = RecruitmentJob::with('workspace')->whereKey($jobId)->where('is_active', true)->firstOrFail();
        $this->dispatch('modal-show', name: 'job-details-modal');
    }

    public function openCompany(int $companyId): void
    {
        $this->selectedCompany = Workspace::query()->whereKey($companyId)->whereIn('type', ['business', 'bussiness'])->firstOrFail();
        $this->dispatch('modal-show', name: 'company-details-modal');
    }

    public function openApplication(int $jobId): void
    {
        $candidate = Auth::guard('candidate')->user();
        abort_unless($candidate, 403);
        $job = RecruitmentJob::with('workspace')->whereKey($jobId)->where('is_active', true)->firstOrFail();
        if (DB::table('job_applications')->where('candidate_id', $candidate->id)->where('recruitment_job_id', $job->id)->exists()) {
            $this->dispatch('toast', variant: 'info', text: 'Já te candidataste a esta oferta.');

            return;
        }
        if (! $candidate->cv_path) {
            $this->activeSection = 'profile';
            $this->dispatch('toast', variant: 'warning', text: 'Adiciona primeiro o teu CV em PDF ao perfil.');

            return;
        }
        $this->selectedJob = $job;
        $this->applicationNotes = '';
        $this->dispatch('modal-show', name: 'candidate-application-modal');
    }

    public function submitApplication(): void
    {
        $candidate = Auth::guard('candidate')->user();
        abort_unless($candidate, 403);
        $job = RecruitmentJob::with('workspace')->whereKey($this->selectedJob?->id)->where('is_active', true)->firstOrFail();
        if (! $candidate->cv_path) {
            return;
        }
        if (DB::table('job_applications')->where('candidate_id', $candidate->id)->where('recruitment_job_id', $job->id)->exists()) {
            return;
        }

        DB::table('job_applications')->insert([
            'user_id' => null, 'candidate_id' => $candidate->id, 'workspace_id' => $job->workspace_id, 'recruitment_job_id' => $job->id,
            'role' => $job->title, 'phone' => $candidate->phone ?: '', 'notes' => $this->applicationNotes ?: null, 'cv_path' => $candidate->cv_path,
            'status' => 'pending', 'created_at' => now(), 'updated_at' => now(),
        ]);
        CandidateNotification::create(['candidate_id' => $candidate->id, 'type' => 'application', 'title' => 'Candidatura enviada', 'message' => 'A tua candidatura para '.$job->title.' em '.$job->workspace->name.' foi enviada.', 'url' => '/carreiras']);
        $this->applicationNotes = '';
        $this->dispatch('modal-close', name: 'candidate-application-modal');
        $this->dispatch('toast', text: 'Candidatura submetida com sucesso!', variant: 'success');
    }

    public function markNotificationRead(int $id): void
    {
        $candidate = Auth::guard('candidate')->user();
        abort_unless($candidate, 403);
        CandidateNotification::whereKey($id)->where('candidate_id', $candidate->id)->update(['read_at' => now()]);
    }

    public function markAllNotificationsRead(): void
    {
        $candidate = Auth::guard('candidate')->user();
        abort_unless($candidate, 403);
        CandidateNotification::where('candidate_id', $candidate->id)->whereNull('read_at')->update(['read_at' => now()]);
    }

    public function togglePublicProfile(): void
    {
        $candidate = Auth::guard('candidate')->user();
        abort_unless($candidate, 403);
        $candidate->update(['profile_public' => ! $candidate->profile_public]);
        $this->profile_public = $candidate->profile_public;
    }

    public function logout()
    {
        Auth::guard('candidate')->logout();

        return redirect('/');
    }

    protected function ensureProfileNotification(Candidate $candidate, int $completion): void
    {
        if ($completion < 80 && ! CandidateNotification::where('candidate_id', $candidate->id)->where('type', 'profile')->whereDate('created_at', today())->exists()) {
            CandidateNotification::create(['candidate_id' => $candidate->id, 'type' => 'profile', 'title' => 'Completa o teu perfil', 'message' => 'O teu perfil está a '.$completion.'%. Adiciona mais informação para melhorares as tuas candidaturas.', 'url' => '/carreiras']);
        }
    }

    public function render()
    {
        if (! Auth::guard('candidate')->check()) {
            return view('livewire.public.careers-hub');
        }

        $candidate = Auth::guard('candidate')->user();
        $profileFields = ['headline', 'phone', 'location', 'city', 'linkedin_url', 'github_url', 'portfolio_url', 'preferred_area', 'desired_location', 'employment_type', 'availability', 'education', 'experience', 'skills', 'languages', 'certifications', 'projects', 'about', 'cv_path'];
        $filled = collect($profileFields)->filter(fn ($field) => filled($candidate->{$field}))->count();
        $completion = (int) round(($filled / count($profileFields)) * 100);
        $this->ensureProfileNotification($candidate, $completion);

        $query = RecruitmentJob::query()->with('workspace')->where('is_active', true)->where('vacancies', '>', 0);
        if (filled($this->search)) {
            $query->where(fn ($q) => $q->where('title', 'like', '%'.$this->search.'%')->orWhere('description', 'like', '%'.$this->search.'%')->orWhere('skills', 'like', '%'.$this->search.'%'));
        }
        if (filled($this->locationFilter)) {
            $query->where('location', 'like', '%'.$this->locationFilter.'%');
        }
        if (filled($this->workModelFilter)) {
            $query->where('work_model', $this->workModelFilter);
        }
        if (filled($this->contractFilter)) {
            $query->where('contract_type', $this->contractFilter);
        }
        if (filled($this->areaFilter)) {
            $query->where('title', 'like', '%'.$this->areaFilter.'%');
        }
        $jobs = $query->when($this->sortBy === 'salary', fn ($q) => $q->orderByDesc('salary_max'))->when($this->sortBy === 'company', fn ($q) => $q->orderBy('workspace_id'))->when($this->sortBy === 'recent', fn ($q) => $q->latest('published_at'))->get();

        $companiesQuery = Workspace::query()->whereIn('type', ['business', 'bussiness'])->whereHas('recruitmentJobs', fn ($q) => $q->where('is_active', true));
        if (filled($this->companySearch)) {
            $companiesQuery->where(fn ($q) => $q->where('name', 'like', '%'.$this->companySearch.'%')->orWhere('industry', 'like', '%'.$this->companySearch.'%')->orWhere('address', 'like', '%'.$this->companySearch.'%'));
        }
        $companies = $companiesQuery->withCount(['recruitmentJobs' => fn ($q) => $q->where('is_active', true)])->latest('updated_at')->get();

        $applications = DB::table('job_applications')->leftJoin('workspaces', 'job_applications.workspace_id', '=', 'workspaces.id')->leftJoin('recruitment_jobs', 'job_applications.recruitment_job_id', '=', 'recruitment_jobs.id')->where('job_applications.candidate_id', $candidate->id)->select('job_applications.*', 'workspaces.name as company_name', 'workspaces.logo_path as company_logo', 'recruitment_jobs.title as job_title')->latest('job_applications.created_at')->get();
        $savedJobIds = DB::table('candidate_saved_jobs')->where('candidate_id', $candidate->id)->pluck('recruitment_job_id')->all();
        $savedJobs = RecruitmentJob::with('workspace')->whereIn('id', $savedJobIds)->where('is_active', true)->latest()->get();
        $notifications = CandidateNotification::where('candidate_id', $candidate->id)->latest()->limit(30)->get();
        $unreadNotifications = $notifications->whereNull('read_at')->count();

        return view('livewire.public.candidate-portal', compact('candidate','jobs','companies','applications','savedJobs','savedJobIds','notifications','unreadNotifications','completion'));
    }
}
