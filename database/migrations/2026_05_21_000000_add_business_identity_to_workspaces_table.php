<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            // Identidade Visual e Legal
            if (! Schema::hasColumn('workspaces', 'logo_path')) {
                $table->string('logo_path')->nullable()->after('name');
            }
            if (! Schema::hasColumn('workspaces', 'legal_name')) {
                $table->string('legal_name')->nullable()->after('name'); // Nome oficial
            }
            if (! Schema::hasColumn('workspaces', 'tax_number')) {
                $table->string('tax_number', 20)->nullable()->after('legal_name'); // NIF/CNPJ
            }
            if (! Schema::hasColumn('workspaces', 'industry')) {
                $table->string('industry')->nullable(); // Setor: Tecnologia, Retalho, etc.
            }

            // Configurações de Gestão
            if (! Schema::hasColumn('workspaces', 'currency')) {
                $table->string('currency', 3)->default('EUR');
            }
            if (! Schema::hasColumn('workspaces', 'initial_capital')) {
                $table->decimal('initial_capital', 15, 2)->default(0); // Capital Social
            }
            if (! Schema::hasColumn('workspaces', 'business_email')) {
                $table->string('business_email')->nullable();
            }
            if (! Schema::hasColumn('workspaces', 'address')) {
                $table->string('address')->nullable();
            }

            // Datas Fiscais
            if (! Schema::hasColumn('workspaces', 'fiscal_year_start')) {
                $table->integer('fiscal_year_start')->default(1); // Janeiro
            }
        });
    }

    public function down(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropColumn([
                'logo_path', 'legal_name', 'tax_number', 'industry',
                'currency', 'initial_capital', 'business_email', 'address', 'fiscal_year_start',
            ]);
        });
    }
};
