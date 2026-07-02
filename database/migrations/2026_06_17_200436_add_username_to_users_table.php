<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        // Username único para perfis sociais (/social/u/{username})
        if (!Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->nullable()->unique()->after('name');
            });

            // Gera um username inicial para utilizadores existentes a partir do nome
            DB::table('users')->select('id', 'name')->orderBy('id')->each(function ($user) {
                $base = Str::slug($user->name, '');
                $base = $base !== '' ? $base : 'user'.$user->id;
                $candidate = $base;
                $i = 1;

                while (DB::table('users')->where('username', $candidate)->where('id', '!=', $user->id)->exists()) {
                    $candidate = $base.$i;
                    $i++;
                }

                DB::table('users')->where('id', $user->id)->update(['username' => $candidate]);
            });
        }

        // Bio curta opcional para o perfil social
        if (!Schema::hasColumn('users', 'social_bio')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('social_bio', 160)->nullable()->after('username');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'social_bio')) {
                $table->dropColumn('social_bio');
            }
            if (Schema::hasColumn('users', 'username')) {
                $table->dropColumn('username');
            }
        });
    }
};
