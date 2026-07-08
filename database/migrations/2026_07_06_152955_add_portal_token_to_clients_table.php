<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('clients', function (Blueprint $table) {
        // Só tenta criar a coluna se ela NÃO existir
        if (!Schema::hasColumn('clients', 'portal_token')) {
            $table->string('portal_token')->unique()->nullable()->after('notes');
        }
    });
}

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('portal_token');
        });
    }
};
