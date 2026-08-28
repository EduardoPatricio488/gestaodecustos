<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_documents', function (Blueprint $table) {
            // Data de validade (O que causou o erro)
            if (! Schema::hasColumn('business_documents', 'expiry_date')) {
                $table->date('expiry_date')->nullable();
            }

            // Data de emissão (útil para organização)
            if (! Schema::hasColumn('business_documents', 'issue_date')) {
                $table->date('issue_date')->nullable();
            }

            // Estado do documento
            if (! Schema::hasColumn('business_documents', 'status')) {
                $table->string('status')->default('active');
            }
        });
    }

    public function down(): void {}
};
