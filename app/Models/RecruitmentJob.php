<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RecruitmentJob extends Model
{
    protected $fillable = [
        'workspace_id', 'title', 'description', 'responsibilities', 'requirements',
        'skills', 'benefits', 'location', 'work_model', 'contract_type', 'salary_min',
        'salary_max', 'experience_level', 'vacancies', 'is_active', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
            'is_active' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function savedBy(): BelongsToMany
    {
        return $this->belongsToMany(Candidate::class, 'candidate_saved_jobs', 'recruitment_job_id', 'candidate_id')->withTimestamps();
    }
}
