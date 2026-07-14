@extends('layouts.customer')
@section('title', 'Payments')
@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="space-y-4">
        <nav class="flex items-center space-x-2 text-sm">
            <a href="{{ route('customer.dashboard') }}" class="text-zinc-400 hover:text-amber-500 transition">Home</a>
            <span class="text-zinc-600">›</span>
            <a href="{{ route('customer.dashboard') }}" class="text-zinc-400 hover:text-amber-500 transition">Dashboard</a>
            <span class="text-zinc-600">›</span>
            <span class="text-amber-500 font-medium">Payments</span>
        </nav>
        
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">Payments</h1>
                <p class="text-zinc-400 mt-1">Manage all your airline payments</p>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-gradient-to-br from-zinc-900 to-zinc-800 border border-zinc-700 rounded-xl p-4 hover:border-amber-500/30 transition-all duration-300 hover:shadow-lg hover:shadow-amber-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-zinc-400 text-xs uppercase tracking-wider mb-1">Total</p>
                    <p class="text-2xl font-bold text-white">{{ $stats['total'] }}</p>
                </div>
                <div class="w-10 h-10 bg-amber-500/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-wallet text-amber-500"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-zinc-900 to-zinc-800 border border-zinc-700 rounded-xl p-4 hover:border-yellow-500/30 transition-all duration-300 hover:shadow-lg hover:shadow-yellow-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-zinc-400 text-xs uppercase tracking-wider mb-1">Pending</p>
                    <p class="text-2xl font-bold text-yellow-400">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-500/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-zinc-900 to-zinc-800 border border-zinc-700 rounded-xl p-4 hover:border-emerald-500/30 transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-zinc-400 text-xs uppercase tracking-wider mb-1">Completed</p>
                    <p class="text-2xl font-bold text-emerald-400">{{ $stats['paid'] }}</p>
                </div>
                <div class="w-10 h-10 bg-emerald-500/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-zinc-900 to-zinc-800 border border-zinc-700 rounded-xl p-4 hover:border-red-500/30 transition-all duration-300 hover:shadow-lg hover:shadow-red-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-zinc-400 text-xs uppercase tracking-wider mb-1">Failed</p>
                    <p class="text-2xl font-bold text-red-400">{{ $stats['failed'] }}</p>
                </div>
                <div class="w-10 h-10 bg-red-500/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-zinc-900 to-zinc-800 border border-zinc-700 rounded-xl p-4 hover:border-blue-500/30 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-zinc-400 text-xs uppercase tracking-wider mb-1">Refunded</p>
                    <p class="text-2xl font-bold text-blue-400">{{ $stats['refunded'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-500/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-undo text-blue-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-zinc-900/80 backdrop-blur-sm border border-zinc-800 rounded-xl p-6">
        <form method="GET" action="{{ route('customer.payments.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-zinc-400 mb-2">Search</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Booking code, flight number, or passenger name..." class="w-full bg-zinc-800 border border-zinc-700 rounded-lg pl-10 pr-4 py-2.5 text-white text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-2">Status</label>
                    <select name="status" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed/Expired</option>
                        <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>

                <!-- Sort -->
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-2">Sort By</label>
                    <select name="sort" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                        <option value="newest" {{ request('sort') === 'newest' || !request('sort') ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Highest Price</option>
                        <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Lowest Price</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 text-black rounded-lg font-semibold text-sm hover:from-amber-600 hover:to-amber-700 transition shadow-lg shadow-amber-500/20">
                    <i class="fas fa-filter mr-2"></i>Apply Filters
                </button>
                <a href="{{ route('customer.payments.index') }}" class="px-6 py-2.5 bg-zinc-800 text-zinc-300 rounded-lg font-semibold text-sm hover:bg-zinc-700 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Payments Table -->
    <div class="bg-zinc-900/80 backdrop-blur-sm border border-zinc-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-zinc-500 bg-zinc-800/50">
                        <th class="px-6 py-4">Booking Code</th>
                        <th class="px-6 py-4">Flight</th>
                        <th class="px-6 py-4">Route</th>
                        <th class="px-6 py-4">Passenger</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Payment Status</th>
                        <th class="px-6 py-4">Booking Status</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @forelse($bookings as $booking)
                    <tr class="text-sm text-zinc-300 hover:bg-zinc-800/30 transition">
                        <td class="px-6 py-4">
                            <span class="font-mono text-amber-500 font-semibold">{{ $booking->booking_code }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-white font-medium">{{ $booking->flight->airline->name }}</p>
                                <p class="text-xs text-zinc-500">{{ $booking->flight->flight_number }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="text-white">{{ $booking->flight->departureAirport->iata_code }}</span>
                                <i class="fas fa-arrow-right text-amber-500 text-xs"></i>
                                <span class="text-white">{{ $booking->flight->arrivalAirport->iata_code }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-white">{{ $booking->passengers->first()->full_name ?? 'N/A' }}</p>
                            <p class="text-xs text-zinc-500">{{ $booking->total_passengers }} passenger(s)</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-white font-semibold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $paymentStatus = $booking->payment->payment_status ?? 'pending';
                                $statusColors = [
                                    'pending' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
                                    'paid' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                    'failed' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                    'expired' => 'bg-zinc-500/10 text-zinc-400 border-zinc-500/20',
                                    'refunded' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $statusColors[$paymentStatus] ?? $statusColors['pending'] }}">
                                {{ ucfirst($paymentStatus) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $bookingColors = [
                                    'pending' => 'bg-yellow-500/10 text-yellow-400',
                                    'paid' => 'bg-emerald-500/10 text-emerald-400',
                                    'issued' => 'bg-emerald-500/10 text-emerald-400',
                                    'cancelled' => 'bg-red-500/10 text-red-400',
                                    'refunded' => 'bg-blue-500/10 text-blue-400',
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $bookingColors[$booking->status] ?? 'bg-zinc-500/10 text-zinc-400' }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-zinc-400">
                            {{ \Carbon\Carbon::parse($booking->created_at)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <button onclick="showPaymentDetail({{ $booking->id }})" class="p-2 bg-zinc-800 text-zinc-300 rounded-lg hover:bg-zinc-700 transition" title="View Details">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                                
                                @if($booking->status === 'pending')
                                <a href="{{ route('customer.payment.show', $booking) }}" class="p-2 bg-amber-500/10 text-amber-500 rounded-lg hover:bg-amber-500/20 transition" title="Pay Now">
                                    <i class="fas fa-credit-card text-xs"></i>
                                </a>
                                @endif

                                @if($booking->status === 'issued' || $booking->status === 'paid')
                                <a href="{{ route('customer.eticket', $booking) }}" class="p-2 bg-emerald-500/10 text-emerald-400 rounded-lg hover:bg-emerald-500/20 transition" title="Download E-Ticket">
                                    <i class="fas fa-ticket-alt text-xs"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12">
                            <div class="text-center">
                                <i class="fas fa-inbox text-6xl text-zinc-700 mb-4"></i>
                                <p class="text-zinc-400 text-lg mb-2">No payments found</p>
                                <p class="text-zinc-500 text-sm">Start booking flights to see your payments here</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($bookings->hasPages())
        <div class="p-4 border-t border-zinc-800">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Payment Detail Modal -->
<div id="paymentModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-zinc-900 border-b border-zinc-800 px-6 py-4 flex items-center justify-between">
            <h3 class="text-xl font-bold text-white">Payment Details</h3>
            <button onclick="closePaymentModal()" class="text-zinc-400 hover:text-white transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="paymentModalContent" class="p-6">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>

@push('scripts')
<script>
function showPaymentDetail(bookingId) {
    const modal = document.getElementById('paymentModal');
    const content = document.getElementById('paymentModalContent');
    
    // Show loading state
    content.innerHTML = '<div class="text-center py-12"><i class="fas fa-spinner fa-spin text-4xl text-amber-500"></i></div>';
    modal.classList.remove('hidden');
    
    // Fetch payment details
    fetch(`/customer/bookings/${bookingId}`)
        .then(response => response.text())
        .then(html => {
            // Extract the relevant content from the booking page
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const bookingDetails = doc.querySelector('.booking-details');
            
            if (bookingDetails) {
                content.innerHTML = bookingDetails.innerHTML;
            } else {
                content.innerHTML = '<p class="text-center text-zinc-400 py-8">Unable to load payment details</p>';
            }
        })
        .catch(error => {
            content.innerHTML = '<p class="text-center text-red-400 py-8">Error loading payment details</p>';
        });
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePaymentModal();
    }
});

// Close modal on backdrop click
document.getElementById('paymentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentModal();
    }
});
</script>
@endpush
@endsection