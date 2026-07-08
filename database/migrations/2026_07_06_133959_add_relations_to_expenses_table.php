<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('expenses', function (Blueprint $table) {
        // Só adiciona project_id se ele NÃO existir
        if (!Schema::hasColumn('expenses', 'project_id')) {
            $table->unsignedBigInteger('project_id')->nullable()->after('workspace_id');
        }

        // Só adiciona task_id se ele NÃO existir
        if (!Schema::hasColumn('expenses', 'task_id')) {
            $table->unsignedBigInteger('task_id')->nullable()->after('project_id');
        }
    });
}

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['project_id', 'task_id']);
        });
    }
};
