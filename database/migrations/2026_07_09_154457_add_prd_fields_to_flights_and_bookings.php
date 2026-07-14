<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add PRD-mandated fields to flights table
        Schema::table('flights', function (Blueprint $table) {
            $table->enum('status', ['scheduled', 'boarding', 'delayed', 'departed', 'arrived', 'cancelled'])
                  ->default('scheduled')
                  ->after('available_seats');
            $table->string('gate', 10)->nullable()->after('arrival_time');
            $table->string('terminal', 10)->nullable()->after('arrival_time');
            $table->string('flight_duration', 10)->nullable()->after('arrival_time');
            $table->index('flight_number');
            $table->index('departure_time');
            $table->index('status');
        });

        // Add PRD-mandated fields to bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('status', ['pending', 'paid', 'issued', 'cancelled', 'refunded'])
                  ->default('pending')
                  ->after('total_price');
            $table->string('midtrans_transaction_id')->nullable()->after('status');
            $table->string('midtrans_transaction_status')->nullable()->after('midtrans_transaction_id');
            $table->string('payment_type', 50)->nullable()->after('midtrans_transaction_status');
            $table->dateTime('paid_at')->nullable()->after('payment_type');
            $table->index('status');
        });

        // Add PRD-mandated fields to payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'expired', 'refunded'])
                  ->default('pending')
                  ->after('amount');
            $table->string('snap_token', 500)->nullable()->after('transaction_code');
            $table->string('transaction_id')->nullable()->after('snap_token');
            $table->string('fraud_status', 50)->nullable()->after('transaction_id');
            $table->string('midtrans_status_code', 10)->nullable()->after('fraud_status');
            $table->dateTime('settlement_time')->nullable()->after('midtrans_status_code');
            $table->index('payment_status');
        });

        // Add phone and nationality to passengers table
        Schema::table('passengers', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('full_name');
            $table->string('nationality', 100)->nullable()->after('passport_number');
            $table->string('emergency_contact', 100)->nullable()->after('nationality');
            $table->string('emergency_phone', 20)->nullable()->after('emergency_contact');
        });
    }

    public function down(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            $table->dropColumn(['status', 'gate', 'terminal', 'flight_duration']);
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['midtrans_transaction_id', 'midtrans_transaction_status', 'payment_type', 'paid_at']);
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['snap_token', 'transaction_id', 'fraud_status', 'midtrans_status_code', 'settlement_time']);
        });
        Schema::table('passengers', function (Blueprint $table) {
            $table->dropColumn(['phone', 'nationality', 'emergency_contact', 'emergency_phone']);
        });
    }
};