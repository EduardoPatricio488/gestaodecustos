<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('support_tickets', function (Blueprint $table) {
        if (!Schema::hasColumn('support_tickets', 'workspace_id')) {
            $table->unsignedBigInteger('workspace_id')->nullable()->after('user_id');
        }
    });
}

public function down()
{
    Schema::table('support_tickets', function (Blueprint $table) {
        $table->dropColumn('workspace_id');
    });
}}
    ;
