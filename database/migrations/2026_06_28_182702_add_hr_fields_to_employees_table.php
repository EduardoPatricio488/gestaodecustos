<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
    $table->boolean('active')->default(true);
    $table->boolean('suspended')->default(false);
    $table->timestamp('terminated_at')->nullable();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('employees', function (Blueprint $table) {
    $table->dropColumn(['active', 'suspended', 'terminated_at']);
});
    }
};
