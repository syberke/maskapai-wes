<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Tabel Users Utama dengan Multi-Role
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'manager', 'staff', 'customer'])->default('customer');
            $table->rememberToken();
            $table->timestamps();
        });

        // Tabel Bandara (Airports)
        Schema::create('airports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('city');
            $table->string('country');
            $table->string('iata_code', 5)->unique();
            $table->timestamps();
        });

        // Tabel Maskapai (Airlines)
        Schema::create('airlines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique();
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('photos')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('airlines');
        Schema::dropIfExists('airports');
        Schema::dropIfExists('users');
    }
};