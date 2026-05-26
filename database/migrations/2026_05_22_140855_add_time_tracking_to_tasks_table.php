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
    Schema::table('tasks', function (Blueprint $table) {
        // Controla se o cronómetro está ativo agora
        $table->boolean('is_timer_running')->default(false)->after('status');

        // Regista quando o cronómetro foi iniciado
        $table->timestamp('timer_started_at')->nullable()->after('is_timer_running');

        // Acumula o tempo total gasto (em segundos)
        $table->bigInteger('total_seconds')->default(0)->after('timer_started_at');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            //
        });
    }
};
