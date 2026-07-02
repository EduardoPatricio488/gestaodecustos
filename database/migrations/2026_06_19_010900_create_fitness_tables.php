<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fitness_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // corrida, ciclismo, ginasio, natacao, caminhada, yoga, outro
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->integer('duration_minutes');
            $table->decimal('calories', 8, 2)->nullable();
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->date('activity_date');
            $table->timestamps();
        });

        Schema::create('fitness_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type'); // distancia_semanal, calorias_mensais, sessoes_semanais, tempo_semanal
            $table->decimal('target', 10, 2);
            $table->date('deadline')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fitness_goals');
        Schema::dropIfExists('fitness_activities');
    }
};
