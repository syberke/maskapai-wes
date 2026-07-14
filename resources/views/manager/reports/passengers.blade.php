@extends('layouts.manager')
@section('title', 'Passenger Report')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div><h2 class="text-xl font-semibold text-white tracking-tight">Passenger Report</h2><p class="text-zinc-500 text-sm mt-1">Passenger analytics and demographics</p></div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-2 bg-zinc-800 text-zinc-300 rounded-lg text-xs hover:bg-zinc-700 transition"><i class="fas fa-file-pdf"></i> Export PDF</button>
            <button class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-500/10 text-amber-400 rounded-lg text-xs hover:bg-amber-500/20 transition"><i class="fas fa-file-excel"></i> Export Excel</button>
        </div>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-4"><p class="text-zinc-400 text-xs">Total Passengers</p><p class="text-xl font-bold text-white mt-1">{{ $totalPassengers }}</p></div>
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-4"><p class="text-zinc-400 text-xs">Today</p><p class="text-xl font-bold text-amber-500 mt-1">{{ $passengersToday }}</p></div>
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-4"><p class="text-zinc-400 text-xs">Male</p><p class="text-xl font-bold text-blue-400 mt-1">{{ $male }}</p></div>
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-4"><p class="text-zinc-400 text-xs">Female</p><p class="text-xl font-bold text-pink-400 mt-1">{{ $female }}</p></div>
    </div>
</div>
@endsection