<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = collect(Schema::getColumns('bank_transfers'))->keyBy('name');

        foreach (['from_account_id', 'to_account_id'] as $column) {
            if (! $columns->has($column) || $columns[$column]['nullable']) {
                continue;
            }

            Schema::table('bank_transfers', function (Blueprint $table) use ($column): void {
                $table->foreignId($column)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // Keep both account references nullable: incoming and outgoing
        // transfers legitimately leave one side empty.
    }
};
