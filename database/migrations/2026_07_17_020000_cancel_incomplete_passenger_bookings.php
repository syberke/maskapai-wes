<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('bookings') || ! Schema::hasTable('passengers')) {
            return;
        }

        $updates = [
            'status' => 'cancelled',
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('bookings', 'capacity_released_at')) {
            $updates['capacity_released_at'] = now();
        }

        DB::table('bookings')
            ->where('status', 'pending')
            ->whereRaw('(SELECT COUNT(*) FROM passengers WHERE passengers.booking_id = bookings.id) < bookings.total_passengers')
            ->update($updates);
    }

    public function down(): void
    {
        // Invalid legacy bookings cannot be reconstructed automatically.
    }
};
