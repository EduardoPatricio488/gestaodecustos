<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // Ligação ao utilizador
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Ligação ao workspace
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');

            // Dados principais
            $table->string('name');
            $table->string('role');
            $table->decimal('salary', 10, 2);
            $table->integer('pay_day')->default(25);

            // Estado do colaborador
            $table->string('status')->default('ativo');

            // Foto do colaborador
            $table->string('photo_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
