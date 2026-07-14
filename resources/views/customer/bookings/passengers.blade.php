@extends('layouts.customer')
@section('title', 'Passenger Information')
@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-gradient-to-r from-amber-500/10 to-amber-600/5 border border-amber-500/20 rounded-xl p-6">
        <h2 class="text-xl font-bold text-white">Passenger Information</h2>
        <p class="text-zinc-400 text-sm mt-1">Enter details for all passengers</p>
    </div>

    <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-base font-semibold text-white">Flight Details</h3>
            <span class="text-xs text-zinc-500">{{ $flight->airline->name }} - {{ $flight->flight_number }}</span>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div><span class="text-zinc-500">Route</span><p class="text-white font-semibold">{{ $flight->departureAirport->iata_code }} → {{ $flight->arrivalAirport->iata_code }}</p></div>
            <div><span class="text-zinc-500">Departure</span><p class="text-white font-semibold">{{ \Carbon\Carbon::parse($flight->departure_time)->format('d M Y H:i') }}</p></div>
            <div><span class="text-zinc-500">Duration</span><p class="text-white font-semibold">{{ $flight->duration }}h</p></div>
            <div><span class="text-zinc-500">Passengers</span><p class="text-white font-semibold">{{ $passengerCount }} person(s)</p></div>
        </div>
    </div>

    <form method="POST" action="{{ route('customer.bookings.store') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="flight_id" value="{{ $flight->id }}">
        <input type="hidden" name="cabin_class" value="{{ $cabinClass }}">
        @foreach($selectedSeats as $seat)
            <input type="hidden" name="seats[]" value="{{ $seat->id }}">
        @endforeach
        
        <!-- Pricing Summary -->
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @php
                        $classIcon = match($cabinClass) {
                            'first' => 'fa-crown text-amber-500',
                            'business' => 'fa-briefcase text-purple-400',
                            default => 'fa-chair text-emerald-400',
                        };
                        $classLabel = match($cabinClass) {
                            'first' => 'First Class',
                            'business' => 'Business Class',
                            default => 'Economy Class',
                        };
                        $classColor = match($cabinClass) {
                            'first' => 'text-amber-500',
                            'business' => 'text-purple-400',
                            default => 'text-emerald-400',
                        };
                    @endphp
                    <i class="fas {{ $classIcon }}"></i>
                    <span class="text-white font-semibold">{{ $classLabel }}</span>
                </div>
                <div class="text-right">
                    <p class="text-zinc-400 text-xs">Price per person</p>
                    <p class="{{ $classColor }} font-bold">Rp {{ number_format($classPrice, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-zinc-700 flex justify-between">
                <span class="text-zinc-400 text-sm">{{ $passengerCount }} passenger(s)</span>
                <span class="text-white font-bold text-lg">Rp {{ number_format($classPrice * $passengerCount, 0, ',', '.') }}</span>
            </div>
        </div>

        @foreach($selectedSeats as $index => $seat)
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl overflow-hidden">
            <div class="p-4 bg-zinc-800/30 border-b border-zinc-800 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-white">Passenger {{ $index + 1 }}</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Seat: {{ $seat->seat_number }} ({{ ucfirst($seat->class) }})</p>
                </div>
                <span class="text-xs {{ $classColor }} font-semibold">{{ $classLabel }}</span>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2">Full Name *</label>
                        <input type="text" name="passengers[{{ $index }}][full_name]" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2">Email *</label>
                        <input type="email" name="passengers[{{ $index }}][email]" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2">Phone *</label>
                        <input type="text" name="passengers[{{ $index }}][phone]" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2">Gender *</label>
                        <select name="passengers[{{ $index }}][gender]" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>
                            <option value="">Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2">Date of Birth *</label>
                        <input type="date" name="passengers[{{ $index }}][date_of_birth]" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2">Nationality *</label>
                        <input type="text" name="passengers[{{ $index }}][nationality]" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Passport Number (Optional)</label>
                    <input type="text" name="passengers[{{ $index }}][passport_number]" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2">Emergency Contact Name</label>
                        <input type="text" name="passengers[{{ $index }}][emergency_contact]" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300 mb-2">Emergency Contact Phone</label>
                        <input type="text" name="passengers[{{ $index }}][emergency_phone]" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 text-black rounded-xl font-semibold hover:from-amber-600 hover:to-amber-700 transition shadow-lg shadow-amber-500/20">
                Complete Booking
            </button>
            <a href="{{ route('customer.flights.seats', $flight) }}" class="px-6 py-2.5 bg-zinc-800 text-zinc-300 rounded-lg hover:bg-zinc-700 transition">Back</a>
        </div>
    </form>
</div>
@endsection