<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Support\\Facades\\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('investments')
            ->whereRaw('UPPER(symbol) = ?', ['VUAA.DE'])
            ->update(['type' => 'ETF']);
    }

    public function down(): void
    {
        DB::table('investments')
            ->whereRaw('UPPER(symbol) = ?', ['VUAA.DE'])
            ->where('type', 'ETF')
            ->update(['type' => 'Acao']);
    }
};
