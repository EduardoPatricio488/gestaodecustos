<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reminders', function (Blueprint $col) {
            $col->id();
            $col->foreignId('user_id')->constrained()->onDelete('cascade');
            $col->foreignId('workspace_id')->constrained()->onDelete('cascade');

            $col->string('title');
            $col->datetime('remind_at');
            $col->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $col->enum('frequency', ['once', 'daily', 'weekly', 'monthly'])->default('once');

            $col->boolean('is_completed')->default(false);
            $col->timestamp('completed_at')->nullable();
            $col->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('reminders');
    }
};
