<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

final class SqlDateExpression
{
    public static function month(string $column = 'created_at'): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "CAST(strftime('%m', {$column}) AS INTEGER)",
            'pgsql' => "EXTRACT(MONTH FROM {$column})",
            default => "MONTH({$column})",
        };
    }
}
