@extends('layouts.manager')
@section('title', 'Occupancy Report')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div><h2 class="text-xl font-semibold text-white tracking-tight">Occupancy Report</h2><p class="text-zinc-500 text-sm mt-1">Seat occupancy rates per airline</p></div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-2 bg-zinc-800 text-zinc-300 rounded-lg text-xs hover:bg-zinc-700 transition"><i class="fas fa-file-pdf"></i> Export PDF</button>
            <button class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-500/10 text-amber-400 rounded-lg text-xs hover:bg-amber-500/20 transition"><i class="fas fa-file-excel"></i> Export Excel</button>
        </div>
    </div>
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl overflow-hidden">
        <table class="w-full">
            <thead><tr class="text-left text-xs uppercase tracking-wider text-zinc-500 bg-zinc-800/30"><th class="px-6 py-4 font-semibold">Airline</th><th class="px-6 py-4 font-semibold">Total Flights</th><th class="px-6 py-4 font-semibold">Occupancy Rate</th><th class="px-6 py-4 font-semibold">Revenue</th></tr></thead>
            <tbody class="divide-y divide-zinc-800">
                @foreach($occupancyData as $od)
                <tr class="text-sm text-zinc-300">
                    <td class="px-6 py-4 font-medium text-white">{{ $od['name'] }}</td>
                    <td class="px-6 py-4">{{ $od['total_flights'] }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-32 h-2.5 bg-zinc-800 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-amber-500 to-emerald-500 rounded-full" style="width: {{ $od['occupancy_rate'] }}%"></div>
                            </div>
                            <span class="text-white font-semibold text-sm">{{ $od['occupancy_rate'] }}%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-mono">Rp {{ number_format($od['revenue'],0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection