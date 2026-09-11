<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            if (! Schema::hasColumn('job_applications', 'candidate_id')) {
                $table->foreignId('candidate_id')->nullable()->after('user_id')->constrained('candidates')->nullOnDelete();
            }
        });

        Schema::table('job_applications', function (Blueprint $table) {
            if (Schema::hasColumn('job_applications', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            if (Schema::hasColumn('job_applications', 'candidate_id')) {
                $table->dropForeign(['candidate_id']);
                $table->dropColumn('candidate_id');
            }

            if (Schema::hasColumn('job_applications', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable(false)->change();
            }
        });
    }
};
