<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Personal Information
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('phone')->nullable()->after('email');
            $table->string('gender')->nullable()->after('phone');
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->string('nationality')->nullable()->after('date_of_birth');
            $table->string('passport_number')->nullable()->after('nationality');
            
            // Emergency Contact
            $table->string('emergency_contact')->nullable()->after('passport_number');
            $table->string('emergency_phone')->nullable()->after('emergency_contact');
            
            // Travel Preferences
            $table->string('preferred_cabin')->nullable()->after('emergency_phone'); // economy, business, first
            $table->string('preferred_seat')->nullable()->after('preferred_cabin'); // window, aisle, middle
            $table->string('meal_preference')->nullable()->after('preferred_seat'); // regular, vegetarian, halal, kosher
            $table->text('special_assistance')->nullable()->after('meal_preference');
            $table->string('preferred_language')->default('en')->after('special_assistance');
            $table->string('timezone')->default('Asia/Jakarta')->after('preferred_language');
            
            // Documents
            $table->string('passport_document')->nullable()->after('timezone');
            $table->string('national_id_document')->nullable()->after('passport_document');
            $table->string('visa_document')->nullable()->after('national_id_document');
            
            // Notifications
            $table->boolean('email_notification')->default(true)->after('visa_document');
            $table->boolean('sms_notification')->default(false)->after('email_notification');
            $table->boolean('flight_reminder')->default(true)->after('sms_notification');
            $table->boolean('promotion')->default(false)->after('flight_reminder');
            $table->boolean('newsletter')->default(false)->after('promotion');
            
            // Membership
            $table->string('membership_level')->default('silver')->after('newsletter'); // silver, gold, platinum
            $table->integer('reward_points')->default(0)->after('membership_level');
            $table->timestamp('member_since')->nullable()->after('reward_points');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'last_name', 'phone', 'gender', 'date_of_birth', 'nationality', 'passport_number',
                'emergency_contact', 'emergency_phone',
                'preferred_cabin', 'preferred_seat', 'meal_preference', 'special_assistance', 'preferred_language', 'timezone',
                'passport_document', 'national_id_document', 'visa_document',
                'email_notification', 'sms_notification', 'flight_reminder', 'promotion', 'newsletter',
                'membership_level', 'reward_points', 'member_since'
            ]);
        });
    }
};