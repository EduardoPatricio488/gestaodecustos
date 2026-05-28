<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Corrigir tabela de Assinaturas
        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                if (!Schema::hasColumn('subscriptions', 'workspace_id')) {
                    $table->foreignId('workspace_id')->nullable()->constrained()->onDelete('cascade');
                }
            });
        }

        // Corrigir tabela de Dívidas
        if (Schema::hasTable('debts')) {
            Schema::table('debts', function (Blueprint $table) {
                if (!Schema::hasColumn('debts', 'workspace_id')) {
                    $table->foreignId('workspace_id')->nullable()->constrained()->onDelete('cascade');
                }
            });
        }
    }

    public function down(): void {
        Schema::table('subscriptions', fn (Blueprint $table) => $table->dropColumn('workspace_id'));
        Schema::table('debts', fn (Blueprint $table) => $table->dropColumn('workspace_id'));
    }
};
