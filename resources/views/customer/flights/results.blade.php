@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-zinc-950">
    <div class="max-w-7xl mx-auto px-6 py-12">
        <div class="space-y-6">
            <!-- Header -->
            <div class="bg-gradient-to-r from-amber-500/10 to-amber-600/5 border border-amber-500/20 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-white">Search Results</h2>
                        <p class="text-zinc-400 text-sm mt-1">{{ $flights->count() }} flights found</p>
                    </div>
                    <a href="{{ route('homepage') }}" class="text-amber-500 hover:text-amber-400 text-sm transition">&larr; New Search</a>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-5">
                <form action="{{ route('flights.search') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @foreach(request()->except(['airline_id','max_price','time_slat','page']) as $key => $value)
                        @if($value && !is_array($value)) <input type="hidden" name="{{ $key }}" value="{{ $value }}"> @endif
                    @endforeach
                    <div>
                        <label class="block text-xs text-zinc-500 mb-1">Airline</label>
                        <select name="airline_id" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white text-sm">
                            <option value="">All Airlines</option>
                            @foreach($airlines as $al)
                                <option value="{{ $al->id }}" {{ request('airline_id')==$al->id?'selected':'' }}>{{ $al->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-zinc-500 mb-1">Max Price</label>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Rp..." class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-zinc-500 mb-1">Time</label>
                        <select name="time_slat" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-white text-sm">
                            <option value="">All Times</option>
                            <option value="morning" {{ request('time_slat')=='morning'?'selected':'' }}>Morning (04-11)</option>
                            <option value="afternoon" {{ request('time_slat')=='afternoon'?'selected':'' }}>Afternoon (11-18)</option>
                            <option value="night" {{ request('time_slat')=='night'?'selected':'' }}>Night (18-04)</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-amber-500 text-black font-semibold px-4 py-2 rounded-lg hover:bg-amber-600 transition">Filter</button>
                    </div>
                </form>
            </div>

            <!-- Results -->
            @forelse($flights as $flight)
            <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6 hover:border-amber-500/30 transition group">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-center">
                    <div class="md:col-span-2">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-amber-500/10 rounded-full flex items-center justify-center">
                                <span class="text-amber-500 font-bold">{{ substr($flight->airline->name,0,2) }}</span>
                            </div>
                            <div>
                                <p class="text-white font-semibold">{{ $flight->airline->name }}</p>
                                <p class="text-xs text-zinc-500">{{ $flight->flight_number }} • {{ $flight->airplane->model }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="flex items-center justify-between gap-4">
                            <div class="text-center">
                                <p class="text-xl font-bold text-white">{{ \Carbon\Carbon::parse($flight->departure_time)->format('H:i') }}</p>
                                <p class="text-xs text-zinc-500">{{ $flight->departureAirport->city }} ({{ $flight->departureAirport->iata_code }})</p>
                            </div>
                            <div class="flex-1 text-center">
                                <div class="border-t border-dashed border-zinc-600 relative">
                                    <span class="absolute -top-2 left-1/2 -translate-x-1/2 bg-zinc-900 px-2 text-xs text-zinc-500">{{ \Carbon\Carbon::parse($flight->departure_time)->diffInHours($flight->arrival_time) }}h</span>
                                </div>
                            </div>
                            <div class="text-center">
                                <p class="text-xl font-bold text-white">{{ \Carbon\Carbon::parse($flight->arrival_time)->format('H:i') }}</p>
                                <p class="text-xs text-zinc-500">{{ $flight->arrivalAirport->city }} ({{ $flight->arrivalAirport->iata_code }})</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-center md:text-right">
                        @php
                            $displayPrice = match($class) {
                                'first' => $flight->first_class_price ?? $flight->price * 4.33,
                                'business' => $flight->business_class_price ?? $flight->price * 2.33,
                                default => $flight->economy_class_price ?? $flight->price,
                            };
                            $classLabel = match($class) {
                                'first' => 'First Class',
                                'business' => 'Business Class',
                                default => 'Economy Class',
                            };
                            $classColor = match($class) {
                                'first' => 'text-amber-500',
                                'business' => 'text-purple-400',
                                default => 'text-emerald-400',
                            };
                        @endphp
                        <p class="text-xs {{ $classColor }} font-semibold mb-1">{{ $classLabel }}</p>
                        <p class="text-2xl font-bold text-amber-500">Rp {{ number_format($displayPrice,0,',','.') }}</p>
                        <p class="text-xs text-zinc-500">{{ $flight->available_seats }} seats left</p>
                    </div>
                    <div class="text-center md:text-right">
                        @auth
                            <a href="{{ route('customer.flights.seats', $flight) }}" class="inline-block px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 text-black font-semibold rounded-lg hover:from-amber-600 hover:to-amber-700 transition shadow-lg shadow-amber-500/20 text-sm">Select Seats</a>
                        @else
                            <a href="{{ route('login') }}" class="inline-block px-6 py-2.5 bg-zinc-800 text-zinc-300 rounded-lg hover:bg-zinc-700 transition text-sm">Login to Book</a>
                        @endauth
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-16 bg-zinc-900/50 border border-zinc-800 rounded-xl">
                <i class="fas fa-plane-slash text-4xl text-zinc-700 mb-4 block"></i>
                <h3 class="text-xl text-zinc-400">No flights found</h3>
                <p class="text-zinc-600 mt-2">Try different search criteria</p>
                <a href="{{ route('homepage') }}" class="inline-block mt-4 text-amber-500 hover:underline">Back to Home</a>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection