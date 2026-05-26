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
            $table->string('logo_path')->nullable()->after('name');
            $table->string('legal_name')->nullable()->after('name'); // Nome oficial
            $table->string('tax_number', 20)->nullable()->after('legal_name'); // NIF/CNPJ
            $table->string('industry')->nullable(); // Setor: Tecnologia, Retalho, etc.

            // Configurações de Gestão
            $table->string('currency', 3)->default('EUR');
            $table->decimal('initial_capital', 15, 2)->default(0); // Capital Social
            $table->string('business_email')->nullable();
            $table->string('address')->nullable();

            // Datas Fiscais
            $table->integer('fiscal_year_start')->default(1); // Janeiro
        });
    }

    public function down(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropColumn([
                'logo_path', 'legal_name', 'tax_number', 'industry',
                'currency', 'initial_capital', 'business_email', 'address', 'fiscal_year_start'
            ]);
        });
    }
};
