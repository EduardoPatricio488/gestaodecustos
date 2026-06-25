<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $blueprint) {
            // Adiciona as colunas necessárias
            $blueprint->timestamp('last_login_at')->nullable();
            $blueprint->string('last_ip')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['last_login_at', 'last_ip']);
        });
    }
};
