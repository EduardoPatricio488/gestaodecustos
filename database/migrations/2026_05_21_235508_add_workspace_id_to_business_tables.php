<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
{
    // Verifica a tabela invoices
    Schema::table('invoices', function (Blueprint $table) {
        if (!Schema::hasColumn('invoices', 'workspace_id')) {
            $table->foreignId('workspace_id')->nullable()->after('user_id')->constrained()->onDelete('cascade');
        }
    });

    // Verifica a tabela employees
    Schema::table('employees', function (Blueprint $table) {
        if (!Schema::hasColumn('employees', 'workspace_id')) {
            $table->foreignId('workspace_id')->nullable()->after('user_id')->constrained()->onDelete('cascade');
        }
    });
}

    public function down(): void {
        Schema::table('invoices', function (Blueprint $table) { $table->dropColumn('workspace_id'); });
        Schema::table('employees', function (Blueprint $table) { $table->dropColumn('workspace_id'); });
    }
};
