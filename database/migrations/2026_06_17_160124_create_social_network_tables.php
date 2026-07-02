<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. TABELA DE POSTS (Texto, Imagem, Vídeo, Financeiro)
        Schema::create('social_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('workspace_id')->nullable()->constrained()->onDelete('set null');

            $table->string('type')->default('text'); // text, image, video, story, financial
            $table->text('content')->nullable();
            $table->string('media_path')->nullable(); // Caminho para foto ou vídeo

            // Lógica Financeira (Link para o que está a ser partilhado)
            $table->string('financial_type')->nullable(); // Expense, Goal, Investment, etc.
            $table->unsignedBigInteger('financial_id')->nullable();

            // Visibilidade: public, followers, friends, group, workspace, private
            $table->string('visibility')->default('public');

            // Stories (Expiram em 24h)
            $table->boolean('is_story')->default(false);
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();
        });

        // 2. TABELA DE SEGUIDORES / AMIGOS
        Schema::create('social_follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follower_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('following_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_friend')->default(false); // Se ambos se seguirem
            $table->timestamps();
        });

        // 3. TABELA DE LIKES
        Schema::create('social_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('post_id')->constrained('social_posts')->onDelete('cascade');
            $table->timestamps();
        });

        // 4. TABELA DE COMENTÁRIOS
        Schema::create('social_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('post_id')->constrained('social_posts')->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('social_comments');
        Schema::dropIfExists('social_likes');
        Schema::dropIfExists('social_follows');
        Schema::dropIfExists('social_posts');
    }
};
