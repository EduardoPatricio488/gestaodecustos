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
    if (Schema::hasColumn('support_tickets', 'message')) {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropColumn('message');
        });
    }
}

public function down()
{
    Schema::table('support_tickets', function (Blueprint $table) {
        $table->text('message')->nullable();
    });
}

};
