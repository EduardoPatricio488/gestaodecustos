<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Só cria a tabela se NÃO existir
        if (!Schema::hasTable('chat_messages')) {
            Schema::create('chat_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('role'); // 'user' ou 'assistant'
                $table->text('content');
                $table->boolean('is_error')->default(false);
                $table->integer('tokens')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('chat_messages')) {
            Schema::dropIfExists('chat_messages');
        }
    }
};
