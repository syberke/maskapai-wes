<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Flight;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MidtransController extends Controller
{
    private const ACTIVE_BOOKING_STATUSES = ['pending', 'paid', 'issued'];

    public function __construct(private MidtransService $midtrans)
    {
    }

    public function pay(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if ($booking->status !== 'pending') {
            return redirect()->route('customer.booking.show', $booking)
                ->with('error', 'Payment has already been processed.');
        }

        if (! $booking->payment || $booking->payment->payment_status !== 'pending') {
            return redirect()->route('customer.booking.show', $booking)
                ->with('error', 'Payment record is unavailable or has already been processed.');
        }

        // booking_code already contains the LXF- prefix. Do not add it twice.
        $orderId = $booking->booking_code;
        $basePrice = (int) $booking->total_price;
        $tax = (int) ($basePrice * 0.11);
        $serviceFee = (int) ($basePrice * 0.05);
        $subtotal = $basePrice - $tax - $serviceFee;

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $basePrice,
            ],
            'customer_details' => [
                'first_name' => auth()->user()->first_name ?: auth()->user()->name,
                'last_name' => auth()->user()->last_name ?: '',
                'email' => auth()->user()->email,
                'phone' => auth()->user()->phone ?: '+62123456789',
                'billing_address' => [
                    'first_name' => auth()->user()->first_name ?: auth()->user()->name,
                    'last_name' => auth()->user()->last_name ?: '',
                    'email' => auth()->user()->email,
                    'phone' => auth()->user()->phone ?: '+62123456789',
                ],
            ],
            'item_details' => [
                [
                    'id' => 'FLIGHT-' . $booking->flight->flight_number,
                    'name' => $booking->flight->airline->name . ' - ' . $booking->flight->flight_number,
                    'price' => $subtotal,
                    'quantity' => 1,
                ],
                ['id' => 'TAX', 'name' => 'Tax (11%)', 'price' => $tax, 'quantity' => 1],
                ['id' => 'SERVICE', 'name' => 'Service Fee', 'price' => $serviceFee, 'quantity' => 1],
            ],
            'enabled_payments' => ['credit_card', 'bca_va', 'bni_va', 'bri_va', 'permata_va', 'gopay', 'shopeepay', 'qris'],
        ];

        try {
            $snapToken = $this->midtrans->createSnapToken($payload);

            $booking->payment->update([
                'snap_token' => $snapToken,
                'transaction_code' => $orderId,
            ]);

            return redirect()->route('customer.payment.show', $booking)
                ->with('success', 'Payment method selected. Please complete your payment.');
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()->route('customer.payment.show', $booking)
                ->with('error', 'Failed to initialize payment. Please try again.');
        }
    }

    public function callback(Request $request): JsonResponse
    {
        $data = $request->all();

        if (! $this->midtrans->verifyCallbackSignature($data)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 403);
        }

        $orderId = (string) ($data['order_id'] ?? '');
        $transactionStatus = (string) ($data['transaction_status'] ?? 'pending');
        $fraudStatus = $data['fraud_status'] ?? null;

        // Support both corrected LXF-XXXX and legacy LXF-LXF-XXXX order IDs.
        $bookingCode = str_starts_with($orderId, 'LXF-LXF-') ? substr($orderId, 4) : $orderId;

        $result = DB::transaction(function () use ($data, $bookingCode, $transactionStatus, $fraudStatus): array {
            $booking = Booking::query()
                ->where('booking_code', $bookingCode)
                ->lockForUpdate()
                ->first();

            if (! $booking) {
                return ['status' => 'error', 'message' => 'Booking not found', 'http' => 404];
            }

            $payment = $booking->payment()->lockForUpdate()->first();

            if (! $payment) {
                return ['status' => 'error', 'message' => 'Payment not found', 'http' => 404];
            }

            $bookingStatus = $this->mapBookingStatus($transactionStatus, $fraudStatus);
            $paymentStatus = $this->mapPaymentStatus($transactionStatus, $fraudStatus);
            $settlementTime = $this->getSettlementTime($transactionStatus, $data);

            $payment->update([
                'transaction_id' => $data['transaction_id'] ?? $payment->transaction_id,
                'midtrans_transaction_id' => $data['transaction_id'] ?? $payment->midtrans_transaction_id,
                'midtrans_transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'midtrans_status_code' => $data['status_code'] ?? null,
                'payment_status' => $paymentStatus,
                'settlement_time' => $settlementTime,
            ]);

            $bookingUpdate = [
                'status' => $bookingStatus,
                'midtrans_transaction_id' => $data['transaction_id'] ?? $booking->midtrans_transaction_id,
                'midtrans_transaction_status' => $transactionStatus,
                'payment_type' => $data['payment_type'] ?? $booking->payment_type,
                'paid_at' => $settlementTime,
            ];

            $shouldReleaseCapacity = in_array($booking->status, self::ACTIVE_BOOKING_STATUSES, true)
                && in_array($bookingStatus, ['cancelled', 'refunded'], true)
                && $booking->capacity_released_at === null;

            if ($shouldReleaseCapacity) {
                $flight = Flight::query()->lockForUpdate()->find($booking->flight_id);

                if ($flight) {
                    $flight->increment('available_seats', (int) $booking->total_passengers);
                }

                $bookingUpdate['capacity_released_at'] = now();
            }

            $booking->update($bookingUpdate);

            return [
                'status' => 'success',
                'booking_status' => $bookingStatus,
                'payment_status' => $paymentStatus,
                'http' => 200,
            ];
        }, 3);

        $httpStatus = $result['http'];
        unset($result['http']);

        return response()->json($result, $httpStatus);
    }

    private function mapPaymentStatus(string $transactionStatus, ?string $fraudStatus): string
    {
        return match ($transactionStatus) {
            'capture', 'settlement' => 'paid',
            'pending' => 'pending',
            'deny', 'cancel' => 'failed',
            'expire' => 'expired',
            'refund', 'partial_refund' => 'refunded',
            default => 'pending',
        };
    }

    private function mapBookingStatus(string $transactionStatus, ?string $fraudStatus): string
    {
        if (in_array($transactionStatus, ['capture', 'settlement'], true)) {
            return $fraudStatus === 'challenge' ? 'paid' : 'issued';
        }

        return match ($transactionStatus) {
            'pending' => 'pending',
            'deny', 'expire', 'cancel' => 'cancelled',
            'refund', 'partial_refund' => 'refunded',
            default => 'pending',
        };
    }

    private function getSettlementTime(string $status, array $data): ?string
    {
        if (in_array($status, ['capture', 'settlement'], true)) {
            return $data['settlement_time'] ?? now()->toDateTimeString();
        }

        return null;
    }
}
