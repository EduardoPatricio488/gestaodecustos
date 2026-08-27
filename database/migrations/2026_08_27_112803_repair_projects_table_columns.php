<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'budget')) {
                $table->decimal('budget', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('projects', 'revenue')) {
                $table->decimal('revenue', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('projects', 'costs')) {
                $table->decimal('costs', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('projects', 'margin')) {
                $table->decimal('margin', 15, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['budget', 'revenue', 'costs', 'margin']);
        });
    }
};
