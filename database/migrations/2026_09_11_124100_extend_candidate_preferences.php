<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            if (! Schema::hasColumn('candidates', 'city')) {
                $table->string('city')->nullable()->after('location');
            }
            if (! Schema::hasColumn('candidates', 'github_url')) {
                $table->string('github_url')->nullable()->after('linkedin_url');
            }
            if (! Schema::hasColumn('candidates', 'website_url')) {
                $table->string('website_url')->nullable()->after('github_url');
            }
            if (! Schema::hasColumn('candidates', 'desired_location')) {
                $table->string('desired_location')->nullable()->after('preferred_area');
            }
            if (! Schema::hasColumn('candidates', 'remote_work')) {
                $table->boolean('remote_work')->default(false)->after('desired_location');
            }
            if (! Schema::hasColumn('candidates', 'hybrid_work')) {
                $table->boolean('hybrid_work')->default(false)->after('remote_work');
            }
            if (! Schema::hasColumn('candidates', 'on_site_work')) {
                $table->boolean('on_site_work')->default(true)->after('hybrid_work');
            }
            if (! Schema::hasColumn('candidates', 'projects')) {
                $table->text('projects')->nullable()->after('certifications');
            }
            if (! Schema::hasColumn('candidates', 'profile_public')) {
                $table->boolean('profile_public')->default(false)->after('cv_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            foreach (['city', 'github_url', 'website_url', 'desired_location', 'remote_work', 'hybrid_work', 'on_site_work', 'projects', 'profile_public'] as $column) {
                if (Schema::hasColumn('candidates', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
