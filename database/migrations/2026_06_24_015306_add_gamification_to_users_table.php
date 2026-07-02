<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona as colunas de Gamificação à tabela de utilizadores.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Adicionamos os campos logo a seguir à coluna 'role' que criámos antes
            if (!Schema::hasColumn('users', 'xp')) {
                $table->integer('xp')->default(0)->after('role');
            }
            if (!Schema::hasColumn('users', 'level')) {
                $table->integer('level')->default(1)->after('xp');
            }
            if (!Schema::hasColumn('users', 'points')) {
                $table->integer('points')->default(0)->after('level');
            }
            if (!Schema::hasColumn('users', 'streak')) {
                $table->integer('streak')->default(0)->after('points');
            }
            if (!Schema::hasColumn('users', 'badges')) {
                $table->json('badges')->nullable()->after('streak');
            }
        });
    }

    /**
     * Remove as colunas caso precises de fazer rollback.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['xp', 'level', 'points', 'streak', 'badges']);
        });
    }
};
