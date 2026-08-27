<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Verificamos se a coluna já não existe para evitar erros
            if (! Schema::hasColumn('projects', 'profit')) {
                // 🔥 Removido o ->after('margin') para garantir que funciona no MySQL
                // Aumentado para 15,2 para suportar valores financeiros maiores
                $table->decimal('profit', 15, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'profit')) {
                $table->dropColumn('profit');
            }
        });
    }
};
