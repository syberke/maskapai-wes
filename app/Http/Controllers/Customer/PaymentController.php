<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Flight;
use App\Services\MidtransService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    private const ACTIVE_BOOKING_STATUSES = ['pending', 'paid', 'issued'];

    public function __construct(private MidtransService $midtrans)
    {
    }

    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = Booking::with(['flight.departureAirport', 'flight.arrivalAirport', 'flight.airline', 'payment', 'passengers'])
            ->where('user_id', $user->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($bookingQuery) use ($search) {
                $bookingQuery->where('booking_code', 'like', "%{$search}%")
                    ->orWhereHas('flight', fn ($flightQuery) => $flightQuery->where('flight_number', 'like', "%{$search}%"))
                    ->orWhereHas('passengers', fn ($passengerQuery) => $passengerQuery->where('full_name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $paymentStatuses = match ($request->status) {
                'pending' => ['pending'],
                'paid' => ['paid'],
                'failed' => ['failed', 'expired'],
                'refunded' => ['refunded'],
                default => [],
            };

            if ($paymentStatuses !== []) {
                $query->whereHas('payment', fn ($paymentQuery) => $paymentQuery->whereIn('payment_status', $paymentStatuses));
            }
        }

        match ($request->input('sort')) {
            'oldest' => $query->oldest(),
            'price_high' => $query->orderByDesc('total_price'),
            'price_low' => $query->orderBy('total_price'),
            default => $query->latest(),
        };

        $bookings = $query->paginate(10)->withQueryString();
        $stats = [
            'total' => Booking::where('user_id', $user->id)->count(),
            'pending' => $this->paymentCount($user->id, ['pending']),
            'paid' => $this->paymentCount($user->id, ['paid']),
            'failed' => $this->paymentCount($user->id, ['failed', 'expired']),
            'refunded' => $this->paymentCount($user->id, ['refunded']),
        ];

        return view('customer.payments.index', compact('bookings', 'stats'));
    }

    public function show(Booking $booking): View
    {
        $this->authorizeBooking($booking);
        $booking->load(['flight.departureAirport', 'flight.arrivalAirport', 'flight.airline', 'payment', 'passengers.seat']);

        return view('customer.payment.show', compact('booking'));
    }

    public function success(Booking $booking): View
    {
        $this->authorizeBooking($booking);
        $booking->load(['flight.departureAirport', 'flight.arrivalAirport', 'flight.airline', 'flight.airplane', 'passengers.seat', 'payment']);

        if ($booking->status === 'pending' && $booking->payment) {
            try {
                // booking_code already contains LXF-. This must match the Midtrans order_id exactly.
                $transaction = $this->midtrans->getTransactionStatus($booking->booking_code);

                if (! empty($transaction['transaction_status'])) {
                    $this->synchronizeGatewayStatus($booking, $transaction);
                    $booking->refresh()->load(['flight.departureAirport', 'flight.arrivalAirport', 'flight.airline', 'flight.airplane', 'passengers.seat', 'payment']);
                }
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        return view('customer.payment.success', compact('booking'));
    }

    public function process(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeBooking($booking);

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Pembayaran sudah diproses sebelumnya.');
        }

        if (! $booking->payment) {
            return back()->with('error', 'Payment record not found. Please contact support.');
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'in:bank_transfer,credit_card,e_wallet'],
        ]);

        $booking->payment->update(['payment_method' => $validated['payment_method']]);

        return redirect()->route('customer.midtrans.pay', $booking)
            ->with('success', 'Metode pembayaran dipilih. Silakan selesaikan pembayaran.');
    }

    public function eticket(Booking $booking): View|RedirectResponse
    {
        $this->authorizeBooking($booking);

        if (! in_array($booking->status, ['issued', 'paid'], true)) {
            return back()->with('error', 'E-Ticket hanya tersedia untuk booking yang sudah dikonfirmasi.');
        }

        $booking->load(['flight.departureAirport', 'flight.arrivalAirport', 'flight.airline', 'flight.airplane', 'passengers.seat', 'payment']);

        return view('customer.bookings.eticket', compact('booking'));
    }

    private function synchronizeGatewayStatus(Booking $booking, array $transaction): void
    {
        $gatewayStatus = (string) $transaction['transaction_status'];
        $fraudStatus = $transaction['fraud_status'] ?? null;
        $paymentStatus = match ($gatewayStatus) {
            'capture', 'settlement' => 'paid',
            'deny', 'cancel' => 'failed',
            'expire' => 'expired',
            'refund', 'partial_refund' => 'refunded',
            default => 'pending',
        };
        $bookingStatus = match ($gatewayStatus) {
            'capture', 'settlement' => $fraudStatus === 'challenge' ? 'paid' : 'issued',
            'deny', 'expire', 'cancel' => 'cancelled',
            'refund', 'partial_refund' => 'refunded',
            default => 'pending',
        };
        $settlementTime = in_array($gatewayStatus, ['capture', 'settlement'], true)
            ? ($transaction['settlement_time'] ?? now())
            : null;

        DB::transaction(function () use ($booking, $transaction, $gatewayStatus, $fraudStatus, $paymentStatus, $bookingStatus, $settlementTime): void {
            $lockedBooking = Booking::query()->lockForUpdate()->findOrFail($booking->id);
            $payment = $lockedBooking->payment()->lockForUpdate()->firstOrFail();

            $payment->update([
                'payment_status' => $paymentStatus,
                'midtrans_transaction_status' => $gatewayStatus,
                'transaction_id' => $transaction['transaction_id'] ?? $payment->transaction_id,
                'fraud_status' => $fraudStatus,
                'settlement_time' => $settlementTime,
            ]);

            $update = [
                'status' => $bookingStatus,
                'midtrans_transaction_status' => $gatewayStatus,
                'midtrans_transaction_id' => $transaction['transaction_id'] ?? $lockedBooking->midtrans_transaction_id,
                'payment_type' => $transaction['payment_type'] ?? $lockedBooking->payment_type,
                'paid_at' => $settlementTime,
            ];

            $shouldRelease = in_array($lockedBooking->status, self::ACTIVE_BOOKING_STATUSES, true)
                && in_array($bookingStatus, ['cancelled', 'refunded'], true)
                && $lockedBooking->capacity_released_at === null;

            if ($shouldRelease) {
                $flight = Flight::query()->lockForUpdate()->find($lockedBooking->flight_id);
                $flight?->increment('available_seats', (int) $lockedBooking->total_passengers);
                $update['capacity_released_at'] = now();
            }

            $lockedBooking->update($update);
        }, 3);
    }

    private function paymentCount(int $userId, array $statuses): int
    {
        return Booking::where('user_id', $userId)
            ->whereHas('payment', fn ($query) => $query->whereIn('payment_status', $statuses))
            ->count();
    }

    private function authorizeBooking(Booking $booking): void
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
