<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('color', 9)->default('#3b82f6');
            $t->string('icon')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('categories'); }
};
