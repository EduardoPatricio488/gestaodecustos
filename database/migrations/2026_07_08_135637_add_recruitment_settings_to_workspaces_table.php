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
    Schema::table('workspaces', function (Blueprint $table) {
        $table->boolean('recruitment_active')->default(true); // Abrir/Fechar vagas
        $table->text('recruitment_description')->nullable(); // Texto do card
        $table->string('recruitment_announcement')->nullable(); // Ex: "Procuramos Developers"
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            //
        });
    }
};
