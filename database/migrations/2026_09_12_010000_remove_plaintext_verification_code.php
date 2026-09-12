<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'verification_code')) {
            DB::table('users')->whereNotNull('verification_code')->update([
                'verification_code' => null,
            ]);
        }
    }

    public function down(): void
    {
        // Legacy plaintext verification codes remain intentionally empty.
    }
};
