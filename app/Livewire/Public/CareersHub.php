<?php

namespace App\Livewire\Public;

use App\Models\Candidate;
use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class CareersHub extends Component
{
    use WithFileUploads;

    public $email;
    public $password;
    public $name;
    public $isRegistering = true;

    public $activeSection = 'overview';
    public $profileSaved = false;

    public $headline = '';
    public $phone = '';
    public $location = '';
    public $linkedin_url = '';
    public $portfolio_url = '';
    public $preferred_area = '';
    public $employment_type = '';
    public $availability = '';
    public $salary_expectation = '';
    public $education = '';
    public $experience = '';
    public $skills = '';
    public $languages = '';
    public $certifications = '';
    public $about = '';
    public $cv = null;

    public $selectedCompanyId = null;
    public $selectedCompanyName = '';
    public $applicationNotes = '';

    #[Layout('layouts.guest')]
    public function authenticate()
    {
        $guard = Auth::guard('candidate');

        if ($this->isRegistering) {
            $this->validate([
                'name' => 'required|string|min:3|max:120',
                'email' => 'required|email|max:255|unique:candidates,email',
                'password' => 'required|string|min:8',
            ]);

            $candidate = Candidate::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            $guard->login($candidate);
        } else {
            $this->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

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

    public function loadProfile(): void
    {
        $candidate = Auth::guard('candidate')->user();
        if (! $candidate) {
            return;
        }

        foreach ([
            'name', 'email', 'headline', 'phone', 'location', 'linkedin_url', 'portfolio_url',
            'preferred_area', 'employment_type', 'availability', 'salary_expectation',
            'education', 'experience', 'skills', 'languages', 'certifications', 'about',
        ] as $field) {
            if (property_exists($this, $field) || array_key_exists($field, get_object_vars($this))) {
                $this->{$field} = $candidate->{$field} ?? '';
            }
        }
    }

    public function saveProfile(): void
    {
        $candidate = Auth::guard('candidate')->user();
        abort_unless($candidate, 403);

        $this->validate([
            'name' => 'required|string|min:3|max:120',
            'email' => 'required|email|max:255|unique:candidates,email,'.$candidate->id,
            'headline' => 'nullable|string|max:160',
            'phone' => 'nullable|string|max:40',
            'location' => 'nullable|string|max:120',
            'linkedin_url' => 'nullable|url|max:255',
            'portfolio_url' => 'nullable|url|max:255',
            'preferred_area' => 'nullable|string|max:120',
            'employment_type' => 'nullable|string|max:60',
            'availability' => 'nullable|string|max:80',
            'salary_expectation' => 'nullable|numeric|min:0|max:9999999',
            'education' => 'nullable|string|max:10000',
            'experience' => 'nullable|string|max:10000',
            'skills' => 'nullable|string|max:5000',
            'languages' => 'nullable|string|max:5000',
            'certifications' => 'nullable|string|max:5000',
            'about' => 'nullable|string|max:10000',
            'cv' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $data = [
            'name' => trim($this->name),
            'email' => strtolower(trim($this->email)),
            'headline' => $this->headline ?: null,
            'phone' => $this->phone ?: null,
            'location' => $this->location ?: null,
            'linkedin_url' => $this->linkedin_url ?: null,
            'portfolio_url' => $this->portfolio_url ?: null,
            'preferred_area' => $this->preferred_area ?: null,
            'employment_type' => $this->employment_type ?: null,
            'availability' => $this->availability ?: null,
            'salary_expectation' => $this->salary_expectation !== '' ? $this->salary_expectation : null,
            'education' => $this->education ?: null,
            'experience' => $this->experience ?: null,
            'skills' => $this->skills ?: null,
            'languages' => $this->languages ?: null,
            'certifications' => $this->certifications ?: null,
            'about' => $this->about ?: null,
        ];

        if ($this->cv) {
            $data['cv_path'] = $this->cv->store('candidate-cvs', 'public');
        }

        $candidate->update($data);
        $this->cv = null;
        $this->profileSaved = true;
        $this->dispatch('toast', variant: 'success', text: 'Perfil profissional atualizado.');
    }

    public function openApplication(int $companyId): void
    {
        $company = Workspace::whereKey($companyId)
            ->whereIn('type', ['business', 'bussiness'])
            ->where('recruitment_active', true)
            ->where('recruitment_vacancies', '>', 0)
            ->firstOrFail();

        $candidate = Auth::guard('candidate')->user();
        $alreadyApplied = DB::table('job_applications')
            ->where('candidate_id', $candidate->id)
            ->where('workspace_id', $company->id)
            ->exists();

        if ($alreadyApplied) {
            $this->dispatch('toast', variant: 'info', text: 'Já tens uma candidatura ativa para esta empresa.');
            return;
        }

        if (! $candidate->cv_path) {
            $this->activeSection = 'profile';
            $this->dispatch('toast', variant: 'warning', text: 'Adiciona primeiro o teu CV em PDF ao perfil.');
            return;
        }

        $this->selectedCompanyId = $company->id;
        $this->selectedCompanyName = $company->name;
        $this->applicationNotes = '';
        $this->dispatch('modal-show', name: 'candidate-application-modal');
    }

    public function submitApplication(): void
    {
        $candidate = Auth::guard('candidate')->user();
        abort_unless($candidate, 403);

        $company = Workspace::whereKey($this->selectedCompanyId)
            ->whereIn('type', ['business', 'bussiness'])
            ->where('recruitment_active', true)
            ->where('recruitment_vacancies', '>', 0)
            ->firstOrFail();

        $exists = DB::table('job_applications')
            ->where('candidate_id', $candidate->id)
            ->where('workspace_id', $company->id)
            ->exists();

        if ($exists) {
            $this->dispatch('modal-close', name: 'candidate-application-modal');
            $this->dispatch('toast', variant: 'info', text: 'Esta candidatura já existe.');
            return;
        }

        DB::table('job_applications')->insert([
            'user_id' => null,
            'candidate_id' => $candidate->id,
            'workspace_id' => $company->id,
            'role' => trim($company->recruitment_announcement ?: ($candidate->preferred_area ?: 'Candidatura geral')),
            'phone' => $candidate->phone ?: '',
            'notes' => $this->applicationNotes ?: null,
            'cv_path' => $candidate->cv_path,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->applicationNotes = '';
        $this->dispatch('modal-close', name: 'candidate-application-modal');
        $this->dispatch('toast', text: 'Candidatura submetida com sucesso!', variant: 'success');
    }

    public function logout()
    {
        Auth::guard('candidate')->logout();

        return redirect('/');
    }

    public function render()
    {
        if (! Auth::guard('candidate')->check()) {
            return view('livewire.public.careers-hub');
        }

        $candidate = Auth::guard('candidate')->user();

        $companies = Workspace::query()
            ->whereIn('type', ['business', 'bussiness'])
            ->where('recruitment_active', true)
            ->where('recruitment_vacancies', '>', 0)
            ->orderByDesc('updated_at')
            ->get();

        $applications = DB::table('job_applications')
            ->leftJoin('workspaces', 'job_applications.workspace_id', '=', 'workspaces.id')
            ->where('job_applications.candidate_id', $candidate->id)
            ->select('job_applications.*', 'workspaces.name as company_name', 'workspaces.logo_path as company_logo')
            ->latest('job_applications.created_at')
            ->get();

        return view('livewire.public.candidate-portal', [
            'candidate' => $candidate,
            'companies' => $companies,
            'applications' => $applications,
        ]);
    }
}
