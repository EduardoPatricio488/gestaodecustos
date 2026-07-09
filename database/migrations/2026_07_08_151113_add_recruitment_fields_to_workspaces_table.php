<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('workspaces', function (Blueprint $table) {
        // Verifica se a coluna recruitment_active NÃO existe antes de criar
        if (!Schema::hasColumn('workspaces', 'recruitment_active')) {
            $table->boolean('recruitment_active')->default(true);
        }

        // Verifica a descrição
        if (!Schema::hasColumn('workspaces', 'recruitment_description')) {
            $table->text('recruitment_description')->nullable();
        }

        // Verifica o anúncio
        if (!Schema::hasColumn('workspaces', 'recruitment_announcement')) {
            $table->text('recruitment_announcement')->nullable();
        }

        // Verifica as vagas
        if (!Schema::hasColumn('workspaces', 'recruitment_vacancies')) {
            $table->integer('recruitment_vacancies')->default(1);
        }

        // Verifica a info extra
        if (!Schema::hasColumn('workspaces', 'recruitment_extra_info')) {
            $table->text('recruitment_extra_info')->nullable();
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            //
        });
    }
};
