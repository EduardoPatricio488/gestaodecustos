<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_access_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('portal_type', 30);
            $table->string('requester_name', 150);
            $table->string('requester_email');
            $table->string('tax_number', 30)->nullable();
            $table->string('status', 30)->default('pending');
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['workspace_id', 'portal_type', 'status']);
            $table->index(['requester_email', 'portal_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_access_requests');
    }
};
