@extends('layouts.customer')
@section('title', 'Payment Success')
@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Success Header -->
    <div class="bg-gradient-to-r from-emerald-500/10 to-emerald-600/5 border border-emerald-500/20 rounded-xl p-8 text-center">
        <div class="w-20 h-20 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check-circle text-5xl text-emerald-400"></i>
        </div>
        <h2 class="text-2xl font-bold text-white mb-2">Payment Successful!</h2>
        <p class="text-zinc-400 mb-2">Your payment has been processed successfully.</p>
        <p class="text-zinc-500 text-sm">Booking Code: <span class="text-amber-500 font-mono font-semibold">{{ $booking->booking_code }}</span></p>
    </div>

    <!-- Payment Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
            <h3 class="text-base font-semibold text-white mb-4">Flight Details</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-zinc-400">Airline</span><span class="text-white">{{ $booking->flight->airline->name }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Flight</span><span class="text-white font-mono">{{ $booking->flight->flight_number }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Route</span><span class="text-white">{{ $booking->flight->departureAirport->iata_code }} → {{ $booking->flight->arrivalAirport->iata_code }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Date</span><span class="text-white">{{ \Carbon\Carbon::parse($booking->flight->departure_time)->format('d M Y H:i') }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Aircraft</span><span class="text-white">{{ $booking->flight->airplane->name ?? 'N/A' }}</span></div>
            </div>
        </div>

        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
            <h3 class="text-base font-semibold text-white mb-4">Payment Summary</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-zinc-400">Booking Code</span><span class="text-amber-500 font-mono">{{ $booking->booking_code }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Total Paid</span><span class="text-2xl font-bold text-emerald-400">Rp {{ number_format($booking->total_price,0,',','.') }}</span></div>
                <div class="flex justify-between">
                    <span class="text-zinc-400">Payment Status</span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400">
                        {{ $booking->payment ? ucfirst($booking->payment->payment_status) : 'Paid' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-400">Booking Status</span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
                @if($booking->payment && $booking->payment->payment_method)
                <div class="flex justify-between"><span class="text-zinc-400">Method</span><span class="text-white">{{ ucfirst(str_replace('_', ' ', $booking->payment->payment_method)) }}</span></div>
                @endif
                @if($booking->payment && $booking->payment->settlement_time)
                <div class="flex justify-between"><span class="text-zinc-400">Paid At</span><span class="text-white">{{ \Carbon\Carbon::parse($booking->payment->settlement_time)->format('d M Y H:i') }}</span></div>
                @endif
            </div>
        </div>
    </div>

    <!-- Passenger Information -->
    @if($booking->passengers && $booking->passengers->count() > 0)
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl overflow-hidden">
        <div class="p-6 border-b border-zinc-800">
            <h3 class="text-base font-semibold text-white">Passenger Information</h3>
            <p class="text-xs text-zinc-500 mt-1">{{ $booking->total_passengers }} passenger(s)</p>
        </div>
        <div class="divide-y divide-zinc-800">
            @foreach($booking->passengers as $passenger)
            <div class="p-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-amber-500/10 rounded-full flex items-center justify-center">
                        <span class="text-amber-500 font-semibold text-sm">{{ substr($passenger->full_name, 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="text-white font-medium">{{ $passenger->full_name }}</p>
                        <p class="text-zinc-400 text-xs">{{ $passenger->nationality ?? 'N/A' }} • Seat {{ optional($passenger->seat)->seat_number ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4">
        @if($booking->status === 'issued' || $booking->status === 'paid')
        <a href="{{ route('customer.eticket', $booking) }}" class="flex-1 px-6 py-3 bg-gradient-to-r from-amber-500 to-amber-600 text-black rounded-xl font-semibold text-center hover:from-amber-600 hover:to-amber-700 transition shadow-lg shadow-amber-500/20">
            <i class="fas fa-ticket-alt mr-2"></i>Download E-Ticket
        </a>
        @endif
        <a href="{{ route('customer.bookings.history') }}" class="flex-1 px-6 py-3 bg-zinc-800 text-white rounded-xl font-semibold text-center hover:bg-zinc-700 transition">
            <i class="fas fa-history mr-2"></i>View Booking History
        </a>
        <a href="{{ route('customer.dashboard') }}" class="flex-1 px-6 py-3 bg-zinc-800 text-white rounded-xl font-semibold text-center hover:bg-zinc-700 transition">
            <i class="fas fa-home mr-2"></i>Back to Dashboard
        </a>
    </div>
</div>
@endsection