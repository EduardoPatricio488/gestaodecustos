<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {

            if (!Schema::hasColumn('chat_messages', 'chat_conversation_id')) {
                $table->foreignId('chat_conversation_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained()
                    ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {

            if (Schema::hasColumn('chat_messages', 'chat_conversation_id')) {
                $table->dropForeign(['chat_conversation_id']);
                $table->dropColumn('chat_conversation_id');
            }
        });
    }
};
