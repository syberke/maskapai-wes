@extends('layouts.staff')
@section('title', 'Staff Dashboard')
@section('content')
<div class="space-y-8">
    <!-- Welcome -->
    <div>
        <h2 class="text-2xl font-bold text-white tracking-tight">Operational Dashboard</h2>
        <p class="text-zinc-500 mt-1">Monitor today's flight operations at a glance.</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="stat-card bg-gradient-to-br from-amber-500/10 to-amber-600/5 border border-amber-500/20 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-zinc-400 text-xs uppercase tracking-wider">Flights Today</span>
                <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-plane text-amber-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['flights_today'] }}</p>
        </div>
        <div class="stat-card bg-gradient-to-br from-blue-500/10 to-blue-600/5 border border-blue-500/20 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-zinc-400 text-xs uppercase tracking-wider">Active Flights</span>
                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-plane-up text-blue-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['active_flights'] }}</p>
        </div>
        <div class="stat-card bg-gradient-to-br from-emerald-500/10 to-emerald-600/5 border border-emerald-500/20 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-zinc-400 text-xs uppercase tracking-wider">Passengers Today</span>
                <div class="w-10 h-10 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-emerald-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['total_passengers_today'] }}</p>
        </div>
        <div class="stat-card bg-gradient-to-br from-purple-500/10 to-purple-600/5 border border-purple-500/20 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-zinc-400 text-xs uppercase tracking-wider">Boarding</span>
                <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-double text-purple-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['boarding_count'] }}</p>
        </div>
    </div>

    <!-- Upcoming Flights -->
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl overflow-hidden">
        <div class="p-6 border-b border-zinc-800">
            <h3 class="text-base font-semibold text-white">Upcoming Departures</h3>
            <p class="text-xs text-zinc-500 mt-1">Next 5 scheduled flights</p>
        </div>
        <div class="divide-y divide-zinc-800">
            @forelse($upcoming_flights as $f)
            <div class="p-5 flex items-center justify-between hover:bg-zinc-800/20 transition">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-amber-500/10 rounded-full flex items-center justify-center">
                        <span class="text-amber-500 font-bold text-xs">{{ substr($f->airline->name, 0, 2) }}</span>
                    </div>
                    <div>
                        <p class="text-white font-semibold text-sm">{{ $f->airline->name }} <span class="text-amber-500 font-mono text-xs">- {{ $f->flight_number }}</span></p>
                        <p class="text-zinc-500 text-xs mt-0.5">{{ $f->departureAirport->city }} ({{ $f->departureAirport->iata_code }}) → {{ $f->arrivalAirport->city }} ({{ $f->arrivalAirport->iata_code }})</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-white font-mono text-sm">{{ \Carbon\Carbon::parse($f->departure_time)->format('H:i') }}</p>
                    <p class="text-zinc-500 text-xs">{{ \Carbon\Carbon::parse($f->departure_time)->format('d M') }}</p>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-zinc-500">
                <i class="fas fa-calendar-times text-2xl mb-2 block text-zinc-700"></i>
                No upcoming flights scheduled
            </div>
            @endforelse
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <a href="{{ route('staff.flights') }}" class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6 hover:border-amber-500/30 transition group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-500/10 rounded-xl flex items-center justify-center group-hover:bg-amber-500/20 transition">
                    <i class="fas fa-plane text-amber-500"></i>
                </div>
                <div>
                    <h3 class="text-white font-semibold">Flight Monitoring</h3>
                    <p class="text-zinc-500 text-sm">View all flight schedules and status</p>
                </div>
                <i class="fas fa-arrow-right text-zinc-600 ml-auto group-hover:text-amber-500 transition"></i>
            </div>
        </a>
        <a href="{{ route('staff.manifest') }}" class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6 hover:border-amber-500/30 transition group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center group-hover:bg-emerald-500/20 transition">
                    <i class="fas fa-clipboard-list text-emerald-500"></i>
                </div>
                <div>
                    <h3 class="text-white font-semibold">Passenger Manifest</h3>
                    <p class="text-zinc-500 text-sm">Check passenger lists per flight</p>
                </div>
                <i class="fas fa-arrow-right text-zinc-600 ml-auto group-hover:text-emerald-500 transition"></i>
            </div>
        </a>
    </div>
</div>
@endsection