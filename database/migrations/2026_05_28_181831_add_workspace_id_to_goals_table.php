<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('goals', function (Blueprint $table) {
            // Verifica se a coluna já existe para não dar erro
            if (!Schema::hasColumn('goals', 'workspace_id')) {
                $table->foreignId('workspace_id')->nullable()->constrained()->onDelete('cascade');
            }
        });
    }

    public function down(): void {
        Schema::table('goals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('workspace_id');
        });
    }
};
