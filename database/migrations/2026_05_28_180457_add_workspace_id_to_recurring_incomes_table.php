<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('recurring_incomes', function (Blueprint $table) {
            if (!Schema::hasColumn('recurring_incomes', 'workspace_id')) {
                $table->foreignId('workspace_id')->nullable()->constrained()->onDelete('cascade');
            }
        });
    }
    public function down(): void {
        Schema::table('recurring_incomes', fn (Blueprint $table) => $table->dropColumn('workspace_id'));
    }
};
