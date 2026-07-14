@extends('layouts.staff')
@section('title', 'Passenger Manifest - ' . $flight->flight_number)
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('staff.manifest') }}" class="text-amber-500 hover:text-amber-400 text-sm inline-flex items-center gap-1.5 transition">
                <i class="fas fa-arrow-left"></i>
                Back to Manifest List
            </a>
            <h2 class="text-xl font-semibold text-white tracking-tight mt-2">{{ $flight->airline->name }} - {{ $flight->flight_number }}</h2>
            <p class="text-zinc-500 text-sm mt-1">{{ $flight->departureAirport->city }} ({{ $flight->departureAirport->iata_code }}) → {{ $flight->arrivalAirport->city }} ({{ $flight->arrivalAirport->iata_code }})</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs text-zinc-500 bg-zinc-800 px-3 py-1.5 rounded-lg">{{ $passengers->count() }} total passengers</span>
        </div>
    </div>

    <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl overflow-hidden">
        <div class="p-4 border-b border-zinc-800 flex items-center justify-between">
            <div class="relative w-64">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-sm"></i>
                <input type="text" placeholder="Search passengers..." class="bg-zinc-800 border border-zinc-700 rounded-lg pl-9 pr-4 py-2 text-sm text-white placeholder-zinc-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 w-full">
            </div>
            <div class="flex gap-2">
                <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-2 bg-zinc-800 text-zinc-300 rounded-lg text-xs hover:bg-zinc-700 transition">
                    <i class="fas fa-print"></i>
                    Print
                </button>
                <button class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-500/10 text-amber-400 rounded-lg text-xs hover:bg-amber-500/20 transition">
                    <i class="fas fa-file-pdf"></i>
                    Export PDF
                </button>
            </div>
        </div>
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-zinc-500 bg-zinc-800/30">
                    <th class="px-6 py-4 font-semibold">Passenger Name</th>
                    <th class="px-6 py-4 font-semibold">Seat</th>
                    <th class="px-6 py-4 font-semibold">Gender</th>
                    <th class="px-6 py-4 font-semibold">Passport</th>
                    <th class="px-6 py-4 font-semibold">Booking Code</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @forelse($passengers as $p)
                <tr class="text-sm text-zinc-300 hover:bg-zinc-800/20 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-zinc-800 rounded-full flex items-center justify-center">
                                <span class="text-xs text-zinc-400 font-medium">{{ substr($p->full_name, 0, 1) }}</span>
                            </div>
                            <span class="text-white font-medium">{{ $p->full_name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4"><span class="font-mono text-amber-500 bg-amber-500/5 px-2 py-1 rounded text-xs">{{ $p->seat_number }}</span></td>
                    <td class="px-6 py-4">{{ $p->gender == 'L' ? 'Male' : 'Female' }}</td>
                    <td class="px-6 py-4 font-mono text-xs">{{ $p->passport_number ?? '-' }}</td>
                    <td class="px-6 py-4 font-mono text-xs text-amber-500/70">{{ $p->booking->booking_code ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-zinc-500"><i class="fas fa-user-slash text-2xl mb-2 block text-zinc-700"></i>No passengers on this flight</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection