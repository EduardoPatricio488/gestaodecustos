<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            if (! Schema::hasColumn('candidates', 'headline')) {
                $table->string('headline')->nullable()->after('name');
            }
            if (! Schema::hasColumn('candidates', 'phone')) {
                $table->string('phone', 40)->nullable()->after('email');
            }
            if (! Schema::hasColumn('candidates', 'location')) {
                $table->string('location')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('candidates', 'linkedin_url')) {
                $table->string('linkedin_url')->nullable()->after('location');
            }
            if (! Schema::hasColumn('candidates', 'portfolio_url')) {
                $table->string('portfolio_url')->nullable()->after('linkedin_url');
            }
            if (! Schema::hasColumn('candidates', 'preferred_area')) {
                $table->string('preferred_area')->nullable()->after('portfolio_url');
            }
            if (! Schema::hasColumn('candidates', 'employment_type')) {
                $table->string('employment_type')->nullable()->after('preferred_area');
            }
            if (! Schema::hasColumn('candidates', 'availability')) {
                $table->string('availability')->nullable()->after('employment_type');
            }
            if (! Schema::hasColumn('candidates', 'salary_expectation')) {
                $table->decimal('salary_expectation', 10, 2)->nullable()->after('availability');
            }
            if (! Schema::hasColumn('candidates', 'education')) {
                $table->text('education')->nullable()->after('salary_expectation');
            }
            if (! Schema::hasColumn('candidates', 'experience')) {
                $table->text('experience')->nullable()->after('education');
            }
            if (! Schema::hasColumn('candidates', 'skills')) {
                $table->text('skills')->nullable()->after('experience');
            }
            if (! Schema::hasColumn('candidates', 'languages')) {
                $table->text('languages')->nullable()->after('skills');
            }
            if (! Schema::hasColumn('candidates', 'certifications')) {
                $table->text('certifications')->nullable()->after('languages');
            }
            if (! Schema::hasColumn('candidates', 'about')) {
                $table->text('about')->nullable()->after('certifications');
            }
            if (! Schema::hasColumn('candidates', 'cv_path')) {
                $table->string('cv_path')->nullable()->after('about');
            }
        });
    }

    public function down(): void
    {
        $columns = [
            'headline', 'phone', 'location', 'linkedin_url', 'portfolio_url',
            'preferred_area', 'employment_type', 'availability', 'salary_expectation',
            'education', 'experience', 'skills', 'languages', 'certifications',
            'about', 'cv_path',
        ];

        Schema::table('candidates', function (Blueprint $table) use ($columns) {
            foreach ($columns as $column) {
                if (Schema::hasColumn('candidates', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
