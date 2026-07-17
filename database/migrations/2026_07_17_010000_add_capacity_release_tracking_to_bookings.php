<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('bookings', 'capacity_released_at')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->timestamp('capacity_released_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Capacity release history is retained to prevent duplicate seat restoration.
    }
};
