<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Airport;
use App\Models\Airline;
use App\Models\Airplane;
use App\Models\Seat;
use App\Models\Flight;
use App\Models\Cms\Banner;
use App\Models\Cms\Destination;
use App\Models\Cms\Testimonial;
use App\Models\Cms\Faq;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Multi-role
        User::create(['name' => 'Super Admin', 'email' => 'admin@luxuryfly.com', 'password' => Hash::make('password123'), 'role' => 'admin', 'email_verified_at' => now()]);
        User::create(['name' => 'Manager Analytics', 'email' => 'manager@luxuryfly.com', 'password' => Hash::make('password123'), 'role' => 'manager', 'email_verified_at' => now()]);
        User::create(['name' => 'Staff Operational', 'email' => 'staff@luxuryfly.com', 'password' => Hash::make('password123'), 'role' => 'staff', 'email_verified_at' => now()]);
        User::create(['name' => 'John Doe', 'email' => 'customer@gmail.com', 'password' => Hash::make('password123'), 'role' => 'customer', 'email_verified_at' => now()]);

        // 2. Airports
        Airport::create(['city' => 'Jakarta', 'country' => 'Indonesia', 'iata_code' => 'CGK', 'name' => 'Soekarno-Hatta International Airport']);
        Airport::create(['city' => 'Singapore', 'country' => 'Singapore', 'iata_code' => 'SIN', 'name' => 'Changi Airport']);
        Airport::create(['city' => 'Doha', 'country' => 'Qatar', 'iata_code' => 'DOH', 'name' => 'Hamad International Airport']);
        Airport::create(['city' => 'Bali', 'country' => 'Indonesia', 'iata_code' => 'DPS', 'name' => 'I Gusti Ngurah Rai International Airport']);
        Airport::create(['city' => 'Surabaya', 'country' => 'Indonesia', 'iata_code' => 'SUB', 'name' => 'Juanda International Airport']);
        Airport::create(['city' => 'Yogyakarta', 'country' => 'Indonesia', 'iata_code' => 'YIA', 'name' => 'Yogyakarta International Airport']);
        Airport::create(['city' => 'Kuala Lumpur', 'country' => 'Malaysia', 'iata_code' => 'KUL', 'name' => 'Kuala Lumpur International Airport']);
        Airport::create(['city' => 'Dubai', 'country' => 'UAE', 'iata_code' => 'DXB', 'name' => 'Dubai International Airport']);

        // 3. Airlines
        $sq = Airline::create(['name' => 'Singapore Airlines', 'code' => 'SQ', 'registration_number' => 'SQ-9V-A1', 'description' => 'Maskapai terbaik dunia.']);
        $qr = Airline::create(['name' => 'Qatar Airways', 'code' => 'QR', 'registration_number' => 'QR-A7-B2', 'description' => 'Layanan premium Qatar.']);
        $ek = Airline::create(['name' => 'Emirates', 'code' => 'EK', 'registration_number' => 'EK-A6-C3', 'description' => 'Pengalaman terbang mewah.']);
        $ga = Airline::create(['name' => 'Garuda Indonesia', 'code' => 'GA', 'registration_number' => 'GA-PK-01', 'description' => 'Maskapai kebanggaan Indonesia.']);

        // 4. Airplanes
        $a1 = Airplane::create(['airline_id' => $sq->id, 'model' => 'Airbus A380-800', 'registration_number' => '9V-SKA', 'capacity' => 360]);
        $a2 = Airplane::create(['airline_id' => $sq->id, 'model' => 'Boeing 777-300ER', 'registration_number' => '9V-SWB', 'capacity' => 280]);
        $a3 = Airplane::create(['airline_id' => $qr->id, 'model' => 'Airbus A350-1000', 'registration_number' => 'A7-ANA', 'capacity' => 320]);
        $a4 = Airplane::create(['airline_id' => $ek->id, 'model' => 'Boeing 777-300ER', 'registration_number' => 'A6-EGA', 'capacity' => 300]);
        $a5 = Airplane::create(['airline_id' => $ga->id, 'model' => 'Airbus A330-900neo', 'registration_number' => 'PK-GHA', 'capacity' => 280]);

        // 5. Seats
        foreach ([$a1, $a2, $a3, $a4, $a5] as $ap) {
            for ($row = 1; $row <= 10; $row++) {
                foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $col) {
                    $class = $row <= 2 ? 'first' : ($row <= 4 ? 'business' : 'economy');
                    Seat::create(['airplane_id' => $ap->id, 'seat_number' => $row . $col, 'class' => $class, 'status' => 'available']);
                }
            }
        }

        // 6. Flights
        $flights = [
            ['airline_id' => $sq->id, 'airplane_id' => $a1->id, 'flight_number' => 'SQ952', 'departure_airport_id' => 1, 'arrival_airport_id' => 2, 'departure_time' => now()->addDays(1)->setHour(8)->setMinute(0), 'arrival_time' => now()->addDays(1)->setHour(11)->setMinute(30), 'price' => 2500000, 'available_seats' => 60],
            ['airline_id' => $sq->id, 'airplane_id' => $a2->id, 'flight_number' => 'SQ953', 'departure_airport_id' => 2, 'arrival_airport_id' => 1, 'departure_time' => now()->addDays(1)->setHour(14)->setMinute(0), 'arrival_time' => now()->addDays(1)->setHour(17)->setMinute(30), 'price' => 2300000, 'available_seats' => 50],
            ['airline_id' => $qr->id, 'airplane_id' => $a3->id, 'flight_number' => 'QR956', 'departure_airport_id' => 3, 'arrival_airport_id' => 1, 'departure_time' => now()->addDays(2)->setHour(9)->setMinute(0), 'arrival_time' => now()->addDays(2)->setHour(16)->setMinute(0), 'price' => 4500000, 'available_seats' => 40],
            ['airline_id' => $ek->id, 'airplane_id' => $a4->id, 'flight_number' => 'EK358', 'departure_airport_id' => 8, 'arrival_airport_id' => 1, 'departure_time' => now()->addDays(2)->setHour(22)->setMinute(0), 'arrival_time' => now()->addDays(3)->setHour(6)->setMinute(0), 'price' => 5500000, 'available_seats' => 35],
            ['airline_id' => $ga->id, 'airplane_id' => $a5->id, 'flight_number' => 'GA414', 'departure_airport_id' => 1, 'arrival_airport_id' => 4, 'departure_time' => now()->addDays(1)->setHour(7)->setMinute(0), 'arrival_time' => now()->addDays(1)->setHour(10)->setMinute(0), 'price' => 1500000, 'available_seats' => 45],
            ['airline_id' => $ga->id, 'airplane_id' => $a5->id, 'flight_number' => 'GA460', 'departure_airport_id' => 1, 'arrival_airport_id' => 5, 'departure_time' => now()->addDays(1)->setHour(12)->setMinute(0), 'arrival_time' => now()->addDays(1)->setHour(13)->setMinute(30), 'price' => 800000, 'available_seats' => 50],
            ['airline_id' => $sq->id, 'airplane_id' => $a1->id, 'flight_number' => 'SQ958', 'departure_airport_id' => 2, 'arrival_airport_id' => 4, 'departure_time' => now()->addDays(3)->setHour(10)->setMinute(0), 'arrival_time' => now()->addDays(3)->setHour(13)->setMinute(0), 'price' => 2800000, 'available_seats' => 55],
            ['airline_id' => $qr->id, 'airplane_id' => $a3->id, 'flight_number' => 'QR960', 'departure_airport_id' => 1, 'arrival_airport_id' => 3, 'departure_time' => now()->addDays(3)->setHour(20)->setMinute(0), 'arrival_time' => now()->addDays(4)->setHour(3)->setMinute(0), 'price' => 4200000, 'available_seats' => 38],
        ];
        foreach ($flights as $f) {
            Flight::create($f);
        }

        // 7. CMS - Banners
        Banner::create(['title' => 'Jelajahi Dunia Bersama Kami', 'subtitle' => 'Terbang dengan kenyamanan kelas utama bersama LuxuryFly', 'image_url' => 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?q=80&w=1920', 'is_active' => true]);
        Banner::create(['title' => 'Premium Travel Experience', 'subtitle' => 'Nikmati layanan eksklusif dengan harga terbaik', 'image_url' => 'https://images.unsplash.com/photo-1464037866556-6812c9d1c72e?q=80&w=1920', 'is_active' => true]);

        // 8. CMS - Destinations
        Destination::create(['city_name' => 'Bali, Indonesia', 'image_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=600', 'description' => 'Surga tropis dengan budaya eksotis.', 'is_featured' => true]);
        Destination::create(['city_name' => 'Singapore', 'image_url' => 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?q=80&w=600', 'description' => 'Pusat bisnis kosmopolitan.', 'is_featured' => true]);
        Destination::create(['city_name' => 'Dubai, UAE', 'image_url' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?q=80&w=600', 'description' => 'Kemegahan timur tengah.', 'is_featured' => true]);
        Destination::create(['city_name' => 'Doha, Qatar', 'image_url' => 'https://images.unsplash.com/photo-1570498839593-e565b39455fc?q=80&w=600', 'description' => 'Kota modern di teluk Arab.', 'is_featured' => true]);
        Destination::create(['city_name' => 'Yogyakarta', 'image_url' => 'https://images.unsplash.com/photo-1589198947426-3e7c76e4c3e4?q=80&w=600', 'description' => 'Kota budaya dan sejarah.', 'is_featured' => true]);
        Destination::create(['city_name' => 'Kuala Lumpur', 'image_url' => 'https://images.unsplash.com/photo-1596495577886-d920f1f6695e?q=80&w=600', 'description' => 'Ibu kota Malaysia yang multikultur.', 'is_featured' => true]);

        // 9. CMS - Testimonials
        Testimonial::create(['name' => 'Sarah Johnson', 'avatar_url' => null, 'review' => 'Pengalaman terbang yang luar biasa! Pelayanan crew sangat profesional dan ramah.', 'rating' => 5]);
        Testimonial::create(['name' => 'Michael Chen', 'avatar_url' => null, 'review' => 'Kursi yang nyaman, makanan lezat, dan ketepatan waktu yang sempurna.', 'rating' => 5]);
        Testimonial::create(['name' => 'Anita Wijaya', 'avatar_url' => null, 'review' => 'Sangat puas dengan layanan LuxuryFly. Akan booking lagi untuk liburan berikutnya!', 'rating' => 4]);
        Testimonial::create(['name' => 'David Kim', 'avatar_url' => null, 'review' => 'First class-nya amazing! Fasilitas lengkap dan private suite yang mewah.', 'rating' => 5]);

        // 10. CMS - FAQs
        Faq::create(['question' => 'Bagaimana cara booking tiket?', 'answer' => 'Anda dapat mencari penerbangan melalui form pencarian di halaman utama, pilih kursi, isi data penumpang, lalu lakukan pembayaran.']);
        Faq::create(['question' => 'Metode pembayaran apa saja yang tersedia?', 'answer' => 'Kami menerima pembayaran melalui Transfer Bank (BCA, Mandiri, BNI, BRI), Kartu Kredit (Visa, Mastercard), dan E-Wallet (GoPay, OVO, Dana).']);
        Faq::create(['question' => 'Bagaimana cara mendapatkan E-Ticket?', 'answer' => 'Setelah pembayaran berhasil, E-Ticket akan tersedia dan dapat diunduh melalui halaman riwayat booking Anda.']);
        Faq::create(['question' => 'Apa kebijakan pembatalan tiket?', 'answer' => 'Pembatalan dapat dilakukan dengan menghubungi customer service kami. Biaya pembatalan berlaku sesuai ketentuan yang berlaku.']);
        Faq::create(['question' => 'Berapa jumlah bagasi yang diizinkan?', 'answer' => 'Setiap penumpang berhak membawa 1 kabin bagasi 7kg dan 2 bagasi tercatat masing-masing 23kg untuk kelas ekonomi.']);
    }
}