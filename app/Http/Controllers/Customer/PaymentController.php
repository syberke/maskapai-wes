<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    public function __construct(private MidtransService $midtrans) {}

    public function index(Request $request): View
    {
        $user = auth()->user();
        
        // Base query with relationships
        $query = Booking::with(['flight.departureAirport', 'flight.arrivalAirport', 'flight.airline', 'payment', 'passengers'])
            ->where('user_id', $user->id)
            ->latest();
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhereHas('flight', function($flight) use ($search) {
                      $flight->where('flight_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('passengers', function($p) use ($search) {
                      $p->where('full_name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by payment status
        if ($request->has('status') && $request->status) {
            $status = $request->status;
            if ($status === 'pending') {
                $query->whereHas('payment', function($q) {
                    $q->where('payment_status', 'pending');
                });
            } elseif ($status === 'paid') {
                $query->whereHas('payment', function($q) {
                    $q->where('payment_status', 'paid');
                });
            } elseif ($status === 'failed') {
                $query->whereHas('payment', function($q) {
                    $q->whereIn('payment_status', ['failed', 'expired']);
                });
            } elseif ($status === 'refunded') {
                $query->whereHas('payment', function($q) {
                    $q->where('payment_status', 'refunded');
                });
            }
        }
        
        // Sorting
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->oldest();
                    break;
                case 'price_high':
                    $query->orderByDesc('total_price');
                    break;
                case 'price_low':
                    $query->orderBy('total_price');
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }
        
        $bookings = $query->paginate(10)->withQueryString();
        
        // Statistics
        $stats = [
            'total' => Booking::where('user_id', $user->id)->count(),
            'pending' => Booking::where('user_id', $user->id)->whereHas('payment', function($q) {
                $q->where('payment_status', 'pending');
            })->count(),
            'paid' => Booking::where('user_id', $user->id)->whereHas('payment', function($q) {
                $q->where('payment_status', 'paid');
            })->count(),
            'failed' => Booking::where('user_id', $user->id)->whereHas('payment', function($q) {
                $q->whereIn('payment_status', ['failed', 'expired']);
            })->count(),
            'refunded' => Booking::where('user_id', $user->id)->whereHas('payment', function($q) {
                $q->where('payment_status', 'refunded');
            })->count(),
        ];
        
        return view('customer.payments.index', compact('bookings', 'stats'));
    }

    public function show(Booking $booking): View
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $booking->load(['flight.departureAirport', 'flight.arrivalAirport', 'flight.airline', 'payment', 'passengers']);

        // If status check is requested via AJAX
        if (request()->has('check_status')) {
            return view('customer.payment.show', compact('booking'));
        }

        return view('customer.payment.show', compact('booking'));
    }

    public function success(Booking $booking): View
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        // Refresh booking from database to get latest status
        $booking->refresh();
        $booking->load(['flight.departureAirport', 'flight.arrivalAirport', 'flight.airline', 'flight.airplane', 'passengers.seat', 'payment']);
        
        // If booking still shows pending, try to check Midtrans status
        if ($booking->status === 'pending' && $booking->payment) {
            try {
                $orderId = 'LXF-' . $booking->booking_code;
                $transactionStatus = $this->midtrans->getTransactionStatus($orderId);
                
                if (!empty($transactionStatus) && isset($transactionStatus['transaction_status'])) {
                    $status = $transactionStatus['transaction_status'];
                    $fraudStatus = $transactionStatus['fraud_status'] ?? null;
                    
                    // Map statuses
                    $paymentStatusMap = [
                        'capture' => 'paid', 'settlement' => 'paid',
                        'pending' => 'pending', 'deny' => 'failed',
                        'expire' => 'expired', 'cancel' => 'failed',
                        'refund' => 'refunded', 'partial_refund' => 'refunded'
                    ];
                    
                    $bookingStatusMap = [
                        'capture' => 'issued', 'settlement' => 'issued',
                        'pending' => 'pending', 'deny' => 'cancelled',
                        'expire' => 'cancelled', 'cancel' => 'cancelled',
                        'refund' => 'refunded', 'partial_refund' => 'refunded'
                    ];
                    
                    if (in_array($status, ['capture', 'settlement'])) {
                        $booking->payment->update([
                            'payment_status' => 'paid',
                            'midtrans_transaction_status' => $status,
                            'settlement_time' => $transactionStatus['settlement_time'] ?? now(),
                        ]);
                        $booking->update(['status' => 'issued']);
                        $booking->refresh();
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Status check failed on success page', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return view('customer.payment.success', compact('booking'));
    }

    public function process(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Pembayaran sudah diproses sebelumnya.');
        }

        // Verify payment exists
        if (!$booking->payment) {
            return back()->with('error', 'Payment record not found. Please contact support.');
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'in:bank_transfer,credit_card,e_wallet'],
        ]);

        // Update payment method
        $booking->payment->update([
            'payment_method' => $validated['payment_method'],
        ]);

        \Log::info('Payment method selected', [
            'booking_id' => $booking->id,
            'payment_method' => $validated['payment_method']
        ]);

        // Redirect to Midtrans pay route which will generate Snap token
        return redirect()->route('customer.midtrans.pay', $booking->id)
            ->with('success', 'Metode pembayaran dipilih. Silakan selesaikan pembayaran.');
    }

    public function eticket(Booking $booking): View
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if ($booking->status !== 'issued' && $booking->status !== 'paid') {
            return back()->with('error', 'E-Ticket hanya tersedia untuk booking yang sudah dikonfirmasi.');
        }

        $booking->load(['flight.departureAirport', 'flight.arrivalAirport', 'flight.airline', 'flight.airplane', 'passengers', 'payment']);
        return view('customer.bookings.eticket', compact('booking'));
    }
}