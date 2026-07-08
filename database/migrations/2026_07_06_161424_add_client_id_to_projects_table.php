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
    Schema::table('projects', function (Blueprint $table) {
        // Criar a relação com a tabela clients
        $table->foreignId('client_id')->nullable()->after('workspace_id')->constrained('clients')->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('projects', function (Blueprint $table) {
        $table->dropForeign(['client_id']);
        $table->dropColumn('client_id');
    });
}
};
