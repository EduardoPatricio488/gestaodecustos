<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Candidate extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'headline', 'phone', 'location', 'city',
        'linkedin_url', 'github_url', 'portfolio_url', 'website_url', 'preferred_area',
        'desired_location', 'remote_work', 'hybrid_work', 'on_site_work', 'employment_type',
        'availability', 'salary_expectation', 'education', 'experience', 'skills',
        'languages', 'certifications', 'projects', 'about', 'cv_path', 'profile_public',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'salary_expectation' => 'decimal:2',
            'remote_work' => 'boolean',
            'hybrid_work' => 'boolean',
            'on_site_work' => 'boolean',
            'profile_public' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function savedJobs(): BelongsToMany
    {
        return $this->belongsToMany(RecruitmentJob::class, 'candidate_saved_jobs', 'candidate_id', 'recruitment_job_id')->withTimestamps();
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(CandidateNotification::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'candidate_id');
    }
}
