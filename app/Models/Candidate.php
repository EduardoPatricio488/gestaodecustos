<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Candidate extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'headline',
        'phone',
        'location',
        'linkedin_url',
        'portfolio_url',
        'preferred_area',
        'employment_type',
        'availability',
        'salary_expectation',
        'education',
        'experience',
        'skills',
        'languages',
        'certifications',
        'about',
        'cv_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'salary_expectation' => 'decimal:2',
            'password' => 'hashed',
        ];
    }
}
