<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('airplanes', function (Blueprint $table) {
            $table->integer('first_class_seats')->default(4)->after('capacity');
            $table->integer('business_class_seats')->default(12)->after('first_class_seats');
            $table->integer('economy_class_seats')->default(168)->after('business_class_seats');
            $table->integer('total_seats')->default(184)->after('economy_class_seats');
        });

        // Update existing airplanes with default values
        \DB::table('airplanes')->update([
            'first_class_seats' => 4,
            'business_class_seats' => 12,
            'economy_class_seats' => 168,
            'total_seats' => 184,
        ]);
    }

    public function down(): void
    {
        Schema::table('airplanes', function (Blueprint $table) {
            $table->dropColumn(['first_class_seats', 'business_class_seats', 'economy_class_seats', 'total_seats']);
        });
    }
};