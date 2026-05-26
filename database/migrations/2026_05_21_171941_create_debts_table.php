<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // 'owe' (Eu devo) ou 'owed' (Devem-me)
            $table->string('person_name'); // Nome do credor ou devedor
            $table->decimal('amount', 12, 2);
            $table->text('description')->nullable();
            $table->date('due_at')->nullable(); // Data limite para pagar/receber
            $table->boolean('is_paid')->default(false); // Se já foi liquidada
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('debts');
    }
};
