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
    Schema::create('proposals', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
        $table->foreignId('client_id')->constrained()->onDelete('cascade');

        $table->string('title'); // Ex: Website e Branding
        $table->string('proposal_number'); // Ex: PROP-2026-001
        $table->decimal('amount', 15, 2);
        $table->string('status')->default('rascunho'); // draft, sent, accepted, declined, converted
        $table->date('valid_until')->nullable();
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
