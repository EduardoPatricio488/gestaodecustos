<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        // The application historically used MySQL DATE_FORMAT() in a few reporting
        // queries. Render production uses PostgreSQL, so provide the narrow
        // compatibility function needed by those existing queries while the
        // reporting code is progressively made driver-aware.
        DB::unprepared(<<<'SQL'
CREATE OR REPLACE FUNCTION date_format(value timestamp, format text)
RETURNS text
LANGUAGE plpgsql
IMMUTABLE
AS $$
BEGIN
    IF format = '%Y-%m' THEN
        RETURN to_char(value, 'YYYY-MM');
    END IF;

    RETURN format;
END;
$$;

CREATE OR REPLACE FUNCTION date_format(value date, format text)
RETURNS text
LANGUAGE plpgsql
IMMUTABLE
AS $$
BEGIN
    IF format = '%Y-%m' THEN
        RETURN to_char(value, 'YYYY-MM');
    END IF;

    RETURN format;
END;
$$;
SQL);
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::unprepared(<<<'SQL'
DROP FUNCTION IF EXISTS date_format(timestamp, text);
DROP FUNCTION IF EXISTS date_format(date, text);
SQL);
    }
};
