<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            if (! Schema::hasColumn('job_applications', 'recruitment_job_id')) {
                $table->foreignId('recruitment_job_id')->nullable()->after('workspace_id')->constrained('recruitment_jobs')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            if (Schema::hasColumn('job_applications', 'recruitment_job_id')) {
                $table->dropForeign(['recruitment_job_id']);
                $table->dropColumn('recruitment_job_id');
            }
        });
    }
};
