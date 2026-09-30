<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('client_portal_tokens');
    }

    public function down(): void
    {
        // Legacy plaintext-token table intentionally not restored.
    }
};
