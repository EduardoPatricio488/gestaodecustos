<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void {
    Schema::table('activity_logs', function (Blueprint $table) {
        // Renomeia ou adiciona os campos que o teu novo código pede
        if (!Schema::hasColumn('activity_logs', 'description')) {
            $table->text('description')->nullable()->after('action');
        }
        if (!Schema::hasColumn('activity_logs', 'model_type')) {
            $table->string('model_type')->nullable()->after('description');
            $table->unsignedBigInteger('model_id')->nullable()->after('model_type');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
