<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('expenses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $t->decimal('amount', 12, 2);
            $t->string('description')->nullable();
            $t->date('spent_at');
            $t->timestamps();
            $t->index(['user_id', 'spent_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('expenses'); }
};
