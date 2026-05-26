<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. Tabela de Tickets (O assunto do problema)
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('subject');
            $table->string('status')->default('open'); // open, pending, closed
            $table->string('priority')->default('normal'); // low, normal, high
            $table->timestamps();
        });

        // 2. Tabela de Mensagens (A conversa dentro do ticket)
        Schema::create('support_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Quem enviou
            $table->text('message');
            $table->boolean('is_admin_reply')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('support_messages');
        Schema::dropIfExists('support_tickets');
    }
};
