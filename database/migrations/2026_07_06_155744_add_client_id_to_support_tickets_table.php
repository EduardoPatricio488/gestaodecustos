<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            // Adiciona a coluna client_id que está em falta
            if (!Schema::hasColumn('support_tickets', 'client_id')) {
                $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            }

            // Aproveita e garante que a coluna message existe (o erro de integridade anterior pode vir daqui)
            if (!Schema::hasColumn('support_tickets', 'message')) {
                $table->text('message')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropColumn(['client_id', 'message']);
        });
    }
};
