<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('recruitment_jobs')) {
            Schema::create('recruitment_jobs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workspace_id')->constrained('workspaces')->cascadeOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->text('responsibilities')->nullable();
                $table->text('requirements')->nullable();
                $table->text('skills')->nullable();
                $table->text('benefits')->nullable();
                $table->string('location')->nullable();
                $table->string('work_model')->nullable();
                $table->string('contract_type')->nullable();
                $table->decimal('salary_min', 10, 2)->nullable();
                $table->decimal('salary_max', 10, 2)->nullable();
                $table->string('experience_level')->nullable();
                $table->unsignedInteger('vacancies')->default(1);
                $table->boolean('is_active')->default(true);
                $table->timestamp('published_at')->nullable();
                $table->timestamps();
                $table->index(['workspace_id', 'is_active']);
            });
        }

        if (! Schema::hasTable('candidate_saved_jobs')) {
            Schema::create('candidate_saved_jobs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
                $table->foreignId('recruitment_job_id')->constrained('recruitment_jobs')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['candidate_id', 'recruitment_job_id']);
            });
        }

        if (! Schema::hasTable('candidate_notifications')) {
            Schema::create('candidate_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
                $table->string('type')->default('system');
                $table->string('title');
                $table->text('message');
                $table->string('url')->nullable();
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                $table->index(['candidate_id', 'read_at']);
            });
        }

        // Sincroniza a atual "montra" empresarial para ofertas reais sem apagar a configuração existente.
        $workspaces = DB::table('workspaces')
            ->whereIn('type', ['business', 'bussiness'])
            ->where('recruitment_active', true)
            ->where('recruitment_vacancies', '>', 0)
            ->get();

        foreach ($workspaces as $workspace) {
            $exists = DB::table('recruitment_jobs')->where('workspace_id', $workspace->id)->exists();
            if ($exists) {
                continue;
            }

            DB::table('recruitment_jobs')->insert([
                'workspace_id' => $workspace->id,
                'title' => trim($workspace->recruitment_announcement ?: ($workspace->industry ? 'Oportunidade em '.$workspace->industry : 'Oportunidade profissional')),
                'description' => $workspace->recruitment_description,
                'requirements' => $workspace->recruitment_extra_info,
                'vacancies' => max(1, (int) $workspace->recruitment_vacancies),
                'is_active' => true,
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_notifications');
        Schema::dropIfExists('candidate_saved_jobs');
        Schema::dropIfExists('recruitment_jobs');
    }
};
