<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'last_ip')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->text('last_ip')->nullable()->change();
            });
        }

        User::query()
            ->whereNotNull('last_ip')
            ->chunkById(100, function ($users): void {
                foreach ($users as $user) {
                    $lastIp = $user->getRawOriginal('last_ip');

                    if ($lastIp === null || $lastIp === '') {
                        continue;
                    }

                    $user->last_ip = $user->last_ip;
                    $user->saveQuietly();
                }
            });
    }

    public function down(): void
    {
        // Do not shrink this column: encrypted values may exceed VARCHAR(255).
    }
};
