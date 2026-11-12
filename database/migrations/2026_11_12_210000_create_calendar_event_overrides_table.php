<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_event_overrides', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('event_key', 255);
            $table->date('event_date');
            $table->json('event_data');
            $table->timestamps();

            $table->unique(['workspace_id', 'user_id', 'event_key'], 'calendar_event_overrides_event_unique');
            $table->index(['workspace_id', 'user_id', 'event_date'], 'calendar_event_overrides_date_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_event_overrides');
    }
};
