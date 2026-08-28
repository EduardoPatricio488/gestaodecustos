<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_documents', function (Blueprint $table) {
            // Se a coluna 'name' não existir, cria-a
            if (! Schema::hasColumn('business_documents', 'name')) {
                $table->string('name')->after('user_id');
            }

            // Garante que outras colunas essenciais existem para o Arquivo funcionar
            if (! Schema::hasColumn('business_documents', 'category')) {
                $table->string('category')->nullable()->after('name');
            }
            if (! Schema::hasColumn('business_documents', 'file_path')) {
                $table->string('file_path')->nullable()->after('category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('business_documents', function (Blueprint $table) {
            // Não removemos para evitar perda de dados em caso de rollback acidental
        });
    }
};
