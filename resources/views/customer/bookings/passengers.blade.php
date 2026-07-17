@extends('layouts.customer')
@section('title', 'Passenger Information')
@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-gradient-to-r from-amber-500/10 to-amber-600/5 border border-amber-500/20 rounded-xl p-6">
        <h2 class="text-xl font-bold text-white">Passenger Information</h2>
        <p class="text-zinc-400 text-sm mt-1">Complete the data for all {{ $passengerCount }} passengers. Every form is required.</p>
    </div>

    @if(session('error'))
    <div class="bg-red-500/10 border border-red-500/20 text-red-300 rounded-xl p-4">{{ session('error') }}</div>
    @endif

    @if($errors->any())
    <div class="bg-red-500/10 border border-red-500/20 text-red-300 rounded-xl p-4">
        <p class="font-semibold">Please correct the passenger data below.</p>
        <ul class="list-disc ml-5 mt-2 text-sm">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-5 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div><span class="text-zinc-500">Flight</span><p class="text-white font-semibold">{{ $flight->flight_number }}</p></div>
        <div><span class="text-zinc-500">Route</span><p class="text-white font-semibold">{{ $flight->departureAirport->iata_code }} → {{ $flight->arrivalAirport->iata_code }}</p></div>
        <div><span class="text-zinc-500">Class</span><p class="text-amber-500 font-semibold">{{ $flight->getCabinClassName($cabinClass) }}</p></div>
        <div><span class="text-zinc-500">Total</span><p class="text-white font-semibold">Rp {{ number_format($classPrice * $passengerCount, 0, ',', '.') }}</p></div>
    </div>

    <form method="POST" action="{{ route('customer.bookings.store') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="flight_id" value="{{ $flight->id }}">
        <input type="hidden" name="cabin_class" value="{{ $cabinClass }}">
        @foreach($selectedSeats as $seat)<input type="hidden" name="seats[]" value="{{ $seat->id }}">@endforeach

        @foreach($selectedSeats as $index => $seat)
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl overflow-hidden">
            <div class="p-4 bg-zinc-800/30 border-b border-zinc-800 flex justify-between">
                <div><h3 class="text-white font-semibold">Passenger {{ $index + 1 }}</h3><p class="text-zinc-500 text-xs">Seat {{ $seat->seat_number }}</p></div>
                <span class="text-amber-500 text-sm font-semibold">{{ ucfirst($seat->class) }}</span>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-sm text-zinc-300 mb-2">Full Name *</label><input type="text" name="passengers[{{ $index }}][full_name]" value="{{ old("passengers.$index.full_name") }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white" required></div>
                    <div><label class="block text-sm text-zinc-300 mb-2">Email *</label><input type="email" name="passengers[{{ $index }}][email]" value="{{ old("passengers.$index.email") }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white" required></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div><label class="block text-sm text-zinc-300 mb-2">Phone *</label><input type="text" name="passengers[{{ $index }}][phone]" value="{{ old("passengers.$index.phone") }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white" required></div>
                    <div><label class="block text-sm text-zinc-300 mb-2">Gender *</label><select name="passengers[{{ $index }}][gender]" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white" required><option value="">Select</option><option value="male" @selected(old("passengers.$index.gender") === 'male')>Male</option><option value="female" @selected(old("passengers.$index.gender") === 'female')>Female</option></select></div>
                    <div><label class="block text-sm text-zinc-300 mb-2">Date of Birth *</label><input type="date" max="{{ now()->subDay()->format('Y-m-d') }}" name="passengers[{{ $index }}][date_of_birth]" value="{{ old("passengers.$index.date_of_birth") }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white" required></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-sm text-zinc-300 mb-2">Nationality *</label><input type="text" name="passengers[{{ $index }}][nationality]" value="{{ old("passengers.$index.nationality", 'Indonesia') }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white" required></div>
                    <div><label class="block text-sm text-zinc-300 mb-2">Passport Number</label><input type="text" name="passengers[{{ $index }}][passport_number]" value="{{ old("passengers.$index.passport_number") }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-sm text-zinc-300 mb-2">Emergency Contact</label><input type="text" name="passengers[{{ $index }}][emergency_contact]" value="{{ old("passengers.$index.emergency_contact") }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white"></div>
                    <div><label class="block text-sm text-zinc-300 mb-2">Emergency Phone</label><input type="text" name="passengers[{{ $index }}][emergency_phone]" value="{{ old("passengers.$index.emergency_phone") }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white"></div>
                </div>
            </div>
        </div>
        @endforeach

        <div class="flex gap-3">
            <button type="submit" class="px-6 py-3 bg-amber-500 text-black rounded-xl font-semibold hover:bg-amber-600 transition"><i class="fas fa-ticket mr-2"></i>Book {{ $passengerCount }} Ticket(s)</button>
            <a href="{{ route('customer.flights.seats', $flight) }}" class="px-6 py-3 bg-zinc-800 text-zinc-300 rounded-xl">Back</a>
        </div>
    </form>
</div>
@endsection
