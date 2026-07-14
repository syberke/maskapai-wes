@extends('layouts.manager')
@section('title', 'Executive Dashboard')
@section('content')
<div class="space-y-8">
    <!-- Welcome -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Executive Dashboard</h2>
            <p class="text-zinc-500 mt-1">Business intelligence & performance overview</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs text-zinc-500 bg-zinc-800 px-3 py-1.5 rounded-lg">{{ now()->format('d F Y') }}</span>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="stat-card bg-gradient-to-br from-amber-500/10 to-amber-600/5 border border-amber-500/20 rounded-xl p-4">
            <p class="text-zinc-400 text-[10px] uppercase tracking-wider">Revenue Today</p>
            <p class="text-lg font-bold text-white mt-1">Rp {{ number_format($dailyRevenue,0,',','.') }}</p>
            <div class="mt-2 flex items-center gap-1 text-[10px] text-emerald-400"><i class="fas fa-arrow-up"></i><span>Daily</span></div>
        </div>
        <div class="stat-card bg-gradient-to-br from-blue-500/10 to-blue-600/5 border border-blue-500/20 rounded-xl p-4">
            <p class="text-zinc-400 text-[10px] uppercase tracking-wider">Revenue This Week</p>
            <p class="text-lg font-bold text-white mt-1">Rp {{ number_format($weeklyRevenue,0,',','.') }}</p>
            <div class="mt-2 flex items-center gap-1 text-[10px] text-blue-400"><i class="fas fa-calendar-week"></i><span>Weekly</span></div>
        </div>
        <div class="stat-card bg-gradient-to-br from-emerald-500/10 to-emerald-600/5 border border-emerald-500/20 rounded-xl p-4">
            <p class="text-zinc-400 text-[10px] uppercase tracking-wider">Revenue This Month</p>
            <p class="text-lg font-bold text-white mt-1">Rp {{ number_format($monthlyRevenue,0,',','.') }}</p>
            <div class="mt-2 flex items-center gap-1 text-[10px] text-emerald-400"><i class="fas fa-calendar-alt"></i><span>Monthly</span></div>
        </div>
        <div class="stat-card bg-gradient-to-br from-purple-500/10 to-purple-600/5 border border-purple-500/20 rounded-xl p-4">
            <p class="text-zinc-400 text-[10px] uppercase tracking-wider">Total Bookings</p>
            <p class="text-lg font-bold text-white mt-1">{{ $totalBookings }}</p>
            <div class="mt-2 flex items-center gap-1 text-[10px] text-zinc-400"><span>{{ $confirmedBookings }} confirmed</span></div>
        </div>
        <div class="stat-card bg-gradient-to-br from-cyan-500/10 to-cyan-600/5 border border-cyan-500/20 rounded-xl p-4">
            <p class="text-zinc-400 text-[10px] uppercase tracking-wider">Success Rate</p>
            <p class="text-lg font-bold text-emerald-400 mt-1">{{ $successRate }}%</p>
            <div class="mt-2 flex items-center gap-1 text-[10px] text-emerald-400"><i class="fas fa-check-circle"></i><span>Booking success</span></div>
        </div>
        <div class="stat-card bg-gradient-to-br from-rose-500/10 to-rose-600/5 border border-rose-500/20 rounded-xl p-4">
            <p class="text-zinc-400 text-[10px] uppercase tracking-wider">Cancellation Rate</p>
            <p class="text-lg font-bold text-rose-400 mt-1">{{ $cancellationRate }}%</p>
            <div class="mt-2 flex items-center gap-1 text-[10px] text-rose-400"><i class="fas fa-times-circle"></i><span>Cancelled</span></div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Revenue Chart -->
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div><h3 class="text-base font-semibold text-white">Monthly Revenue</h3><p class="text-xs text-zinc-500 mt-0.5">Year {{ date('Y') }}</p></div>
                <a href="{{ route('manager.reports.revenue') }}" class="text-xs text-amber-500 hover:text-amber-400 transition">Full Report &rarr;</a>
            </div>
            <div class="space-y-2.5">
                @php $maxRev = max($monthlyRevenueData->max() ?: 1, 1); @endphp
                @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $idx => $mn)
                    @php $rev = $monthlyRevenueData[$idx + 1] ?? 0; $pct = ($rev / $maxRev) * 100; @endphp
                    <div class="flex items-center gap-3 group">
                        <span class="text-xs text-zinc-500 w-8">{{ $mn }}</span>
                        <div class="flex-1 h-7 bg-zinc-800/50 rounded-lg overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-amber-500 to-amber-400 rounded-lg transition-all duration-500 group-hover:opacity-80" style="width: {{ max($pct, 2) }}%"></div>
                        </div>
                        <span class="text-xs text-zinc-400 w-24 text-right font-mono">Rp {{ number_format($rev,0,',','.') }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Booking Analytics -->
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div><h3 class="text-base font-semibold text-white">Booking Analytics</h3><p class="text-xs text-zinc-500 mt-0.5">Status distribution</p></div>
                <a href="{{ route('manager.reports.bookings') }}" class="text-xs text-amber-500 hover:text-amber-400 transition">Full Report &rarr;</a>
            </div>
            <div class="space-y-5">
                @php $total = max($totalBookings, 1); @endphp
                <div class="flex h-5 bg-zinc-800 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 transition-all" style="width: {{ ($confirmedBookings/$total)*100 }}%"></div>
                    <div class="bg-yellow-500 transition-all" style="width: {{ (($totalBookings-$confirmedBookings-$cancelledBookings)/$total)*100 }}%"></div>
                    <div class="bg-red-500 transition-all" style="width: {{ ($cancelledBookings/$total)*100 }}%"></div>
                </div>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="p-3 bg-zinc-800/30 rounded-lg"><p class="text-xl font-bold text-emerald-400">{{ $confirmedBookings }}</p><p class="text-[10px] text-zinc-500">Confirmed</p></div>
                    <div class="p-3 bg-zinc-800/30 rounded-lg"><p class="text-xl font-bold text-yellow-400">{{ $totalBookings - $confirmedBookings - $cancelledBookings }}</p><p class="text-[10px] text-zinc-500">Pending</p></div>
                    <div class="p-3 bg-zinc-800/30 rounded-lg"><p class="text-xl font-bold text-red-400">{{ $cancelledBookings }}</p><p class="text-[10px] text-zinc-500">Cancelled</p></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Occupancy & Top Routes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Occupancy per Airline -->
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div><h3 class="text-base font-semibold text-white">Occupancy Rate</h3><p class="text-xs text-zinc-500 mt-0.5">Per airline</p></div>
                <a href="{{ route('manager.reports.occupancy') }}" class="text-xs text-amber-500 hover:text-amber-400 transition">Full Report &rarr;</a>
            </div>
            <div class="space-y-4">
                @foreach($occupancyData as $od)
                <div>
                    <div class="flex justify-between text-sm mb-1"><span class="text-zinc-300">{{ $od['name'] }}</span><span class="text-white font-semibold">{{ $od['occupancy_rate'] }}%</span></div>
                    <div class="h-2.5 bg-zinc-800 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-amber-500 to-emerald-500 rounded-full transition-all" style="width: {{ $od['occupancy_rate'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Top Routes -->
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div><h3 class="text-base font-semibold text-white">Top Routes</h3><p class="text-xs text-zinc-500 mt-0.5">Most popular flight routes</p></div>
                <a href="{{ route('manager.reports.route-performance') }}" class="text-xs text-amber-500 hover:text-amber-400 transition">Full Report &rarr;</a>
            </div>
            <div class="space-y-3">
                @forelse($topRoutes as $route)
                <div class="flex items-center justify-between p-3 bg-zinc-800/30 rounded-lg hover:bg-zinc-800/50 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-amber-500/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-route text-amber-500 text-xs"></i>
                        </div>
                        <span class="text-white text-sm">Route {{ $route->route }}</span>
                    </div>
                    <span class="text-amber-500 font-semibold text-sm">{{ $route->total }} flights</span>
                </div>
                @empty
                <div class="text-center text-zinc-500 py-4">No route data available</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Report Links -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <a href="{{ route('manager.reports.passengers') }}" class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-5 hover:border-amber-500/30 transition group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-500/10 rounded-xl flex items-center justify-center group-hover:bg-purple-500/20 transition">
                    <i class="fas fa-users text-purple-500"></i>
                </div>
                <div><h3 class="text-white font-semibold">Passenger Report</h3><p class="text-zinc-500 text-xs mt-0.5">View passenger analytics</p></div>
                <i class="fas fa-arrow-right text-zinc-600 ml-auto group-hover:text-purple-500 transition"></i>
            </div>
        </a>
        <a href="{{ route('manager.reports.airline-performance') }}" class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-5 hover:border-amber-500/30 transition group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center group-hover:bg-blue-500/20 transition">
                    <i class="fas fa-building text-blue-500"></i>
                </div>
                <div><h3 class="text-white font-semibold">Airline Performance</h3><p class="text-zinc-500 text-xs mt-0.5">Compare airline metrics</p></div>
                <i class="fas fa-arrow-right text-zinc-600 ml-auto group-hover:text-blue-500 transition"></i>
            </div>
        </a>
        <a href="{{ route('manager.reports.route-performance') }}" class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-5 hover:border-amber-500/30 transition group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center group-hover:bg-emerald-500/20 transition">
                    <i class="fas fa-route text-emerald-500"></i>
                </div>
                <div><h3 class="text-white font-semibold">Route Performance</h3><p class="text-zinc-500 text-xs mt-0.5">Analyze route profitability</p></div>
                <i class="fas fa-arrow-right text-zinc-600 ml-auto group-hover:text-emerald-500 transition"></i>
            </div>
        </a>
    </div>
</div>
@endsection