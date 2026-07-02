<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Só adiciona se NÃO existir
            if (!Schema::hasColumn('users', 'profile_color')) {
                $table->string('profile_color')->default('#6366f1')->after('profile_emoji');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'profile_color')) {
                $table->dropColumn('profile_color');
            }
        });
    }
};
