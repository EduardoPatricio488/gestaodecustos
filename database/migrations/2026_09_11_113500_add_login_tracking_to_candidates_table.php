<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            if (! Schema::hasColumn('candidates', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('email_verified_at');
            }

            if (! Schema::hasColumn('candidates', 'last_ip')) {
                $table->string('last_ip', 45)->nullable()->after('last_login_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            if (Schema::hasColumn('candidates', 'last_ip')) {
                $table->dropColumn('last_ip');
            }

            if (Schema::hasColumn('candidates', 'last_login_at')) {
                $table->dropColumn('last_login_at');
            }
        });
    }
};
