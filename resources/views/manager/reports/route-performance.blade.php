@extends('layouts.manager')
@section('title', 'Route Performance')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div><h2 class="text-xl font-semibold text-white tracking-tight">Route Performance</h2><p class="text-zinc-500 text-sm mt-1">Analyze route profitability and popularity</p></div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-2 bg-zinc-800 rounded-lg text-xs hover:bg-zinc-700 transition"><i class="fas fa-file-pdf"></i> Export PDF</button>
            <button class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-500/10 text-amber-400 rounded-lg text-xs hover:bg-amber-500/20 transition"><i class="fas fa-file-excel"></i> Export Excel</button>
        </div>
    </div>
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl overflow-hidden">
        <table class="w-full">
            <thead><tr class="text-left text-xs uppercase tracking-wider text-zinc-500 bg-zinc-800/30"><th class="px-6 py-4 font-semibold">Route</th><th class="px-6 py-4 font-semibold">Total Flights</th><th class="px-6 py-4 font-semibold">Total Bookings</th><th class="px-6 py-4 font-semibold">Revenue</th></tr></thead>
            <tbody class="divide-y divide-zinc-800">
                @forelse($routes as $r)
                <tr class="text-sm text-zinc-300">
                    <td class="px-6 py-4"><div class="flex items-center gap-3"><div class="w-8 h-8 bg-amber-500/10 rounded-lg flex items-center justify-center"><i class="fas fa-route text-amber-500 text-xs"></i></div><span class="text-white font-medium">{{ $r->route_name }}</span></div></td>
                    <td class="px-6 py-4">{{ $r->total_flights }}</td>
                    <td class="px-6 py-4">{{ $r->total_bookings }}</td>
                    <td class="px-6 py-4 font-mono text-emerald-400">Rp {{ number_format($r->revenue,0,',','.') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-zinc-500">No route data available</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection