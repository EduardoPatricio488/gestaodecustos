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
    Schema::table('fitness_activities', function (Blueprint $table) {
        $table->string('pace')->nullable();
        $table->string('hr_avg')->nullable();
        $table->string('hr_max')->nullable();
        $table->string('steps')->nullable();
        $table->string('cadence')->nullable();
        $table->string('stride')->nullable();
        $table->decimal('te_aerobic', 3, 1)->nullable();
        $table->decimal('te_anaerobic', 3, 1)->nullable();
        $table->integer('recovery_time')->nullable();
        $table->integer('training_load')->nullable();
        $table->string('zone_vo2')->nullable();
        $table->string('zone_anaerobic')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fitness_activities', function (Blueprint $table) {
            //
        });
    }
};
