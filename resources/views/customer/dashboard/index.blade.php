@extends('layouts.customer')
@section('title', 'My Dashboard')
@section('content')
<div class="space-y-8">
    <!-- Welcome -->
    <div class="bg-gradient-to-r from-amber-500/10 to-amber-600/5 border border-amber-500/20 rounded-xl p-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Welcome back, {{ auth()->user()->name }}!</h2>
                <p class="text-zinc-400 mt-1">Your next journey awaits. Explore our premium destinations.</p>
            </div>
            <a href="{{ route('homepage') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 text-black rounded-xl font-semibold text-sm hover:from-amber-600 hover:to-amber-700 transition-all shadow-lg shadow-amber-500/20">
                <i class="fas fa-search"></i>
                <span>Search Flights</span>
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="stat-card bg-zinc-900/80 border border-zinc-800 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-zinc-400 text-xs uppercase tracking-wider">Total Bookings</span>
                <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center"><i class="fas fa-ticket-alt text-amber-500 text-sm"></i></div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="stat-card bg-zinc-900/80 border border-zinc-800 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-zinc-400 text-xs uppercase tracking-wider">Confirmed</span>
                <div class="w-10 h-10 bg-emerald-500/20 rounded-lg flex items-center justify-center"><i class="fas fa-check-circle text-emerald-500 text-sm"></i></div>
            </div>
            <p class="text-3xl font-bold text-emerald-400">{{ $stats['confirmed'] }}</p>
        </div>
        <div class="stat-card bg-zinc-900/80 border border-zinc-800 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-zinc-400 text-xs uppercase tracking-wider">Pending</span>
                <div class="w-10 h-10 bg-yellow-500/20 rounded-lg flex items-center justify-center"><i class="fas fa-clock text-yellow-500 text-sm"></i></div>
            </div>
            <p class="text-3xl font-bold text-yellow-400">{{ $stats['pending'] }}</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <a href="{{ route('customer.payments.index') }}" class="group bg-gradient-to-br from-zinc-900 to-zinc-800 border border-zinc-700 rounded-xl p-6 hover:border-amber-500/30 transition-all duration-300 hover:shadow-lg hover:shadow-amber-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-zinc-400 text-sm mb-2">Manage Payments</p>
                    <p class="text-xl font-bold text-white group-hover:text-amber-500 transition">View All Payments</p>
                    <p class="text-xs text-zinc-500 mt-1">Track and pay for your bookings</p>
                </div>
                <div class="w-14 h-14 bg-amber-500/10 rounded-xl flex items-center justify-center group-hover:bg-amber-500/20 transition">
                    <i class="fas fa-credit-card text-2xl text-amber-500"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('homepage') }}" class="group bg-gradient-to-br from-zinc-900 to-zinc-800 border border-zinc-700 rounded-xl p-6 hover:border-emerald-500/30 transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-zinc-400 text-sm mb-2">Book New Flight</p>
                    <p class="text-xl font-bold text-white group-hover:text-emerald-500 transition">Search Flights</p>
                    <p class="text-xs text-zinc-500 mt-1">Find your next destination</p>
                </div>
                <div class="w-14 h-14 bg-emerald-500/10 rounded-xl flex items-center justify-center group-hover:bg-emerald-500/20 transition">
                    <i class="fas fa-plane text-2xl text-emerald-500"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Upcoming Flights -->
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl overflow-hidden">
        <div class="p-6 border-b border-zinc-800">
            <h3 class="text-base font-semibold text-white">Upcoming Trips</h3>
            <p class="text-xs text-zinc-500 mt-1">Your scheduled flights</p>
        </div>
        <div class="divide-y divide-zinc-800">
            @forelse($upcomingBookings as $b)
            <div class="p-5 flex items-center justify-between hover:bg-zinc-800/20 transition">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-500/10 rounded-full flex items-center justify-center">
                        <span class="text-amber-500 font-bold text-sm">{{ substr($b->flight->airline->name,0,2) }}</span>
                    </div>
                    <div>
                        <p class="text-white font-semibold">{{ $b->flight->airline->name }} <span class="text-amber-500 text-xs font-mono">- {{ $b->flight->flight_number }}</span></p>
                        <p class="text-zinc-400 text-sm">{{ $b->flight->departureAirport->city }} ({{ $b->flight->departureAirport->iata_code }}) → {{ $b->flight->arrivalAirport->city }} ({{ $b->flight->arrivalAirport->iata_code }})</p>
                    </div>
                </div>
                <div class="text-right flex items-center gap-4">
                    <div>
                        <p class="text-white font-semibold text-sm">{{ \Carbon\Carbon::parse($b->flight->departure_time)->format('d M Y') }}</p>
                        <p class="text-xs text-zinc-500">{{ \Carbon\Carbon::parse($b->flight->departure_time)->format('H:i') }}</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold @if($b->status=='confirmed') bg-emerald-500/10 text-emerald-400 @else bg-yellow-500/10 text-yellow-400 @endif">{{ ucfirst($b->status) }}</span>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-zinc-500"><i class="fas fa-calendar-times text-2xl mb-2 block text-zinc-700"></i>No upcoming trips. <a href="{{ route('homepage') }}" class="text-amber-500 hover:underline">Book a flight now</a></div>
            @endforelse
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl overflow-hidden">
        <div class="p-6 border-b border-zinc-800 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-white">Recent Bookings</h3>
                <p class="text-xs text-zinc-500 mt-1">Your latest transactions</p>
            </div>
            <a href="{{ route('customer.bookings.history') }}" class="text-xs text-amber-500 hover:text-amber-400 transition">View All &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="text-left text-xs uppercase tracking-wider text-zinc-500 bg-zinc-800/30"><th class="px-6 py-4 font-semibold">Booking Code</th><th class="px-6 py-4 font-semibold">Route</th><th class="px-6 py-4 font-semibold">Date</th><th class="px-6 py-4 font-semibold">Amount</th><th class="px-6 py-4 font-semibold">Status</th><th class="px-6 py-4 font-semibold"></th></tr></thead>
                <tbody class="divide-y divide-zinc-800">
                    @forelse($recentBookings as $b)
                    <tr class="text-sm text-zinc-300">
                        <td class="px-6 py-4 font-mono text-amber-500 text-xs">{{ $b->booking_code }}</td>
                        <td class="px-6 py-4 text-xs">{{ $b->flight->departureAirport->iata_code }} → {{ $b->flight->arrivalAirport->iata_code }}</td>
                        <td class="px-6 py-4 text-xs">{{ $b->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 font-mono">Rp {{ number_format($b->total_price,0,',','.') }}</td>
                        <td class="px-6 py-4"><span class="px-2 py-0.5 rounded text-xs font-semibold @if($b->status=='confirmed') bg-emerald-500/10 text-emerald-400 @elseif($b->status=='pending') bg-yellow-500/10 text-yellow-400 @else bg-red-500/10 text-red-400 @endif">{{ ucfirst($b->status) }}</span></td>
                        <td class="px-6 py-4"><a href="{{ route('customer.booking.show', $b) }}" class="text-amber-500 hover:text-amber-400 text-xs">Detail</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-zinc-500">No bookings yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection