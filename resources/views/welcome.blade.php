<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LuxuryFly - Premium Online Flight Information System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        .font-luxury { font-family: 'Cinzel', serif; }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-zinc-950 text-slate-100 overflow-x-hidden">

    <nav class="absolute top-0 left-0 w-full z-50 bg-gradient-to-b from-black/80 to-transparent backdrop-blur-sm border-b border-amber-500/10">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="#" class="font-luxury text-2xl font-bold tracking-widest text-amber-500">
                LUXURY<span class="text-white font-light">FLY</span>
            </a>
            <div class="flex items-center space-x-6 text-sm tracking-wider uppercase">
                <a href="#destinations" class="hover:text-amber-500 transition">Destinations</a>
                <a href="#testimonials" class="hover:text-amber-500 transition">Experience</a>
                <a href="#faq" class="hover:text-amber-500 transition">FAQ</a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="border border-amber-500 px-4 py-2 rounded text-amber-500 hover:bg-amber-500 hover:text-black transition duration-300">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-amber-500 transition">Log In</a>
                        <a href="{{ route('register') }}" class="bg-amber-500 text-black font-semibold px-4 py-2 rounded hover:bg-amber-600 transition duration-300">Register</a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <header class="relative min-h-screen flex flex-col justify-center items-center px-6 pt-20 bg-cover bg-center" style="background-image: linear-gradient(to bottom, rgba(0,0,0,0.4), rgba(9,9,11,1)), url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?q=80&w=1920');">
        
        <div class="text-center mb-10 max-w-3xl mt-12" data-aos="fade-up">
            <h1 class="font-luxury text-4xl md:text-6xl font-bold text-amber-400 tracking-wide mb-4">
                The World's Finest Journey
            </h1>
            <p class="text-zinc-400 text-lg md:text-xl font-light tracking-wide">
                Jelajahi destinasi global dengan kenyamanan kelas utama dan pelayanan eksklusif tanpa batas.
            </p>
        </div>

        <div class="w-full max-w-6xl bg-zinc-900/90 p-6 rounded-xl border border-amber-500/30 shadow-2xl backdrop-blur-md" data-aos="zoom-in">
            <div class="border-b border-zinc-800 pb-3 mb-5 flex space-x-6">
                <span class="text-amber-500 font-semibold tracking-wider text-sm uppercase border-b-2 border-amber-500 pb-3 cursor-pointer">One-Way / Sekali Jalan</span>
            </div>
            
            <form action="{{ route('flights.search') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-amber-500 font-bold mb-1">Origin (Asal)</label>
                        <select name="departure_airport_id" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded p-2.5 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm">
                            <option value="">Pilih Bandara Asal</option>
                            @foreach($airports as $airport)
                                <option value="{{ $airport->id }}">{{ $airport->city }} ({{ $airport->iata_code }}) - {{ $airport->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-amber-500 font-bold mb-1">Destination (Tujuan)</label>
                        <select name="arrival_airport_id" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded p-2.5 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm">
                            <option value="">Pilih Bandara Tujuan</option>
                            @foreach($airports as $airport)
                                <option value="{{ $airport->id }}">{{ $airport->city }} ({{ $airport->iata_code }}) - {{ $airport->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs uppercase tracking-widest text-amber-500 font-bold mb-1">Departure Date</label>
                        <input type="date" name="departure_date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" class="w-full bg-zinc-950 border border-zinc-800 text-white rounded p-2.5 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs uppercase tracking-widest text-amber-500 font-bold mb-1">Passengers & Class</label>
                        <div class="grid grid-cols-3 gap-1">
                            <input type="number" name="passengers" value="1" min="1" class="col-span-1 bg-zinc-950 border border-zinc-800 text-white rounded p-2.5 text-center text-sm focus:border-amber-500 focus:ring-0">
                            <select name="class" class="col-span-2 bg-zinc-950 border border-zinc-800 text-white rounded p-2.5 text-xs focus:border-amber-500 focus:ring-0">
                                <option value="economy">Economy</option>
                                <option value="business">Business</option>
                                <option value="first">First Class</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-black font-bold uppercase tracking-widest text-xs py-3.5 px-4 rounded transition duration-300 shadow-lg shadow-amber-500/20">
                            Search Flights
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </header>

    <section id="destinations" class="max-w-7xl mx-auto px-6 py-24">
        <div class="text-center mb-16">
            <h2 class="font-luxury text-3xl md:text-4xl font-bold text-white tracking-wider">Popular Destinations</h2>
            <div class="h-0.5 w-20 bg-amber-500 mx-auto mt-4"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($featuredDestinations ?? [] as $dest)
                <div class="group relative rounded-xl overflow-hidden border border-zinc-800 bg-zinc-900 transition duration-500 hover:-translate-y-2">
                    <div class="h-64 overflow-hidden">
                        <img src="{{ $dest->image_url }}" alt="{{ $dest->city_name }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                    </div>
                    <div class="p-6">
                        <h3 class="font-luxury text-xl font-bold text-amber-400">{{ $dest->city_name }}</h3>
                        <p class="text-zinc-400 text-sm mt-2 line-clamp-2">{{ $dest->description }}</p>
                    </div>
                </div>
            @empty
                <div class="group relative rounded-xl overflow-hidden border border-zinc-800 bg-zinc-900">
                    <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=600" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="font-luxury text-xl font-bold text-amber-400">Bali, Indonesia</h3>
                        <p class="text-zinc-400 text-sm mt-2">Surga tropis dengan kebudayaan eksotis nan mewah.</p>
                    </div>
                </div>
                <div class="group relative rounded-xl overflow-hidden border border-zinc-800 bg-zinc-900">
                    <img src="https://images.unsplash.com/photo-1525625293386-3f8f99389edd?q=80&w=600" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="font-luxury text-xl font-bold text-amber-400">Singapore</h3>
                        <p class="text-zinc-400 text-sm mt-2">Pusat bisnis kosmopolitan dengan arsitektur futuristik modern.</p>
                    </div>
                </div>
                <div class="group relative rounded-xl overflow-hidden border border-zinc-800 bg-zinc-900">
                    <img src="https://images.unsplash.com/photo-1512453979798-5ea266f8880c?q=80&w=600" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="font-luxury text-xl font-bold text-amber-400">Dubai, UAE</h3>
                        <p class="text-zinc-400 text-sm mt-2">Kemegahan timur tengah yang mendefinisikan arti kemewahan murni.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Featured Airlines -->
    <section id="airlines" class="max-w-7xl mx-auto px-6 py-24">
        <div class="text-center mb-16">
            <h2 class="font-luxury text-3xl md:text-4xl font-bold text-white tracking-wider">Featured Airlines</h2>
            <div class="h-0.5 w-20 bg-amber-500 mx-auto mt-4"></div>
            <p class="text-zinc-400 mt-4">Terbang bersama maskapai premium terbaik dunia</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @php
                $featuredAirlines = \App\Models\Airline::all();
            @endphp
            @foreach($featuredAirlines as $al)
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-8 text-center hover:border-amber-500/30 transition group">
                <div class="w-16 h-16 bg-amber-500/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-amber-500/20 transition">
                    <span class="text-amber-500 font-luxury text-xl font-bold">{{ substr($al->name, 0, 2) }}</span>
                </div>
                <h3 class="text-white font-semibold">{{ $al->name }}</h3>
                <p class="text-zinc-500 text-xs mt-1">{{ $al->code }} • {{ $al->registration_number ?? '-' }}</p>
                @if($al->description)
                    <p class="text-zinc-400 text-xs mt-2">{{ $al->description }}</p>
                @endif
            </div>
            @endforeach
        </div>
    </section>

    <!-- Testimonials -->
    <section id="testimonials" class="bg-black py-24">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="font-luxury text-3xl md:text-4xl font-bold text-white tracking-wider">What Our Passengers Say</h2>
                <div class="h-0.5 w-20 bg-amber-500 mx-auto mt-4"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($testimonials as $t)
                <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
                    <div class="flex items-center gap-1 mb-3">
                        @for($i=1; $i<=5; $i++)
                            <svg class="w-4 h-4 {{ $i <= $t->rating ? 'text-amber-500' : 'text-zinc-700' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-zinc-300 text-sm leading-relaxed">"{{ $t->review }}"</p>
                    <div class="mt-4 pt-4 border-t border-zinc-800">
                        <p class="text-white font-semibold text-sm">{{ $t->name }}</p>
                    </div>
                </div>
                @empty
                <div class="col-span-4 text-center text-zinc-500">Belum ada testimoni</div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="max-w-7xl mx-auto px-6 py-24">
        <div class="text-center mb-16">
            <h2 class="font-luxury text-3xl md:text-4xl font-bold text-white tracking-wider">Why Choose LuxuryFly</h2>
            <div class="h-0.5 w-20 bg-amber-500 mx-auto mt-4"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-amber-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-white font-semibold text-lg mb-2">Best Price Guarantee</h3>
                <p class="text-zinc-400 text-sm">Harga terbaik untuk setiap penerbangan dengan maskapai premium dunia.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-amber-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h3 class="text-white font-semibold text-lg mb-2">Secure Booking</h3>
                <p class="text-zinc-400 text-sm">Sistem pembayaran aman dengan enkripsi penuh untuk data Anda.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-amber-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <h3 class="text-white font-semibold text-lg mb-2">24/7 Support</h3>
                <p class="text-zinc-400 text-sm">Layanan pelanggan siap membantu Anda kapan saja, di mana saja.</p>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="bg-black py-24">
        <div class="max-w-4xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="font-luxury text-3xl md:text-4xl font-bold text-white tracking-wider">Frequently Asked Questions</h2>
                <div class="h-0.5 w-20 bg-amber-500 mx-auto mt-4"></div>
            </div>
            <div class="space-y-3" x-data="{ active: null }">
                @forelse($faqs as $index => $faq)
                <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
                    <button @click="active = active === {{ $index }} ? null : {{ $index }}" class="w-full flex items-center justify-between p-5 text-left">
                        <span class="text-white font-medium">{{ $faq->question }}</span>
                        <svg class="w-5 h-5 text-zinc-400 transition-transform" :class="{ 'rotate-180': active === {{ $index }} }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="active === {{ $index }}" x-transition class="px-5 pb-5">
                        <p class="text-zinc-400 text-sm">{{ $faq->answer }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center text-zinc-500">Belum ada FAQ</div>
                @endforelse
            </div>
        </div>
    </section>

    <footer class="bg-black border-t border-zinc-900 text-zinc-500 text-sm py-12">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
                <p class="font-luxury text-lg font-bold text-amber-500 tracking-widest mb-2">LUXURYFLY</p>
                <p>&copy; {{ date('Y') }} LuxuryFly International. All rights reserved.</p>
            </div>
            <div class="md:text-right text-zinc-400 space-x-6">
                <a href="#" class="hover:text-amber-500">Terms of Service</a>
                <a href="#" class="hover:text-amber-500">Privacy Policy</a>
            </div>
        </div>
    </footer>

</body>
</html>