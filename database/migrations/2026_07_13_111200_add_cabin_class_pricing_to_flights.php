<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            $table->decimal('first_class_price', 12, 2)->nullable()->after('price');
            $table->decimal('business_class_price', 12, 2)->nullable()->after('first_class_price');
            $table->decimal('economy_class_price', 12, 2)->nullable()->after('business_class_price');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('cabin_class', 20)->default('economy')->after('total_passengers');
        });
    }

    public function down(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            $table->dropColumn(['first_class_price', 'business_class_price', 'economy_class_price']);
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('cabin_class');
        });
    }
};