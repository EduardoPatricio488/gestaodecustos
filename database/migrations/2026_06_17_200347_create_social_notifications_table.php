<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('social_notifications', function (Blueprint $table) {
            $table->id();

            // Quem recebe a notificação
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Quem causou a notificação (quem deu like, comentou, seguiu...)
            $table->foreignId('actor_id')->constrained('users')->onDelete('cascade');

            // like, comment, follow
            $table->string('type');

            // Post relacionado (nullable porque 'follow' não tem post)
            $table->foreignId('post_id')->nullable()->constrained('social_posts')->onDelete('cascade');

            // Pequeno preview de texto (ex: início do comentário)
            $table->string('preview')->nullable();

            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_notifications');
    }
};
