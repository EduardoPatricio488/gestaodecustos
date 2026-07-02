<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table) {

            // Dados bancários
            $table->string('bank_name')->nullable()->after('name');
            $table->string('country')->nullable()->after('bank_name');
            $table->string('iban')->nullable()->after('country');
            $table->string('swift')->nullable()->after('iban');
            $table->string('holder_name')->nullable()->after('swift');

            // Financeiro avançado
            $table->decimal('credit_limit', 12, 2)->nullable()->after('balance');
            $table->decimal('forecast_balance', 12, 2)->nullable()->after('credit_limit');
            $table->unsignedInteger('risk_score')->nullable()->after('forecast_balance');

            // Tags e notas
            $table->json('tags')->nullable()->after('risk_score');
            $table->text('notes')->nullable()->after('tags');
        });
    }

    public function down(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->dropColumn([
                'bank_name',
                'country',
                'iban',
                'swift',
                'holder_name',
                'credit_limit',
                'forecast_balance',
                'risk_score',
                'tags',
                'notes',
            ]);
        });
    }
};
