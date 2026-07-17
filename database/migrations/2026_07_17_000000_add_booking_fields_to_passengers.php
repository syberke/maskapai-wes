<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('passengers', 'email')) {
            Schema::table('passengers', function (Blueprint $table) {
                $table->string('email')->nullable();
            });
        }

        if (! Schema::hasColumn('passengers', 'seat_id')) {
            Schema::table('passengers', function (Blueprint $table) {
                $table->unsignedBigInteger('seat_id')->nullable()->index();
            });
        }

        if (! Schema::hasColumn('passengers', 'date_of_birth')) {
            Schema::table('passengers', function (Blueprint $table) {
                $table->date('date_of_birth')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Legacy passenger columns are intentionally preserved.
    }
};
