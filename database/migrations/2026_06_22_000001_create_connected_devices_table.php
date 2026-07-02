<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('connected_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');                    // "Xiaomi Smart Band 10"
            $table->string('brand')->nullable();       // "Xiaomi"
            $table->string('emoji')->default('⌚');
            $table->string('provider')->default('manual'); // manual | strava | garmin | fitbit | healthconnect
            $table->string('provider_user_id')->nullable();
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();          // dados extras (bateria, firmware, etc.)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('connected_devices');
    }
};
