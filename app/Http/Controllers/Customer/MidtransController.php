<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MidtransController extends Controller
{
    public function __construct(private MidtransService $midtrans) {}

    /**
     * Generate Snap token and redirect to Midtrans payment page
     */
    public function pay(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if ($booking->status !== 'pending') {
            return redirect()->route('customer.booking.show', $booking->id)
                ->with('error', 'Payment has already been processed.');
        }

        // Verify payment exists
        if (!$booking->payment) {
            return redirect()->route('customer.booking.show', $booking->id)
                ->with('error', 'Payment record not found. Please contact support.');
        }

        // Verify payment is pending
        if ($booking->payment->payment_status !== 'pending') {
            return redirect()->route('customer.booking.show', $booking->id)
                ->with('error', 'Payment has already been processed.');
        }

        // Generate unique order ID (max 50 characters for Midtrans)
        $orderId = 'LXF-' . $booking->booking_code;

        // Calculate item details with breakdown
        $basePrice = (int) $booking->total_price;
        $tax = (int) ($basePrice * 0.11); // 11% tax
        $serviceFee = (int) ($basePrice * 0.05); // 5% service fee
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
                [
                    'id' => 'TAX',
                    'name' => 'Tax (11%)',
                    'price' => $tax,
                    'quantity' => 1,
                ],
                [
                    'id' => 'SERVICE',
                    'name' => 'Service Fee',
                    'price' => $serviceFee,
                    'quantity' => 1,
                ],
            ],
            'enabled_payments' => ['credit_card', 'bca_va', 'bni_va', 'bri_va', 'permata_va', 'gopay', 'shopeepay', 'qris'],
        ];

        try {
            \Log::info('Creating Midtrans Snap Token', [
                'order_id' => $orderId,
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'amount' => $basePrice,
                'user_id' => auth()->id()
            ]);

            $snapToken = $this->midtrans->createSnapToken($payload);

            \Log::info('Midtrans Snap Token Created', [
                'order_id' => $orderId,
                'snap_token' => $snapToken,
                'booking_id' => $booking->id
            ]);

            // Save snap token and order ID to payment record
            $booking->payment->update([
                'snap_token' => $snapToken,
                'transaction_code' => $orderId,
            ]);

            // Redirect to payment page with booking ID (NOT token in session)
            return redirect()->route('customer.payment.show', $booking->id)
                ->with('success', 'Payment method selected. Please complete your payment.');
        } catch (\Exception $e) {
            \Log::error('Midtrans Snap Token Creation Failed', [
                'order_id' => $orderId,
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('customer.payment.show', $booking->id)
                ->with('error', 'Failed to initialize payment. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Handle Midtrans callback
     */
    public function callback(Request $request): RedirectResponse
    {
        $data = $request->all();

        \Log::info('Midtrans Callback Received', $data);

        // Verify signature
        if (!$this->midtrans->verifyCallbackSignature($data)) {
            \Log::error('Midtrans Callback - Invalid Signature', $data);
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 403);
        }

        $orderId = $data['order_id'];
        \Log::info('Processing callback for order_id', ['order_id' => $orderId]);

        // Extract booking code from order ID (format: LXF-XXXXXXX)
        $bookingCode = str_replace('LXF-', '', $orderId);
        $booking = Booking::where('booking_code', $bookingCode)->first();

        if (!$booking) {
            \Log::error('Midtrans Callback - Booking not found', ['booking_code' => $bookingCode]);
            return response()->json(['status' => 'error', 'message' => 'Booking not found'], 404);
        }

        $payment = $booking->payment;
        if (!$payment) {
            \Log::error('Midtrans Callback - Payment not found', ['booking_id' => $booking->id]);
            return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
        }

        $status = $data['transaction_status'];
        $fraudStatus = $data['fraud_status'] ?? null;

        \Log::info('Midtrans Callback - Updating status', [
            'booking_id' => $booking->id,
            'transaction_status' => $status,
            'fraud_status' => $fraudStatus
        ]);

        // Update payment record
        $payment->update([
            'transaction_id' => $data['transaction_id'],
            'midtrans_transaction_id' => $data['transaction_id'],
            'midtrans_transaction_status' => $status,
            'fraud_status' => $fraudStatus,
            'midtrans_status_code' => $data['status_code'],
            'payment_status' => $this->mapPaymentStatus($status, $fraudStatus),
            'settlement_time' => $this->getSettlementTime($status, $data),
        ]);

        // Update booking status
        $bookingStatus = $this->mapBookingStatus($status, $fraudStatus);
        $booking->update([
            'status' => $bookingStatus,
            'midtrans_transaction_id' => $data['transaction_id'],
            'midtrans_transaction_status' => $status,
            'payment_type' => $data['payment_type'] ?? null,
            'paid_at' => $this->getSettlementTime($status, $data),
        ]);

        \Log::info('Midtrans Callback - Success', [
            'booking_id' => $booking->id,
            'booking_status' => $bookingStatus,
            'payment_status' => $payment->payment_status
        ]);

        return response()->json(['status' => 'success', 'booking_status' => $bookingStatus]);
    }

    /**
     * Map Midtrans transaction status to our payment status
     */
    private function mapPaymentStatus(string $transactionStatus, ?string $fraudStatus): string
    {
        return match ($transactionStatus) {
            'capture', 'settlement' => 'paid',
            'pending' => 'pending',
            'deny' => 'failed',
            'expire' => 'expired',
            'cancel' => 'failed',
            'refund' => 'refunded',
            'partial_refund' => 'refunded',
            default => 'pending',
        };
    }

    /**
     * Map Midtrans status to booking status
     */
    private function mapBookingStatus(string $transactionStatus, ?string $fraudStatus): string
    {
        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            return $fraudStatus === 'challenge' ? 'paid' : 'issued';
        }

        return match ($transactionStatus) {
            'pending' => 'pending',
            'deny', 'expire', 'cancel' => 'cancelled',
            'refund', 'partial_refund' => 'refunded',
            default => 'pending',
        };
    }

    /**
     * Get settlement time from callback data
     */
    private function getSettlementTime(string $status, array $data): ?string
    {
        if (in_array($status, ['capture', 'settlement'])) {
            return $data['settlement_time'] ?? now()->toDateTimeString();
        }
        return null;
    }
}