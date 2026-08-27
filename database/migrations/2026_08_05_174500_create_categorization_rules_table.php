<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorization_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('keyword');
            $table->unsignedInteger('priority')->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['workspace_id', 'is_active', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorization_rules');
    }
};
