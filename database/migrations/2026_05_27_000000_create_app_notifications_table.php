<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Workspace nulo significa notificação global de sistema
            $table->foreignId('workspace_id')->nullable()->constrained()->onDelete('cascade');

            $table->string('title');
            $table->text('message');

            // Tipo: info (azul), success (verde), warning (laranja), danger (vermelho)
            $table->string('type')->default('info');

            // Rota para onde o utilizador deve ir ao clicar
            $table->string('link')->nullable();

            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
    }
};
