<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Adiciona profile_emoji se não existir
            if (! Schema::hasColumn('users', 'profile_emoji')) {
                $table->string('profile_emoji')->nullable();
            }

            // Adiciona profile_color se não existir
            if (! Schema::hasColumn('users', 'profile_color')) {
                $table->string('profile_color')->default('#6366f1');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_emoji', 'profile_color']);
        });
    }
};
