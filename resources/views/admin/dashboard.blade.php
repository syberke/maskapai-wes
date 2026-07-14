@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-8">
    <!-- Welcome Section -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Welcome back, {{ auth()->user()->name }}!</h2>
            <p class="text-zinc-500 mt-1">Here's what's happening with LuxuryFly today.</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-zinc-400">{{ now()->format('l, d F Y') }}</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="stat-card bg-gradient-to-br from-amber-500/10 to-amber-600/5 border border-amber-500/20 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-zinc-400 text-xs uppercase tracking-wider">Total Flights</span>
                <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-plane text-amber-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['total_flights'] }}</p>
            <div class="mt-2 flex items-center gap-1 text-xs text-emerald-400">
                <i class="fas fa-arrow-up"></i>
                <span>Active routes</span>
            </div>
        </div>

        <div class="stat-card bg-gradient-to-br from-blue-500/10 to-blue-600/5 border border-blue-500/20 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-zinc-400 text-xs uppercase tracking-wider">Total Bookings</span>
                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-ticket-alt text-blue-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['total_bookings'] }}</p>
            <div class="mt-2 flex items-center gap-1 text-xs text-zinc-500">
                <span>{{ $stats['confirmed_bookings'] }} confirmed</span>
            </div>
        </div>

        <div class="stat-card bg-gradient-to-br from-emerald-500/10 to-emerald-600/5 border border-emerald-500/20 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-zinc-400 text-xs uppercase tracking-wider">Revenue</span>
                <div class="w-10 h-10 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-emerald-500 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-white">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
            <div class="mt-2 flex items-center gap-1 text-xs text-emerald-400">
                <i class="fas fa-arrow-up"></i>
                <span>Total earnings</span>
            </div>
        </div>

        <div class="stat-card bg-gradient-to-br from-purple-500/10 to-purple-600/5 border border-purple-500/20 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-zinc-400 text-xs uppercase tracking-wider">Customers</span>
                <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-purple-500 text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['total_users'] }}</p>
            <div class="mt-2 flex items-center gap-1 text-xs text-zinc-500">
                <span>Registered users</span>
            </div>
        </div>
    </div>

    <!-- Secondary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <span class="text-zinc-400 text-xs uppercase tracking-wider">Airlines</span>
                <i class="fas fa-building text-zinc-600"></i>
            </div>
            <p class="text-xl font-bold text-white mt-1">{{ $stats['total_airlines'] }}</p>
        </div>
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <span class="text-zinc-400 text-xs uppercase tracking-wider">Airports</span>
                <i class="fas fa-map-marker-alt text-zinc-600"></i>
            </div>
            <p class="text-xl font-bold text-white mt-1">{{ $stats['total_airports'] }}</p>
        </div>
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <span class="text-zinc-400 text-xs uppercase tracking-wider">Pending Payments</span>
                <i class="fas fa-clock text-yellow-600"></i>
            </div>
            <p class="text-xl font-bold text-yellow-400 mt-1">{{ $stats['pending_bookings'] }}</p>
        </div>
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <span class="text-zinc-400 text-xs uppercase tracking-wider">Confirmed</span>
                <i class="fas fa-check-circle text-emerald-600"></i>
            </div>
            <p class="text-xl font-bold text-emerald-400 mt-1">{{ $stats['confirmed_bookings'] }}</p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Revenue Chart -->
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base font-semibold text-white">Monthly Revenue</h3>
                <span class="text-xs text-zinc-500">This Year</span>
            </div>
            <div class="space-y-3">
                @php $maxRev = max($monthly_revenue->max() ?: 1, 1); @endphp
                @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $idx => $monthName)
                    @php
                        $rev = $monthly_revenue[$idx + 1] ?? 0;
                        $pct = ($rev / $maxRev) * 100;
                    @endphp
                    <div class="flex items-center gap-3 group">
                        <span class="text-xs text-zinc-500 w-8">{{ $monthName }}</span>
                        <div class="flex-1 h-6 bg-zinc-800/50 rounded-lg overflow-hidden relative">
                            <div class="h-full bg-gradient-to-r from-amber-500 to-amber-400 rounded-lg transition-all duration-500 group-hover:opacity-80" style="width: {{ max($pct, 2) }}%"></div>
                        </div>
                        <span class="text-xs text-zinc-400 w-24 text-right font-mono">Rp {{ number_format($rev, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Status Booking Overview -->
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base font-semibold text-white">Booking Status Overview</h3>
                <span class="text-xs text-zinc-500">Distribution</span>
            </div>
            <div class="space-y-5">
                @php
                    $total = max($stats['pending_bookings'] + $stats['confirmed_bookings'], 1);
                    $pendingPct = ($stats['pending_bookings'] / $total) * 100;
                    $confirmedPct = ($stats['confirmed_bookings'] / $total) * 100;
                @endphp
                <div class="relative pt-1">
                    <div class="flex h-4 bg-zinc-800 rounded-full overflow-hidden">
                        <div class="bg-amber-500 transition-all duration-500" style="width: {{ $pendingPct }}%"></div>
                        <div class="bg-emerald-500 transition-all duration-500" style="width: {{ $confirmedPct }}%"></div>
                    </div>
                    <div class="flex justify-between mt-3 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-amber-500 rounded-full"></div>
                            <span class="text-zinc-400">Pending</span>
                            <span class="text-white font-semibold">{{ $stats['pending_bookings'] }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                            <span class="text-zinc-400">Confirmed</span>
                            <span class="text-white font-semibold">{{ $stats['confirmed_bookings'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-zinc-800 pt-5 grid grid-cols-2 gap-4">
                    <div class="text-center p-4 bg-zinc-800/30 rounded-lg">
                        <p class="text-2xl font-bold text-white">{{ $stats['total_airports'] }}</p>
                        <p class="text-xs text-zinc-500 mt-1">Total Airports</p>
                    </div>
                    <div class="text-center p-4 bg-zinc-800/30 rounded-lg">
                        <p class="text-2xl font-bold text-white">{{ $stats['total_airlines'] }}</p>
                        <p class="text-xs text-zinc-500 mt-1">Total Airlines</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings Table -->
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl overflow-hidden">
        <div class="p-6 border-b border-zinc-800 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-white">Recent Bookings</h3>
                <p class="text-xs text-zinc-500 mt-1">Latest booking transactions</p>
            </div>
            <a href="#" class="text-xs text-amber-500 hover:text-amber-400 transition">View All &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-zinc-500 bg-zinc-800/30">
                        <th class="px-6 py-4 font-semibold">Booking Code</th>
                        <th class="px-6 py-4 font-semibold">Customer</th>
                        <th class="px-6 py-4 font-semibold">Route</th>
                        <th class="px-6 py-4 font-semibold">Amount</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @forelse($recent_bookings as $booking)
                    <tr class="text-sm text-zinc-300 hover:bg-zinc-800/20 transition">
                        <td class="px-6 py-4">
                            <span class="font-mono text-amber-500 font-bold text-xs bg-amber-500/5 px-2 py-1 rounded">{{ $booking->booking_code }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-zinc-800 rounded-full flex items-center justify-center">
                                    <span class="text-xs text-zinc-400 font-medium">{{ substr($booking->user->name, 0, 1) }}</span>
                                </div>
                                <span>{{ $booking->user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-mono bg-zinc-800/50 px-2 py-1 rounded">{{ $booking->flight->departureAirport->iata_code }} → {{ $booking->flight->arrivalAirport->iata_code }}</span>
                        </td>
                        <td class="px-6 py-4 font-mono text-sm">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold
                                @if($booking->status === 'confirmed') bg-emerald-500/10 text-emerald-400
                                @elseif($booking->status === 'pending') bg-yellow-500/10 text-yellow-400
                                @elseif($booking->status === 'cancelled') bg-red-500/10 text-red-400
                                @else bg-blue-500/10 text-blue-400
                                @endif">
                                <i class="fas fa-circle text-[6px] {{ $booking->status === 'confirmed' ? 'text-emerald-400' : ($booking->status === 'pending' ? 'text-yellow-400' : 'text-red-400') }}"></i>
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-zinc-500">{{ $booking->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i class="fas fa-inbox text-3xl text-zinc-700 mb-3 block"></i>
                            <p class="text-zinc-500">No bookings yet</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection