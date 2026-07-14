@extends('layouts.admin')
@section('title', 'Tambah Penerbangan')
@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-gradient-to-r from-amber-500/10 to-amber-600/5 border border-amber-500/20 rounded-xl p-6">
        <h2 class="text-xl font-bold text-white">Tambah Penerbangan Baru</h2>
        <p class="text-zinc-400 text-sm mt-1">Lengkapi data penerbangan dengan konfigurasi kursi otomatis</p>
    </div>

    <form method="POST" action="{{ route('admin.flights.store') }}" class="space-y-6">
        @csrf
        
        <!-- Airline & Aircraft Selection -->
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
            <h3 class="text-base font-semibold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-plane text-amber-500"></i> Maskapai & Pesawat
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Maskapai *</label>
                    <select name="airline_id" id="airline_id" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition" required>
                        <option value="">Pilih Maskapai</option>
                        @foreach($airlines as $al)
                        <option value="{{ $al->id }}" {{ old('airline_id') == $al->id ? 'selected' : '' }}>{{ $al->name }} ({{ $al->code ?? substr($al->name,0,2) }})</option>
                        @endforeach
                    </select>
                    @error('airline_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Pesawat *</label>
                    <select name="airplane_id" id="airplane_id" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition" required>
                        <option value="">Pilih Pesawat</option>
                        @foreach($airplanes as $ap)
                        <option value="{{ $ap->id }}" 
                                data-airline="{{ $ap->airline_id }}"
                                data-first="{{ $ap->first_class_seats }}"
                                data-business="{{ $ap->business_class_seats }}"
                                data-economy="{{ $ap->economy_class_seats }}"
                                data-total="{{ $ap->getTotalSeatsCount() }}"
                                data-model="{{ $ap->model }}"
                                {{ old('airplane_id') == $ap->id ? 'selected' : '' }}>
                            {{ $ap->model }} ({{ $ap->registration_number }})
                        </option>
                        @endforeach
                    </select>
                    @error('airplane_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Aircraft Configuration Preview -->
            <div id="aircraft-config" class="hidden mt-4">
                <div class="bg-zinc-800/30 border border-zinc-700 rounded-xl p-5">
                    <h4 class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
                        <i class="fas fa-cogs text-amber-500"></i> Aircraft Configuration
                    </h4>
                    <div class="grid grid-cols-4 gap-4">
                        <div class="bg-amber-500/10 border border-amber-500/20 rounded-lg p-3 text-center">
                            <i class="fas fa-crown text-amber-500 text-lg mb-1"></i>
                            <p class="text-zinc-400 text-xs">First Class</p>
                            <p class="text-white font-bold text-lg" id="first-seats-count">0</p>
                            <p class="text-zinc-500 text-xs">seats</p>
                        </div>
                        <div class="bg-purple-500/10 border border-purple-500/20 rounded-lg p-3 text-center">
                            <i class="fas fa-briefcase text-purple-400 text-lg mb-1"></i>
                            <p class="text-zinc-400 text-xs">Business</p>
                            <p class="text-white font-bold text-lg" id="business-seats-count">0</p>
                            <p class="text-zinc-500 text-xs">seats</p>
                        </div>
                        <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-lg p-3 text-center">
                            <i class="fas fa-chair text-emerald-400 text-lg mb-1"></i>
                            <p class="text-zinc-400 text-xs">Economy</p>
                            <p class="text-white font-bold text-lg" id="economy-seats-count">0</p>
                            <p class="text-zinc-500 text-xs">seats</p>
                        </div>
                        <div class="bg-amber-500/10 border border-amber-500/20 rounded-lg p-3 text-center">
                            <i class="fas fa-layer-group text-amber-500 text-lg mb-1"></i>
                            <p class="text-zinc-400 text-xs">Total</p>
                            <p class="text-white font-bold text-lg" id="total-seats-count">0</p>
                            <p class="text-zinc-500 text-xs">seats</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flight Number & Base Price -->
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
            <h3 class="text-base font-semibold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-tag text-amber-500"></i> Penerbangan & Harga
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">No. Penerbangan</label>
                    <input type="text" name="flight_number" id="flight_number" value="{{ old('flight_number') }}" placeholder="Auto-generated" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                    <p class="text-xs text-zinc-500 mt-1">Kosongkan untuk generate otomatis</p>
                    @error('flight_number') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Base Price (Economy) *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400">Rp</span>
                        <input type="number" name="price" id="base_price" value="{{ old('price') }}" placeholder="Contoh: 3000000" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg pl-10 pr-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition" required>
                    </div>
                    @error('price') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Cabin Price Preview -->
            <div id="price-preview" class="hidden mt-4">
                <div class="bg-zinc-800/30 border border-zinc-700 rounded-xl p-5">
                    <h4 class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
                        <i class="fas fa-chart-bar text-amber-500"></i> Cabin Price Preview
                    </h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-emerald-500/5 border border-emerald-500/10 rounded-lg p-4 text-center">
                            <i class="fas fa-chair text-emerald-400 text-lg mb-1"></i>
                            <p class="text-zinc-400 text-xs">Economy</p>
                            <p class="text-emerald-400 font-bold text-lg" id="economy-price-preview">Rp 0</p>
                        </div>
                        <div class="bg-purple-500/5 border border-purple-500/10 rounded-lg p-4 text-center">
                            <i class="fas fa-briefcase text-purple-400 text-lg mb-1"></i>
                            <p class="text-zinc-400 text-xs">Business</p>
                            <p class="text-purple-400 font-bold text-lg" id="business-price-preview">Rp 0</p>
                            <p class="text-zinc-600 text-xs mt-1">Base × 2.33</p>
                        </div>
                        <div class="bg-amber-500/5 border border-amber-500/10 rounded-lg p-4 text-center">
                            <i class="fas fa-crown text-amber-500 text-lg mb-1"></i>
                            <p class="text-zinc-400 text-xs">First Class</p>
                            <p class="text-amber-500 font-bold text-lg" id="first-price-preview">Rp 0</p>
                            <p class="text-zinc-600 text-xs mt-1">Base × 4.33</p>
                        </div>
                    </div>
                    <!-- Hidden fields for auto-calculated prices -->
                    <input type="hidden" name="economy_class_price" id="economy_class_price">
                    <input type="hidden" name="business_class_price" id="business_class_price">
                    <input type="hidden" name="first_class_price" id="first_class_price">
                </div>
            </div>
        </div>

        <!-- Route & Time -->
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
            <h3 class="text-base font-semibold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-route text-amber-500"></i> Rute & Waktu
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Bandara Asal *</label>
                    <select name="departure_airport_id" id="departure_airport" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition" required>
                        <option value="">Pilih Bandara</option>
                        @foreach($airports as $ap)
                        <option value="{{ $ap->id }}" {{ old('departure_airport_id') == $ap->id ? 'selected' : '' }}>{{ $ap->city }} ({{ $ap->iata_code }})</option>
                        @endforeach
                    </select>
                    @error('departure_airport_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Bandara Tujuan *</label>
                    <select name="arrival_airport_id" id="arrival_airport" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition" required>
                        <option value="">Pilih Bandara</option>
                        @foreach($airports as $ap)
                        <option value="{{ $ap->id }}" {{ old('arrival_airport_id') == $ap->id ? 'selected' : '' }}>{{ $ap->city }} ({{ $ap->iata_code }})</option>
                        @endforeach
                    </select>
                    @error('arrival_airport_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Waktu Berangkat *</label>
                    <input type="datetime-local" name="departure_time" id="departure_time" value="{{ old('departure_time') }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition" required>
                    @error('departure_time') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Waktu Tiba *</label>
                    <input type="datetime-local" name="arrival_time" id="arrival_time" value="{{ old('arrival_time') }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition" required>
                    @error('arrival_time') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Durasi</label>
                    <input type="text" name="flight_duration" id="flight_duration" value="{{ old('flight_duration') }}" readonly placeholder="Auto-calculated" class="w-full bg-zinc-800/50 border border-zinc-700 rounded-lg px-4 py-2.5 text-zinc-400 cursor-not-allowed">
                    <p class="text-xs text-zinc-500 mt-1">Dihitung otomatis</p>
                </div>
            </div>
        </div>

        <!-- Seats & Status -->
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
            <h3 class="text-base font-semibold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-chair text-amber-500"></i> Kursi & Status
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Kursi Tersedia</label>
                    <input type="number" name="available_seats" id="available_seats" value="{{ old('available_seats') }}" readonly placeholder="Auto from aircraft" class="w-full bg-zinc-800/50 border border-zinc-700 rounded-lg px-4 py-2.5 text-zinc-400 cursor-not-allowed" required>
                    <p class="text-xs text-zinc-500 mt-1">Mengikuti konfigurasi pesawat</p>
                    @error('available_seats') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Gate</label>
                    <input type="text" name="gate" value="{{ old('gate') }}" maxlength="10" placeholder="Contoh: A1" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Terminal</label>
                    <input type="text" name="terminal" value="{{ old('terminal') }}" maxlength="10" placeholder="Contoh: T3" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Status</label>
                    <select name="status" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition" required>
                        <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="boarding" {{ old('status') == 'boarding' ? 'selected' : '' }}>Boarding</option>
                        <option value="delayed" {{ old('status') == 'delayed' ? 'selected' : '' }}>Delayed</option>
                        <option value="departed" {{ old('status') == 'departed' ? 'selected' : '' }}>Departed</option>
                        <option value="arrived" {{ old('status') == 'arrived' ? 'selected' : '' }}>Arrived</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex gap-3">
            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-amber-500 to-amber-600 text-black rounded-xl font-semibold hover:from-amber-600 hover:to-amber-700 transition shadow-lg shadow-amber-500/20">
                <i class="fas fa-save mr-2"></i> Simpan Penerbangan
            </button>
            <a href="{{ route('admin.flights.index') }}" class="px-8 py-3 bg-zinc-800 text-zinc-300 rounded-xl font-semibold hover:bg-zinc-700 transition">
                Batal
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const airplaneSelect = document.getElementById('airplane_id');
    const basePriceInput = document.getElementById('base_price');
    const departureTime = document.getElementById('departure_time');
    const arrivalTime = document.getElementById('arrival_time');
    const durationInput = document.getElementById('flight_duration');
    const availableSeats = document.getElementById('available_seats');
    
    // Aircraft config elements
    const aircraftConfig = document.getElementById('aircraft-config');
    const firstSeats = document.getElementById('first-seats-count');
    const businessSeats = document.getElementById('business-seats-count');
    const economySeats = document.getElementById('economy-seats-count');
    const totalSeats = document.getElementById('total-seats-count');
    
    // Price preview elements
    const pricePreview = document.getElementById('price-preview');
    const economyPrice = document.getElementById('economy-price-preview');
    const businessPrice = document.getElementById('business-price-preview');
    const firstPrice = document.getElementById('first-price-preview');
    const economyHidden = document.getElementById('economy_class_price');
    const businessHidden = document.getElementById('business_class_price');
    const firstHidden = document.getElementById('first_class_price');
    
    function formatRupiah(amount) {
        return 'Rp ' + Math.round(amount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    
    function updateAircraftConfig() {
        const selected = airplaneSelect.options[airplaneSelect.selectedIndex];
        
        if (selected && selected.value) {
            const first = parseInt(selected.dataset.first) || 0;
            const business = parseInt(selected.dataset.business) || 0;
            const economy = parseInt(selected.dataset.economy) || 0;
            const total = parseInt(selected.dataset.total) || (first + business + economy);
            
            firstSeats.textContent = first;
            businessSeats.textContent = business;
            economySeats.textContent = economy;
            totalSeats.textContent = total;
            availableSeats.value = total;
            
            aircraftConfig.classList.remove('hidden');
            updatePrices();
        } else {
            aircraftConfig.classList.add('hidden');
            pricePreview.classList.add('hidden');
            availableSeats.value = '';
        }
    }
    
    function updatePrices() {
        const basePrice = parseFloat(basePriceInput.value) || 0;
        
        if (basePrice > 0) {
            const economyP = basePrice;
            const businessP = Math.round(basePrice * 2.33);
            const firstP = Math.round(basePrice * 4.33);
            
            economyPrice.textContent = formatRupiah(economyP);
            businessPrice.textContent = formatRupiah(businessP);
            firstPrice.textContent = formatRupiah(firstP);
            
            economyHidden.value = economyP;
            businessHidden.value = businessP;
            firstHidden.value = firstP;
            
            pricePreview.classList.remove('hidden');
        } else {
            pricePreview.classList.add('hidden');
            economyHidden.value = '';
            businessHidden.value = '';
            firstHidden.value = '';
        }
    }
    
    function updateDuration() {
        if (departureTime.value && arrivalTime.value) {
            const dep = new Date(departureTime.value);
            const arr = new Date(arrivalTime.value);
            
            if (arr > dep) {
                const diffMs = arr - dep;
                const hours = Math.floor(diffMs / (1000 * 60 * 60));
                const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
                
                durationInput.value = hours + ' Jam ' + minutes + ' Menit';
            } else {
                durationInput.value = '';
            }
        } else {
            durationInput.value = '';
        }
    }
    
    // Event listeners
    airplaneSelect.addEventListener('change', updateAircraftConfig);
    basePriceInput.addEventListener('input', updatePrices);
    departureTime.addEventListener('change', updateDuration);
    arrivalTime.addEventListener('change', updateDuration);
    
    // Initial state if old values exist
    if (airplaneSelect.value) updateAircraftConfig();
    if (basePriceInput.value) updatePrices();
    if (departureTime.value && arrivalTime.value) updateDuration();
});
</script>
@endpush
@endsection