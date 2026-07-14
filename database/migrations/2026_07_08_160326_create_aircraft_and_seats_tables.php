<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Tabel Pesawat (Airplanes)
        Schema::create('airplanes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('airline_id')->constrained('airlines')->onDelete('cascade');
            $table->string('model');
            $table->string('registration_number');
            $table->integer('capacity');
            $table->text('description')->nullable();
            $table->string('photos')->nullable();
            $table->timestamps();
        });

        // Tabel Kursi (Seats)
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('airplane_id')->constrained('airplanes')->onDelete('cascade');
            $table->string('seat_number');
            $table->enum('class', ['economy', 'business', 'first']);
            $table->enum('status', ['available', 'booked'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('seats');
        Schema::dropIfExists('airplanes');
    }
};