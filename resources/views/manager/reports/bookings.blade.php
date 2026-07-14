@extends('layouts.manager')
@section('title', 'Booking Report')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div><h2 class="text-xl font-semibold text-white tracking-tight">Booking Report</h2><p class="text-zinc-500 text-sm mt-1">Booking analytics and trends</p></div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-2 bg-zinc-800 text-zinc-300 rounded-lg text-xs hover:bg-zinc-700 transition"><i class="fas fa-file-pdf"></i> Export PDF</button>
            <button class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-500/10 text-amber-400 rounded-lg text-xs hover:bg-amber-500/20 transition"><i class="fas fa-file-excel"></i> Export Excel</button>
        </div>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-4"><p class="text-zinc-400 text-xs">Total Bookings</p><p class="text-xl font-bold text-white mt-1">{{ $total }}</p></div>
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-4"><p class="text-zinc-400 text-xs">Confirmed</p><p class="text-xl font-bold text-emerald-400 mt-1">{{ $confirmed }}</p></div>
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-4"><p class="text-zinc-400 text-xs">Pending</p><p class="text-xl font-bold text-yellow-400 mt-1">{{ $pending }}</p></div>
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-4"><p class="text-zinc-400 text-xs">Cancelled</p><p class="text-xl font-bold text-red-400 mt-1">{{ $cancelled }}</p></div>
    </div>
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
        <h3 class="text-base font-semibold text-white mb-4">Monthly Booking Trend</h3>
        <div class="space-y-2.5">@php $max = max($monthlyBookings->max()?:1,1); @endphp @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $i=>$mn) @php $c=$monthlyBookings[$i+1]??0; $p=($c/$max)*100; @endphp <div class="flex items-center gap-3"><span class="text-xs text-zinc-500 w-8">{{ $mn }}</span><div class="flex-1 h-7 bg-zinc-800/50 rounded-lg overflow-hidden"><div class="h-full bg-gradient-to-r from-blue-500 to-blue-400 rounded-lg" style="width:{{ max($p,2) }}%"></div></div><span class="text-xs text-zinc-400 w-16 text-right font-mono">{{ $c }}</span></div>@endforeach</div>
    </div>
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl overflow-hidden">
        <div class="p-4 border-b border-zinc-800"><h3 class="text-sm font-semibold text-white">Recent Bookings</h3></div>
        <table class="w-full"><thead><tr class="text-left text-xs uppercase tracking-wider text-zinc-500 bg-zinc-800/30"><th class="px-6 py-4 font-semibold">Code</th><th class="px-6 py-4 font-semibold">Customer</th><th class="px-6 py-4 font-semibold">Route</th><th class="px-6 py-4 font-semibold">Amount</th><th class="px-6 py-4 font-semibold">Status</th><th class="px-6 py-4 font-semibold">Date</th></tr></thead>
        <tbody class="divide-y divide-zinc-800">@forelse($recentBookings as $b)<tr class="text-sm text-zinc-300"><td class="px-6 py-4 font-mono text-amber-500 text-xs">{{ $b->booking_code }}</td><td class="px-6 py-4">{{ $b->user->name }}</td><td class="px-6 py-4 text-xs">{{ $b->flight->departureAirport->iata_code }} → {{ $b->flight->arrivalAirport->iata_code }}</td><td class="px-6 py-4 font-mono">Rp {{ number_format($b->total_price,0,',','.') }}</td><td class="px-6 py-4"><span class="px-2 py-0.5 rounded text-xs font-semibold @if($b->status=='confirmed') bg-emerald-500/10 text-emerald-400 @elseif($b->status=='pending') bg-yellow-500/10 text-yellow-400 @else bg-red-500/10 text-red-400 @endif">{{ ucfirst($b->status) }}</span></td><td class="px-6 py-4 text-xs text-zinc-500">{{ $b->created_at->format('d M Y') }}</td></tr>@empty <tr><td colspan="6" class="px-6 py-8 text-center text-zinc-500">No bookings</td></tr>@endforelse</tbody></table>
    </div>
</div>
@endsection