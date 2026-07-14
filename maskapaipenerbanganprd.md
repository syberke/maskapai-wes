# SISTEM INFORMASI MASKAPAI PENERBANGAN ONLINE (LARAVEL 11)

Buat aplikasi web Sistem Informasi Maskapai Penerbangan Online menggunakan Laravel 11, MySQL, Tailwind CSS, dan Blade Template.

Aplikasi harus memiliki tampilan modern, premium, profesional, responsif, dan berbeda dari template pasaran agar tidak menyerupai hasil peserta lain.

## TECHNOLOGY STACK

Backend:

* Laravel 11
* PHP 8.3
* Eloquent ORM
* Laravel Breeze Authentication
* Laravel Notifications
* Laravel Queues

Frontend:

* Blade Template
* Tailwind CSS
* Alpine.js
* Chart.js
* AOS Animation

Database:

* MySQL

Integrasi:

* Midtrans Sandbox
* Google reCAPTCHA
* SMTP Mailtrap/Gmail
* PWA (Progressive Web App)

---

# KETENTUAN WAJIB

1. Layout harus unik dan tidak menyerupai template umum.
2. Semua fitur wajib dinamis dan terhubung ke database.
3. Landing page harus memiliki CMS sehingga seluruh konten dapat diubah admin.
4. Sistem dapat digunakan secara online dan offline (minimal E-Ticket dan Booking History).
5. Pembayaran menggunakan Midtrans Sandbox.
6. Login wajib menggunakan:

   * Email Verification
   * Google reCAPTCHA
7. Dashboard manajemen harus memiliki laporan detail dan analitik.

---

# DATABASE

Gunakan PDM berikut sebagai dasar utama.

## users

* id
* name
* email
* password
* role
* email_verified_at
* created_at
* updated_at

Role:

* admin
* manager
* staff
* customer

---

## airports

* id
* city
* country
* iata_code
* name
* created_at
* updated_at

---

## airlines

* id
* name
* code
* logo
* registration_number
* photos
* created_at
* updated_at

---

## airplanes

* id
* airline_id
* model
* capacity
* created_at
* updated_at

---

## seats

* id
* airplane_id
* seat_number
* class
* status
* created_at
* updated_at

Class:

* economy
* business
* first

Status:

* available
* booked

---

## flights

* id
* airline_id
* airplane_id
* departure_airport_id
* arrival_airport_id
* departure_time
* arrival_time
* price
* available_seats
* created_at
* updated_at

---

## bookings

* id
* user_id
* flight_id
* booking_code
* status
* total_price
* created_at
* updated_at

Status:

* pending
* paid
* confirmed
* cancelled

---

## passengers

* id
* booking_id
* name
* gender
* birth_date
* passport_number
* seat_number
* created_at
* updated_at

---

## payments

* id
* booking_id
* payment_method
* payment_status
* transaction_code
* paid_at
* created_at
* updated_at

---

# RELASI ELOQUENT

User:

* hasMany(Bookings)

Booking:

* belongsTo(User)
* belongsTo(Flight)
* hasMany(Passengers)
* hasOne(Payment)

Flight:

* belongsTo(Airline)
* belongsTo(Airplane)
* belongsTo(Airport, departure_airport_id)
* belongsTo(Airport, arrival_airport_id)
* hasMany(Bookings)

Airline:

* hasMany(Airplanes)
* hasMany(Flights)

Airplane:

* belongsTo(Airline)
* hasMany(Seats)

Seat:

* belongsTo(Airplane)

Passenger:

* belongsTo(Booking)

Payment:

* belongsTo(Booking)

---

# TAMBAHAN TABEL CMS

Tambahkan tabel:

## banners

## testimonials

## destinations

## homepage_sections

## settings

## faqs

Semua data landing page harus berasal dari database.

---

# LANDING PAGE

Desain premium terinspirasi dari:

* Singapore Airlines
* Qatar Airways
* Emirates

Gunakan warna:

* Putih
* Hitam
* Gold

Buat desain eksklusif dan profesional.

Section:

### Hero

Flight Search Form

Field:

* Origin
* Destination
* Date
* Passenger
* Class

### Featured Airlines

### Popular Destinations

### Testimonials

### Why Choose Us

### FAQ

### Footer

Semua data berasal dari database.

---

# AUTHENTICATION

Gunakan Laravel Breeze.

Implementasikan:

## Register

* Nama
* Email
* Password
* Confirm Password
* reCAPTCHA

## Login

* Email
* Password
* reCAPTCHA

## Email Verification

## Forgot Password

## Reset Password

## Logout

---

# ROLE & PERMISSIONS

Admin:

* Full Access

Staff:

* Melihat jadwal penerbangan
* Melihat booking
* Melihat manifest penumpang

Manager:

* Dashboard analytics
* Report
* Export PDF
* Export Excel

Customer:

* Booking tiket
* Pembayaran
* E-Ticket

Gunakan Middleware Role.

---

# CUSTOMER FEATURES

## Flight Search

Cari berdasarkan:

* Kota Asal
* Kota Tujuan
* Tanggal
* Kelas

Filter:

* Maskapai
* Harga
* Waktu Keberangkatan

---

## Flight Detail

Menampilkan:

* Airline
* Aircraft
* Departure
* Arrival
* Duration
* Available Seats
* Price

---

## Seat Selection

Visual seat map.

Status:

* Available
* Booked
* Selected

Gunakan warna berbeda.

---

## Booking System

Flow:

Cari Flight
↓
Pilih Flight
↓
Isi Data Penumpang
↓
Pilih Kursi
↓
Checkout
↓
Midtrans
↓
E-Ticket

---

## Payment

Integrasi Midtrans Snap.

Implementasikan:

* Snap Token
* Callback
* Webhook Verification

Jika pembayaran sukses:

payment_status = paid

booking_status = confirmed

---

## E-Ticket

Generate:

* PDF
* QR Code
* Booking Code

Isi:

* Nama Penumpang
* Nomor Kursi
* Flight
* Airline
* Departure
* Arrival

---

## Booking History

Customer dapat melihat seluruh transaksi.

---

# STAFF FEATURES

## Passenger Manifest

Melihat seluruh penumpang per penerbangan.

## Flight Monitoring

Melihat jadwal dan status penerbangan.

---

# ADMIN FEATURES

Dashboard Admin.

CRUD:

* Airports
* Airlines
* Airplanes
* Seats
* Flights
* Users
* Testimonials
* Destinations
* Homepage Content

---

# MANAGER FEATURES

Dashboard Analytics.

Menampilkan:

## Revenue Analytics

* Harian
* Mingguan
* Bulanan

## Booking Analytics

* Total Booking
* Success Rate
* Cancellation Rate

## Occupancy Rate

Per:

* Airline
* Flight
* Route

## Top Airlines

Ranking maskapai berdasarkan:

* Revenue
* Occupancy
* Total Flights

## Top Routes

Contoh:

* CGK → DPS
* CGK → SUB
* DPS → YIA

---

# REPORT

Export:

* PDF
* Excel

Report:

1. Revenue Report
2. Booking Report
3. Passenger Report
4. Airline Performance Report
5. Route Performance Report
6. Occupancy Report

---

# OFFLINE MODE

Implementasikan PWA.

Gunakan:

* Service Worker
* Cache API
* IndexedDB

Customer tetap dapat:

* Membuka E-Ticket
* Melihat Booking History

saat offline.

---

# SECURITY

Implementasikan:

* Email Verification
* Google reCAPTCHA
* Laravel CSRF Protection
* Laravel Sanctum
* Rate Limiting
* Password Hashing Bcrypt
* Form Validation
* SQL Injection Protection
* XSS Protection
* Role Based Access Control

---

# OUTPUT YANG DIINGINKAN

Berikan secara bertahap:

1. Struktur Folder Laravel
2. Migration Lengkap
3. Model & Relasi Eloquent
4. Seeder Sample Data
5. Middleware Role
6. Authentication System
7. CRUD Semua Master Data
8. Landing Page Dinamis
9. Flight Search Engine
10. Seat Selection System
11. Booking Module
12. Midtrans Integration
13. E-Ticket PDF
14. QR Code Generator
15. PWA Offline Mode
16. Dashboard Analytics
17. Report PDF & Excel
18. Blade Views
19. Responsive Design
20. Best Practice Laravel 11 Production Ready

Buat kode yang rapi, mengikuti high Laravel, menggunakan Service Layer jika diperlukan, serta siap digunakan sebagai proyek UKK/Tugas Akhir.