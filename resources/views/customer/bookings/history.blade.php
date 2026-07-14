@extends('layouts.customer')
@section('title', 'Booking History')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div><h2 class="text-xl font-semibold text-white tracking-tight">Booking History</h2><p class="text-zinc-500 text-sm mt-1">All your flight bookings</p></div>
        <a href="{{ route('homepage') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 text-black rounded-xl font-semibold text-sm hover:from-amber-600 hover:to-amber-700 transition shadow-lg shadow-amber-500/20"><i class="fas fa-plus"></i> New Booking</a>
    </div>
    @forelse($bookings as $booking)
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6 hover:border-amber-500/20 transition">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <span class="font-mono text-amber-500 font-bold bg-amber-500/5 px-3 py-1 rounded text-sm">{{ $booking->booking_code }}</span>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold @if($booking->status=='confirmed') bg-emerald-500/10 text-emerald-400 @elseif($booking->status=='pending') bg-yellow-500/10 text-yellow-400 @else bg-red-500/10 text-red-400 @endif">{{ ucfirst($booking->status) }}</span>
                <span class="text-xs text-zinc-500">{{ $booking->created_at->format('d M Y') }}</span>
            </div>
            <p class="text-amber-500 font-bold">Rp {{ number_format($booking->total_price,0,',','.') }}</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-500/10 rounded-full flex items-center justify-center"><span class="text-amber-500 font-bold">{{ substr($booking->flight->airline->name,0,2) }}</span></div>
            <div class="flex-1">
                <p class="text-white font-semibold">{{ $booking->flight->airline->name }} - {{ $booking->flight->flight_number }}</p>
                <p class="text-zinc-400 text-sm">{{ $booking->flight->departureAirport->city }} ({{ $booking->flight->departureAirport->iata_code }}) → {{ $booking->flight->arrivalAirport->city }} ({{ $booking->flight->arrivalAirport->iata_code }})</p>
                <p class="text-zinc-500 text-xs">{{ \Carbon\Carbon::parse($booking->flight->departure_time)->format('d M Y H:i') }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('customer.booking.show', $booking) }}" class="px-4 py-2 bg-zinc-800 text-zinc-300 rounded-lg text-sm hover:bg-zinc-700 transition">Detail</a>
                @if($booking->status === 'confirmed')
                <a href="{{ route('customer.eticket', $booking) }}" class="px-4 py-2 bg-amber-500/10 text-amber-400 rounded-lg text-sm hover:bg-amber-500/20 transition"><i class="fas fa-ticket-alt"></i> E-Ticket</a>
                @endif
                @if($booking->status === 'pending')
                <a href="{{ route('customer.payment.show', $booking) }}" class="px-4 py-2 bg-amber-500 text-black rounded-lg text-sm font-semibold hover:bg-amber-600 transition">Pay Now</a>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-16 bg-zinc-900/50 border border-zinc-800 rounded-xl">
        <i class="fas fa-inbox text-4xl text-zinc-700 mb-4 block"></i>
        <p class="text-zinc-500">No bookings yet</p>
        <a href="{{ route('homepage') }}" class="text-amber-500 hover:underline mt-2 inline-block">Search Flights</a>
    </div>
    @endforelse
    <div class="mt-6">{{ $bookings->links() }}</div>
</div>
@endsection