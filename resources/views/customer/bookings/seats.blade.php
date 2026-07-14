@extends('layouts.customer')
@section('title', 'Select Seats')
@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-amber-500/10 to-amber-600/5 border border-amber-500/20 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-white">Select Your Seats</h2>
                <p class="text-zinc-400 text-sm mt-1">{{ $flight->airline->name }} - {{ $flight->flight_number }} | {{ $flight->departureAirport->city }} → {{ $flight->arrivalAirport->city }}</p>
            </div>
            <div class="text-right">
                <p class="text-amber-500 font-semibold">{{ $flight->getCabinClassName($cabinClass) }}</p>
                <p class="text-zinc-400 text-sm">Rp {{ number_format($classPrice, 0, ',', '.') }}/person</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Seat Map -->
        <div class="lg:col-span-3 space-y-6">
            <form id="seat-form" method="POST" action="{{ route('customer.flights.passengers', $flight) }}">
                @csrf
                <input type="hidden" name="cabin_class" value="{{ $cabinClass }}">

                <!-- First Class Section -->
                @if($firstClassSeats->count() > 0)
                <div class="bg-zinc-900/80 border border-amber-500/20 rounded-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-500/10 to-amber-600/5 px-6 py-4 border-b border-amber-500/20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-crown text-amber-500"></i>
                                <div>
                                    <h3 class="text-white font-semibold">First Class</h3>
                                    <p class="text-zinc-400 text-xs">Rows 1-2 • 4 seats</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-amber-500 font-bold">Rp {{ number_format($flight->first_class_price ?? $flight->price * 4.33, 0, ',', '.') }}</p>
                                <p class="text-zinc-500 text-xs">per person</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-center gap-8">
                            @foreach($firstClassSeats->chunk(1) as $row)
                            <div class="space-y-3">
                                @foreach($row as $seat)
                                <div class="flex items-center gap-2">
                                    <label class="seat-label {{ $cabinClass === 'first' ? 'cursor-pointer' : 'cursor-not-allowed opacity-40' }} {{ $seat->status === 'booked' ? 'opacity-30' : '' }}">
                                        <input type="checkbox" name="seats[]" value="{{ $seat->id }}" 
                                            class="peer sr-only" 
                                            {{ $cabinClass !== 'first' || $seat->status === 'booked' ? 'disabled' : '' }}
                                            data-class="first">
                                        <div class="w-12 h-12 flex items-center justify-center rounded-lg text-xs font-semibold border-2 transition-all duration-200
                                            @if($seat->status === 'booked') bg-red-500/20 border-red-500/30 text-red-400
                                            @elseif($cabinClass !== 'first') bg-zinc-800/50 border-zinc-700 text-zinc-600
                                            @else bg-zinc-800 border-zinc-700 text-zinc-300 hover:border-amber-500 peer-checked:bg-amber-500 peer-checked:border-amber-500 peer-checked:text-black
                                            @endif">
                                            {{ $seat->seat_number }}
                                        </div>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Business Class Section -->
                @if($businessClassSeats->count() > 0)
                <div class="bg-zinc-900/80 border border-purple-500/20 rounded-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500/10 to-purple-600/5 px-6 py-4 border-b border-purple-500/20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-briefcase text-purple-400"></i>
                                <div>
                                    <h3 class="text-white font-semibold">Business Class</h3>
                                    <p class="text-zinc-400 text-xs">Rows 3-5 • 12 seats</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-purple-400 font-bold">Rp {{ number_format($flight->business_class_price ?? $flight->price * 2.33, 0, ',', '.') }}</p>
                                <p class="text-zinc-500 text-xs">per person</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-center gap-8">
                            @foreach($businessClassSeats->chunk(2) as $row)
                            <div class="space-y-3">
                                @foreach($row as $seat)
                                <div class="flex items-center gap-2">
                                    <label class="seat-label {{ $cabinClass === 'business' ? 'cursor-pointer' : 'cursor-not-allowed opacity-40' }} {{ $seat->status === 'booked' ? 'opacity-30' : '' }}">
                                        <input type="checkbox" name="seats[]" value="{{ $seat->id }}" 
                                            class="peer sr-only" 
                                            {{ $cabinClass !== 'business' || $seat->status === 'booked' ? 'disabled' : '' }}
                                            data-class="business">
                                        <div class="w-12 h-12 flex items-center justify-center rounded-lg text-xs font-semibold border-2 transition-all duration-200
                                            @if($seat->status === 'booked') bg-red-500/20 border-red-500/30 text-red-400
                                            @elseif($cabinClass !== 'business') bg-zinc-800/50 border-zinc-700 text-zinc-600
                                            @else bg-zinc-800 border-zinc-700 text-zinc-300 hover:border-purple-500 peer-checked:bg-purple-500 peer-checked:border-purple-500 peer-checked:text-black
                                            @endif">
                                            {{ $seat->seat_number }}
                                        </div>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Economy Class Section -->
                @if($economyClassSeats->count() > 0)
                <div class="bg-zinc-900/80 border border-emerald-500/20 rounded-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500/10 to-emerald-600/5 px-6 py-4 border-b border-emerald-500/20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-chair text-emerald-400"></i>
                                <div>
                                    <h3 class="text-white font-semibold">Economy Class</h3>
                                    <p class="text-zinc-400 text-xs">Rows 6-33 • 168 seats</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-emerald-400 font-bold">Rp {{ number_format($flight->economy_class_price ?? $flight->price, 0, ',', '.') }}</p>
                                <p class="text-zinc-500 text-xs">per person</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-center gap-4">
                            @foreach($economyClassSeats->chunk(3) as $row)
                            <div class="space-y-2">
                                @foreach($row as $seat)
                                <div class="flex items-center gap-2">
                                    <label class="seat-label {{ $cabinClass === 'economy' ? 'cursor-pointer' : 'cursor-not-allowed opacity-40' }} {{ $seat->status === 'booked' ? 'opacity-30' : '' }}">
                                        <input type="checkbox" name="seats[]" value="{{ $seat->id }}" 
                                            class="peer sr-only" 
                                            {{ $cabinClass !== 'economy' || $seat->status === 'booked' ? 'disabled' : '' }}
                                            data-class="economy">
                                        <div class="w-10 h-10 flex items-center justify-center rounded-lg text-xs font-semibold border-2 transition-all duration-200
                                            @if($seat->status === 'booked') bg-red-500/20 border-red-500/30 text-red-400
                                            @elseif($cabinClass !== 'economy') bg-zinc-800/50 border-zinc-700 text-zinc-600
                                            @else bg-zinc-800 border-zinc-700 text-zinc-300 hover:border-emerald-500 peer-checked:bg-emerald-500 peer-checked:border-emerald-500 peer-checked:text-black
                                            @endif">
                                            {{ $seat->seat_number }}
                                        </div>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Legend -->
                <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-4">
                    <div class="flex flex-wrap items-center gap-6">
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 bg-zinc-800 border-2 border-zinc-700 rounded"></div>
                            <span class="text-zinc-400 text-xs">Available</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 bg-amber-500 border-2 border-amber-500 rounded"></div>
                            <span class="text-zinc-400 text-xs">Selected</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 bg-red-500/20 border-2 border-red-500/30 rounded"></div>
                            <span class="text-zinc-400 text-xs">Occupied</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 bg-zinc-800/50 border-2 border-zinc-700 rounded"></div>
                            <span class="text-zinc-400 text-xs">Unavailable</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-crown text-amber-500 text-xs"></i>
                            <span class="text-zinc-400 text-xs">First Class</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-briefcase text-purple-400 text-xs"></i>
                            <span class="text-zinc-400 text-xs">Business</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-chair text-emerald-400 text-xs"></i>
                            <span class="text-zinc-400 text-xs">Economy</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-5 sticky top-24">
                <h3 class="text-white font-semibold mb-4">Booking Summary</h3>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-zinc-400">Flight</span>
                        <span class="text-white">{{ $flight->flight_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-400">Route</span>
                        <span class="text-white">{{ $flight->departureAirport->iata_code }} → {{ $flight->arrivalAirport->iata_code }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-400">Date</span>
                        <span class="text-white">{{ \Carbon\Carbon::parse($flight->departure_time)->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-400">Class</span>
                        <span class="text-amber-500 font-semibold">{{ $flight->getCabinClassName($cabinClass) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-400">Passengers</span>
                        <span class="text-white">{{ $passengerCount }}</span>
                    </div>
                    <div class="flex justify-between pt-3 border-t border-zinc-700">
                        <span class="text-zinc-400">Price/person</span>
                        <span class="text-white font-semibold">Rp {{ number_format($classPrice, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-400">Total</span>
                        <span class="text-amber-500 font-bold text-lg" id="total-price">Rp {{ number_format($classPrice * $passengerCount, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if($cabinClass !== 'economy' && $cabinClass !== 'business' && $cabinClass !== 'first')
                <div class="mt-4 p-3 bg-yellow-500/10 border border-yellow-500/20 rounded-lg text-yellow-400 text-xs">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Only seats from your selected cabin class can be chosen.
                </div>
                @endif

                <div class="mt-4" id="selected-count">
                    <p class="text-zinc-500 text-xs mb-2">Selected: <span id="selected-seats-count">0</span> / {{ $passengerCount }} seats</p>
                    <button type="submit" form="seat-form" id="continue-btn" disabled class="w-full px-4 py-3 bg-gradient-to-r from-amber-500 to-amber-600 text-black rounded-xl font-semibold text-sm hover:from-amber-600 hover:to-amber-700 transition shadow-lg shadow-amber-500/20 disabled:opacity-50 disabled:cursor-not-allowed">
                        Continue to Passenger Details
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[name="seats[]"]:not(:disabled)');
    const maxSeats = {{ $passengerCount }};
    const continueBtn = document.getElementById('continue-btn');
    const selectedCount = document.getElementById('selected-seats-count');
    
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const checked = document.querySelectorAll('input[name="seats[]"]:checked');
            
            if (checked.length > maxSeats) {
                this.checked = false;
                return;
            }
            
            selectedCount.textContent = checked.length;
            continueBtn.disabled = checked.length !== maxSeats;
        });
    });
});
</script>
@endpush
@endsection