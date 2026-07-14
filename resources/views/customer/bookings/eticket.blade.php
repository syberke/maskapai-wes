@extends('layouts.customer')
@section('title', 'E-Ticket')
@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-white tracking-tight">E-Ticket</h2>
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 text-black rounded-xl font-semibold text-sm hover:from-amber-600 hover:to-amber-700 transition shadow-lg shadow-amber-500/20">
            <i class="fas fa-download"></i> Download PDF
        </button>
    </div>

    <div class="bg-white rounded-xl overflow-hidden">
        <div class="bg-gradient-to-r from-amber-500 to-amber-600 p-8">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="font-luxury text-2xl font-bold text-black tracking-wider">LUXURYFLY</h2>
                    <p class="text-black/70 text-sm mt-1">Premium Flight Ticket</p>
                </div>
                <div class="text-right">
                    <p class="text-black font-mono font-bold text-lg">{{ $booking->booking_code }}</p>
                    <p class="text-black/70 text-xs mt-1">Booking Code</p>
                </div>
            </div>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div><p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">Airline</p><p class="text-black font-semibold">{{ $booking->flight->airline->name }}</p><p class="text-zinc-400 text-sm">{{ $booking->flight->flight_number }}</p></div>
                <div><p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">Aircraft</p><p class="text-black font-semibold">{{ $booking->flight->airplane->model ?? '-' }}</p></div>
                <div><p class="text-zinc-500 text-xs uppercase tracking-wider mb-1">Date</p><p class="text-black font-semibold">{{ \Carbon\Carbon::parse($booking->flight->departure_time)->format('d F Y') }}</p><p class="text-zinc-400 text-sm">{{ \Carbon\Carbon::parse($booking->flight->departure_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->flight->arrival_time)->format('H:i') }}</p></div>
            </div>

            <div class="relative py-8 border-y-2 border-dashed border-zinc-200">
                <div class="flex items-center justify-between">
                    <div class="text-center"><p class="text-3xl font-bold text-black">{{ $booking->flight->departureAirport->iata_code }}</p><p class="text-zinc-500 text-sm">{{ $booking->flight->departureAirport->city }}</p><p class="text-zinc-400 text-xs">{{ $booking->flight->departureAirport->name }}</p></div>
                    <div class="flex-1 mx-8 text-center"><div class="text-zinc-400 text-sm mb-1">{{ \Carbon\Carbon::parse($booking->flight->departure_time)->diffInHours($booking->flight->arrival_time) }}h</div><div class="border-t-2 border-dashed border-amber-500 relative"><svg class="absolute -top-2.5 left-1/2 -translate-x-1/2 w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/></svg></div></div>
                    <div class="text-center"><p class="text-3xl font-bold text-black">{{ $booking->flight->arrivalAirport->iata_code }}</p><p class="text-zinc-500 text-sm">{{ $booking->flight->arrivalAirport->city }}</p><p class="text-zinc-400 text-xs">{{ $booking->flight->arrivalAirport->name }}</p></div>
                </div>
            </div>

            <div class="mt-8">
                <h3 class="text-black font-semibold mb-4">Passengers</h3>
                <table class="w-full"><thead><tr class="text-left text-xs uppercase text-zinc-500"><th class="pb-2">Name</th><th class="pb-2">Seat</th><th class="pb-2">Passport</th></tr></thead>
                <tbody class="divide-y divide-zinc-100">@foreach($booking->passengers as $p)<tr class="text-sm text-black"><td class="py-2">{{ $p->full_name }}</td><td class="font-mono">{{ optional($p->seat)->seat_number ?? '-' }}</td><td>{{ $p->passport_number ?? '-' }}</td></tr>@endforeach</tbody></table>
            </div>

            <div class="mt-8 pt-6 border-t border-zinc-100 flex justify-between items-center">
                <div><p class="text-zinc-500 text-xs">Total Paid</p><p class="text-xl font-bold text-amber-600">Rp {{ number_format($booking->total_price,0,',','.') }}</p></div>
                <div class="text-right"><p class="text-zinc-500 text-xs">Payment Status</p><p class="text-emerald-600 font-semibold">PAID</p></div>
            </div>

            <div class="mt-8 pt-6 border-t border-zinc-100 text-center">
                <div class="inline-block bg-zinc-100 px-8 py-4 rounded-lg">
                    <svg class="w-32 h-12 text-black" viewBox="0 0 100 30"><rect x="2" y="2" width="2" height="26"/><rect x="8" y="2" width="4" height="26"/><rect x="16" y="2" width="2" height="26"/><rect x="22" y="2" width="6" height="26"/><rect x="32" y="2" width="2" height="26"/><rect x="38" y="2" width="4" height="26"/><rect x="46" y="2" width="2" height="12"/><rect x="46" y="18" width="2" height="10"/><rect x="52" y="2" width="6" height="26"/><rect x="62" y="2" width="2" height="26"/><rect x="68" y="2" width="4" height="26"/><rect x="76" y="2" width="2" height="26"/><rect x="82" y="2" width="6" height="26"/><rect x="92" y="2" width="2" height="26"/></svg>
                </div>
                <p class="text-zinc-400 text-xs mt-2">{{ $booking->booking_code }}</p>
            </div>
        </div>
    </div>
    <p class="text-center text-zinc-500 text-xs">This E-Ticket is a valid booking confirmation. Please keep it safe.</p>
</div>
<style>
@media print { nav, header, footer, button { display: none !important; } body { background: white !important; } }
</style>
@endsection