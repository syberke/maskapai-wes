@extends('layouts.customer')
@section('title', 'Payment')
@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-gradient-to-r from-amber-500/10 to-amber-600/5 border border-amber-500/20 rounded-xl p-6">
        <h2 class="text-xl font-bold text-white">Payment</h2>
        <p class="text-zinc-400 text-sm mt-1">Complete your booking payment</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
            <h3 class="text-base font-semibold text-white mb-4">Booking Details</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-zinc-400">Booking Code</span><span class="text-amber-500 font-mono">{{ $booking->booking_code }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Flight</span><span class="text-white">{{ $booking->flight->airline->name }} - {{ $booking->flight->flight_number }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Route</span><span class="text-white">{{ $booking->flight->departureAirport->iata_code }} → {{ $booking->flight->arrivalAirport->iata_code }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Date</span><span class="text-white">{{ \Carbon\Carbon::parse($booking->flight->departure_time)->format('d M Y H:i') }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-400">Passengers</span><span class="text-white">{{ $booking->total_passengers }} person(s)</span></div>
            </div>
        </div>

        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
            <h3 class="text-base font-semibold text-white mb-4" id="payment-status-header">Payment Summary</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-zinc-400">Total Price</span><span class="text-2xl font-bold text-amber-500">Rp {{ number_format($booking->total_price,0,',','.') }}</span></div>
                <div class="flex justify-between">
                    <span class="text-zinc-400">Status</span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold 
                        @if($booking->status === 'issued' || $booking->status === 'paid') bg-emerald-500/10 text-emerald-400
                        @elseif($booking->status === 'cancelled') bg-red-500/10 text-red-400
                        @else bg-yellow-500/10 text-yellow-400 
                        @endif" id="booking-status-badge">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
                @if($booking->payment && $booking->payment->payment_method)
                <div class="flex justify-between"><span class="text-zinc-400">Method</span><span class="text-white">{{ ucfirst(str_replace('_', ' ', $booking->payment->payment_method)) }}</span></div>
                @endif
                <div class="flex justify-between"><span class="text-zinc-400">Payment</span><span class="px-2.5 py-1 rounded-full text-xs font-semibold 
                    @if($booking->payment && $booking->payment->payment_status === 'paid') bg-emerald-500/10 text-emerald-400
                    @else bg-yellow-500/10 text-yellow-400 
                    @endif" id="payment-status-badge">
                    {{ $booking->payment ? ucfirst($booking->payment->payment_status) : 'Pending' }}
                </span></div>
            </div>
        </div>
    </div>

    @if($booking->status === 'pending' && (!$booking->payment || !$booking->payment->snap_token))
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-6">
        <h3 class="text-base font-semibold text-white mb-4">Select Payment Method</h3>
        <form method="POST" action="{{ route('customer.midtrans.pay', $booking) }}" id="payment-form" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <label class="cursor-pointer">
                    <input type="radio" name="payment_method" value="bank_transfer" class="peer sr-only" required>
                    <div class="border border-zinc-700 peer-checked:border-amber-500 peer-checked:bg-amber-500/10 rounded-xl p-4 text-center transition hover:border-zinc-600">
                        <i class="fas fa-university text-2xl text-zinc-400 peer-checked:text-amber-500 mb-2"></i>
                        <p class="text-white text-sm font-semibold">Bank Transfer</p>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="payment_method" value="credit_card" class="peer sr-only">
                    <div class="border border-zinc-700 peer-checked:border-amber-500 peer-checked:bg-amber-500/10 rounded-xl p-4 text-center transition hover:border-zinc-600">
                        <i class="fas fa-credit-card text-2xl text-zinc-400 peer-checked:text-amber-500 mb-2"></i>
                        <p class="text-white text-sm font-semibold">Credit Card</p>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="payment_method" value="e_wallet" class="peer sr-only">
                    <div class="border border-zinc-700 peer-checked:border-amber-500 peer-checked:bg-amber-500/10 rounded-xl p-4 text-center transition hover:border-zinc-600">
                        <i class="fas fa-wallet text-2xl text-zinc-400 peer-checked:text-amber-500 mb-2"></i>
                        <p class="text-white text-sm font-semibold">E-Wallet</p>
                    </div>
                </label>
            </div>
            @error('payment_method') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
            <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-amber-500 to-amber-600 text-black rounded-xl font-semibold hover:from-amber-600 hover:to-amber-700 transition shadow-lg shadow-amber-500/20">
                Continue to Payment
            </button>
        </form>
    </div>
    @endif

    @if($booking->payment && $booking->payment->snap_token && $booking->status === 'pending')
    <div class="bg-zinc-900/80 border border-amber-500/20 rounded-xl p-6" id="midtrans-section">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-white">Complete Payment</h3>
            @if(config('midtrans.is_production') === false)
            <span class="px-3 py-1 bg-yellow-500/10 text-yellow-400 rounded-full text-xs font-semibold">Sandbox Mode</span>
            @endif
        </div>
        
        <!-- Payment Loading State -->
        <div id="payment-loading" class="hidden text-center py-8">
            <i class="fas fa-spinner fa-spin text-4xl text-amber-500 mb-4"></i>
            <p class="text-zinc-400">Processing your payment...</p>
            <p class="text-zinc-500 text-sm mt-2">Please complete the payment in the popup window</p>
        </div>

        <!-- Payment Actions -->
        <div id="payment-actions" class="space-y-3">
            <button onclick="openSnapPopup()" class="w-full px-6 py-3 bg-gradient-to-r from-amber-500 to-amber-600 text-black rounded-xl font-semibold hover:from-amber-600 hover:to-amber-700 transition shadow-lg shadow-amber-500/20">
                <i class="fas fa-credit-card mr-2"></i>Open Payment Popup
            </button>
            <p class="text-center text-zinc-500 text-xs">
                A popup window will open to complete your payment securely via Midtrans
            </p>
        </div>

        <!-- Payment Result -->
        <div id="payment-result" class="hidden mt-4"></div>

        <!-- Status Polling -->
        <div id="status-polling" class="hidden text-center py-4">
            <i class="fas fa-sync-alt fa-spin text-amber-500 text-lg"></i>
            <p class="text-zinc-400 text-sm mt-2">Checking payment status...</p>
        </div>

        <script src="https://{{ config('midtrans.is_production') ? 'app.midtrans.com' : 'app.sandbox.midtrans.com' }}/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
        <script>
        // Store booking and route references
        const BOOKING_ID = {{ $booking->id }};
        const SUCCESS_URL = '{{ route('customer.payment.success', $booking) }}';
        const SHOW_URL = '{{ route('customer.payment.show', $booking) }}';
        const STATUS_CHECK_URL = `${SHOW_URL}?check_status=1`;
        let snapPopup = null;
        let pollingInterval = null;

        // Initialize Snap popup when page loads
        document.addEventListener('DOMContentLoaded', function() {
            @if($booking->payment && $booking->payment->snap_token && $booking->status === 'pending')
            // Auto-open the Snap popup after page loads
            setTimeout(openSnapPopup, 500);
            @endif
        });

        function openSnapPopup() {
            const snapToken = '{{ $booking->payment ? $booking->payment->snap_token : '' }}';
            
            if (!snapToken || snapToken === '') {
                showError('Snap token not found. Please try again.');
                return;
            }

            // Show loading state
            document.getElementById('payment-loading').classList.remove('hidden');
            document.getElementById('payment-actions').classList.add('hidden');
            
            console.log('Opening Snap payment popup with token:', snapToken);

            // Open Snap popup with proper callbacks
            snap.pay(snapToken, {
                onSuccess: function(result) {
                    console.log('Payment Success:', result);
                    handlePaymentResult('success', result);
                },
                onPending: function(result) {
                    console.log('Payment Pending:', result);
                    handlePaymentResult('pending', result);
                },
                onError: function(result) {
                    console.error('Payment Error:', result);
                    handlePaymentResult('error', result);
                },
                onClose: function() {
                    console.log('Payment popup closed by user');
                    handlePopupClosed();
                }
            });
        }

        function handlePaymentResult(status, result) {
            // Clear any existing polling
            clearPolling();
            
            // Hide loading
            document.getElementById('payment-loading').classList.add('hidden');
            
            if (status === 'success') {
                // Payment succeeded
                showSuccess('Payment successful! Redirecting...');
                // Start polling to confirm status update on server
                startPolling(SUCCESS_URL);
            } else if (status === 'pending') {
                // Payment is pending (e.g., bank transfer)
                showSuccess('Payment is pending confirmation. Redirecting...');
                startPolling(SUCCESS_URL);
            } else {
                // Payment failed
                showError('Payment failed. Please try again.');
                document.getElementById('payment-actions').classList.remove('hidden');
            }
        }

        function handlePopupClosed() {
            clearPolling();
            document.getElementById('payment-loading').classList.add('hidden');
            
            // Check status to see if payment was actually made
            document.getElementById('status-polling').classList.remove('hidden');
            
            fetch(STATUS_CHECK_URL)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('status-polling').classList.add('hidden');
                    
                    // Check if status changed by looking for specific indicators
                    if (html.includes('payment-status-badge') && html.includes('emerald')) {
                        // Payment was actually completed
                        window.location.href = SUCCESS_URL;
                    } else {
                        // Payment was not completed
                        showWarning('Payment has not been completed.');
                        document.getElementById('payment-actions').classList.remove('hidden');
                    }
                })
                .catch(() => {
                    document.getElementById('status-polling').classList.add('hidden');
                    showWarning('Payment not completed. You can try again.');
                    document.getElementById('payment-actions').classList.remove('hidden');
                });
        }

        function startPolling(redirectUrl) {
            let attempts = 0;
            const maxAttempts = 30; // Check for 30 seconds
            
            pollingInterval = setInterval(function() {
                attempts++;
                
                fetch(STATUS_CHECK_URL)
                    .then(response => response.text())
                    .then(html => {
                        // Check if booking status is now issued/paid
                        if (html.includes('booking-status-badge') && html.includes('issued') || 
                            html.includes('booking-status-badge') && html.includes('emerald')) {
                            clearPolling();
                            window.location.href = redirectUrl;
                        } else if (attempts >= maxAttempts) {
                            clearPolling();
                            window.location.href = redirectUrl;
                        }
                    })
                    .catch(() => {
                        if (attempts >= maxAttempts) {
                            clearPolling();
                            window.location.href = redirectUrl;
                        }
                    });
            }, 1000);
        }

        function clearPolling() {
            if (pollingInterval) {
                clearInterval(pollingInterval);
                pollingInterval = null;
            }
        }

        function showSuccess(message) {
            const resultDiv = document.getElementById('payment-result');
            resultDiv.className = 'mt-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl text-sm';
            resultDiv.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + message;
            resultDiv.classList.remove('hidden');
        }

        function showError(message) {
            const resultDiv = document.getElementById('payment-result');
            resultDiv.className = 'mt-4 bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl text-sm';
            resultDiv.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>' + message;
            resultDiv.classList.remove('hidden');
        }

        function showWarning(message) {
            const resultDiv = document.getElementById('payment-result');
            resultDiv.className = 'mt-4 bg-yellow-500/10 border border-yellow-500/20 text-yellow-400 px-4 py-3 rounded-xl text-sm';
            resultDiv.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>' + message;
            resultDiv.classList.remove('hidden');
        }

        // Handle page visibility changes (user comes back from bank redirect)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden && document.getElementById('midtrans-section')) {
                // User returned to the page - check payment status
                fetch(STATUS_CHECK_URL)
                    .then(response => response.text())
                    .then(html => {
                        if (html.includes('booking-status-badge') && html.includes('issued') ||
                            html.includes('booking-status-badge') && html.includes('emerald')) {
                            window.location.href = SUCCESS_URL;
                        }
                    })
                    .catch(() => {});
            }
        });
        </script>
    </div>
    @endif

    @if(session('success'))
    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl text-sm">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl text-sm">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
    </div>
    @endif
</div>
@endsection