<?php

namespace Tests\Feature;

use App\Models\Airline;
use App\Models\Airplane;
use App\Models\Airport;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransCallbackFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_signed_settlement_callback_issues_booking_without_customer_session(): void
    {
        [$booking, $payment] = $this->callbackFixture();
        $payload = $this->signedPayload($booking, 'settlement');

        $this->postJson(route('customer.midtrans.callback'), $payload)
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'booking_status' => 'issued',
                'payment_status' => 'paid',
            ]);

        $this->assertSame('issued', $booking->fresh()->status);
        $this->assertSame('paid', $payment->fresh()->payment_status);
    }

    public function test_expired_callback_releases_capacity_only_once(): void
    {
        [$booking] = $this->callbackFixture();
        $flight = $booking->flight;
        $payload = $this->signedPayload($booking, 'expire');

        $this->postJson(route('customer.midtrans.callback'), $payload)->assertOk();
        $this->postJson(route('customer.midtrans.callback'), $payload)->assertOk();

        $this->assertSame('cancelled', $booking->fresh()->status);
        $this->assertNotNull($booking->fresh()->capacity_released_at);
        $this->assertSame(184, $flight->fresh()->available_seats);
    }

    public function test_invalid_signature_is_rejected(): void
    {
        [$booking] = $this->callbackFixture();
        $payload = $this->signedPayload($booking, 'settlement');
        $payload['signature_key'] = 'invalid';

        $this->postJson(route('customer.midtrans.callback'), $payload)
            ->assertForbidden();

        $this->assertSame('pending', $booking->fresh()->status);
    }

    private function callbackFixture(): array
    {
        config(['midtrans.server_key' => 'server-key-for-tests']);

        $user = User::factory()->create(['role' => 'customer']);
        $airline = Airline::create(['name' => 'Callback Air', 'code' => 'CA']);
        $airplane = Airplane::create([
            'airline_id' => $airline->id,
            'model' => 'Airbus A320',
            'registration_number' => 'PK-CBK',
            'capacity' => 184,
            'first_class_seats' => 4,
            'business_class_seats' => 12,
            'economy_class_seats' => 168,
            'total_seats' => 184,
        ]);
        $departure = Airport::create(['name' => 'Airport A', 'city' => 'Jakarta', 'country' => 'Indonesia', 'iata_code' => 'CBA']);
        $arrival = Airport::create(['name' => 'Airport B', 'city' => 'Malang', 'country' => 'Indonesia', 'iata_code' => 'CBB']);
        $flight = Flight::create([
            'airline_id' => $airline->id,
            'airplane_id' => $airplane->id,
            'flight_number' => 'CA101',
            'departure_airport_id' => $departure->id,
            'arrival_airport_id' => $arrival->id,
            'departure_time' => now()->addDay(),
            'arrival_time' => now()->addDay()->addHours(2),
            'price' => 1000000,
            'economy_class_price' => 1000000,
            'business_class_price' => 2000000,
            'first_class_price' => 4000000,
            'available_seats' => 179,
            'status' => 'scheduled',
            'flight_duration' => '2h 00m',
        ]);
        $booking = Booking::create([
            'user_id' => $user->id,
            'flight_id' => $flight->id,
            'booking_code' => 'LXF-CALLBACK',
            'cabin_class' => 'economy',
            'total_passengers' => 5,
            'total_price' => 5000000,
            'status' => 'pending',
        ]);
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => 5000000,
            'payment_status' => 'pending',
            'transaction_code' => $booking->booking_code,
        ]);

        return [$booking, $payment];
    }

    private function signedPayload(Booking $booking, string $status): array
    {
        $payload = [
            'order_id' => $booking->booking_code,
            'status_code' => '200',
            'gross_amount' => '5000000.00',
            'transaction_status' => $status,
            'transaction_id' => 'trx-' . $status,
            'payment_type' => 'bank_transfer',
        ];
        $payload['signature_key'] = hash(
            'sha512',
            $payload['order_id']
            . $payload['status_code']
            . $payload['gross_amount']
            . config('midtrans.server_key')
        );

        return $payload;
    }
}
