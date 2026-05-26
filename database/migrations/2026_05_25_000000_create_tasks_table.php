<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Colaborador atribuído

            $table->string('title');
            $table->text('description')->nullable();

            // Gestão de Estado e Prioridade
            $table->string('priority')->default('media'); // baixa, media, alta, critica
            $table->string('status')->default('pendente'); // pendente, em_curso, revisao, concluida

            // Prazos e Performance
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('estimated_hours')->nullable(); // Para cálculo de custo/benefício

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
