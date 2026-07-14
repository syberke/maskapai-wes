@extends('layouts.customer')
@section('title', 'Booking Detail')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-white tracking-tight">Booking Detail</h2>
            <p class="text-zinc-500 text-sm mt-1">Code: <span class="text-amber-500 font-mono">{{ $booking->booking_code }}</span></p>
        </div>
        @if($booking->status === 'confirmed')
            <a href="{{ route('customer.eticket', $booking) }}" class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 text-black rounded-xl font-semibold text-sm hover:from-amber-600 hover:to-amber-700 transition shadow-lg shadow-amber-500/20"><i class="fas fa-ticket-alt mr-2"></i>E-Ticket</a>
        @elseif($booking->status === 'pending')
            <a href="{{ route('customer.payment.show', $booking) }}" class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 text-black rounded-xl font-semibold text-sm hover:from-amber-600 hover:to-amber-700 transition shadow-lg shadow-amber-500/20">Pay Now</a>
        @endif
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
            <h3 class="text-base font-semibold text-white mb-4">Flight Information</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-zinc-400">Airline</span><span class="text-white">{{ $booking->flight->airline->name }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Flight No</span><span class="text-amber-500 font-mono">{{ $booking->flight->flight_number }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Aircraft</span><span class="text-white">{{ $booking->flight->airplane->model }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">From</span><span class="text-white">{{ $booking->flight->departureAirport->city }} ({{ $booking->flight->departureAirport->iata_code }})</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">To</span><span class="text-white">{{ $booking->flight->arrivalAirport->city }} ({{ $booking->flight->arrivalAirport->iata_code }})</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Departure</span><span class="text-white">{{ \Carbon\Carbon::parse($booking->flight->departure_time)->format('d M Y H:i') }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Arrival</span><span class="text-white">{{ \Carbon\Carbon::parse($booking->flight->arrival_time)->format('d M Y H:i') }}</span></div>
            </div>
        </div>
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
            <h3 class="text-base font-semibold text-white mb-4">Payment Info</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-zinc-400">Passengers</span><span class="text-white">{{ $booking->total_passengers }} person(s)</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Total Price</span><span class="text-2xl font-bold text-amber-500">Rp {{ number_format($booking->total_price,0,',','.') }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Status</span><span class="px-2.5 py-1 rounded-full text-xs font-semibold @if($booking->status=='confirmed') bg-emerald-500/10 text-emerald-400 @elseif($booking->status=='pending') bg-yellow-500/10 text-yellow-400 @else bg-red-500/10 text-red-400 @endif">{{ ucfirst($booking->status) }}</span></div>
                @if($booking->payment)
                <div class="flex justify-between"><span class="text-zinc-400">Payment</span><span class="px-2.5 py-1 rounded-full text-xs font-semibold @if($booking->payment->payment_status=='paid') bg-emerald-500/10 text-emerald-400 @else bg-yellow-500/10 text-yellow-400 @endif">{{ ucfirst($booking->payment->payment_status) }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Method</span><span class="text-white">{{ $booking->payment->payment_method ?? '-' }}</span></div>
                @endif
            </div>
        </div>
    </div>
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
        <h3 class="text-base font-semibold text-white mb-4">Passenger List</h3>
        <table class="w-full">
            <thead><tr class="text-left text-xs uppercase text-zinc-500"><th class="pb-3 font-semibold">Name</th><th class="pb-3 font-semibold">Gender</th><th class="pb-3 font-semibold">Seat</th></tr></thead>
            <tbody class="divide-y divide-zinc-800">
                @foreach($booking->passengers as $p)
                <tr class="text-sm text-zinc-300"><td class="py-3">{{ $p->full_name }}</td><td>{{ $p->gender == 'L' ? 'Male' : 'Female' }}</td><td class="font-mono text-amber-500">{{ $p->seat_number }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection