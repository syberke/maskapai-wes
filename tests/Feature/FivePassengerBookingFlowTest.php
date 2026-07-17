<?php

namespace Tests\Feature;

use App\Models\Airline;
use App\Models\Airplane;
use App\Models\Airport;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\Passenger;
use App\Models\Seat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FivePassengerBookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_book_five_passengers_with_complete_gender_data(): void
    {
        [$customer, $flight, $seats] = $this->bookingFixture();

        $response = $this->actingAs($customer)->post(route('customer.bookings.store'), [
            'flight_id' => $flight->id,
            'cabin_class' => 'economy',
            'seats' => $seats->pluck('id')->all(),
            'passengers' => $this->passengerPayload(),
        ]);

        $booking = Booking::where('user_id', $customer->id)->firstOrFail();

        $response->assertRedirect(route('customer.payment.show', $booking));
        $response->assertSessionHasNoErrors();
        $this->assertSame(5, $booking->passengers()->count());
        $this->assertSame(3, $booking->passengers()->where('gender', 'L')->count());
        $this->assertSame(2, $booking->passengers()->where('gender', 'P')->count());
        $this->assertDatabaseHas('passengers', [
            'booking_id' => $booking->id,
            'full_name' => 'Passenger 5',
            'gender' => 'L',
            'seat_number' => '10E',
            'email' => 'passenger5@example.com',
        ]);
        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'payment_status' => 'pending',
        ]);
        $this->assertSame(179, $flight->fresh()->available_seats);
    }

    public function test_reserved_seat_is_blocked_only_for_the_same_flight(): void
    {
        [$customer, $flight, $seats] = $this->bookingFixture();
        $firstSeat = $seats->first();

        $existingBooking = Booking::create([
            'user_id' => $customer->id,
            'flight_id' => $flight->id,
            'booking_code' => 'LXF-EXISTING',
            'cabin_class' => 'economy',
            'total_passengers' => 1,
            'total_price' => 1000000,
            'status' => 'pending',
        ]);
        Passenger::create([
            'booking_id' => $existingBooking->id,
            'seat_id' => $firstSeat->id,
            'seat_number' => $firstSeat->seat_number,
            'full_name' => 'Existing Passenger',
            'email' => 'existing@example.com',
            'phone' => '08123456789',
            'gender' => 'L',
            'birth_date' => '1990-01-01',
            'date_of_birth' => '1990-01-01',
            'nationality' => 'Indonesia',
        ]);

        $payload = $this->passengerPayload()[0];
        $sameFlightResponse = $this->actingAs($customer)->post(route('customer.bookings.store'), [
            'flight_id' => $flight->id,
            'cabin_class' => 'economy',
            'seats' => [$firstSeat->id],
            'passengers' => [$payload],
        ]);
        $sameFlightResponse->assertSessionHasErrors('seats');

        $secondFlight = Flight::create([
            'airline_id' => $flight->airline_id,
            'airplane_id' => $flight->airplane_id,
            'flight_number' => 'GT202',
            'departure_airport_id' => $flight->departure_airport_id,
            'arrival_airport_id' => $flight->arrival_airport_id,
            'departure_time' => now()->addDays(2),
            'arrival_time' => now()->addDays(2)->addHours(2),
            'price' => 1000000,
            'economy_class_price' => 1000000,
            'business_class_price' => 2000000,
            'first_class_price' => 4000000,
            'available_seats' => 184,
            'status' => 'scheduled',
            'flight_duration' => '2h 00m',
        ]);

        $otherFlightResponse = $this->actingAs($customer)->post(route('customer.bookings.store'), [
            'flight_id' => $secondFlight->id,
            'cabin_class' => 'economy',
            'seats' => [$firstSeat->id],
            'passengers' => [$payload],
        ]);
        $otherFlightResponse->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'flight_id' => $secondFlight->id,
            'total_passengers' => 1,
        ]);
    }

    public function test_staff_manifest_displays_gender_and_exports_files(): void
    {
        [$customer, $flight, $seats] = $this->bookingFixture();
        $this->actingAs($customer)->post(route('customer.bookings.store'), [
            'flight_id' => $flight->id,
            'cabin_class' => 'economy',
            'seats' => $seats->pluck('id')->all(),
            'passengers' => $this->passengerPayload(),
        ]);

        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)
            ->get(route('staff.manifest.show', $flight))
            ->assertOk()
            ->assertSee('Passenger 1')
            ->assertSee('Male')
            ->assertSee('Female');

        $this->actingAs($staff)
            ->get(route('staff.manifest.pdf', $flight))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($staff)
            ->get(route('staff.manifest.excel', $flight))
            ->assertOk();
    }

    private function bookingFixture(): array
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $airline = Airline::create(['name' => 'Garuda Test', 'code' => 'GT']);
        $airplane = Airplane::create([
            'airline_id' => $airline->id,
            'model' => 'Boeing 737-800',
            'registration_number' => 'PK-TST',
            'capacity' => 184,
            'first_class_seats' => 4,
            'business_class_seats' => 12,
            'economy_class_seats' => 168,
            'total_seats' => 184,
        ]);
        $departure = Airport::create(['name' => 'Airport A', 'city' => 'Jakarta', 'country' => 'Indonesia', 'iata_code' => 'AAA']);
        $arrival = Airport::create(['name' => 'Airport B', 'city' => 'Malang', 'country' => 'Indonesia', 'iata_code' => 'BBB']);
        $flight = Flight::create([
            'airline_id' => $airline->id,
            'airplane_id' => $airplane->id,
            'flight_number' => 'GT101',
            'departure_airport_id' => $departure->id,
            'arrival_airport_id' => $arrival->id,
            'departure_time' => now()->addDay(),
            'arrival_time' => now()->addDay()->addHours(2),
            'price' => 1000000,
            'economy_class_price' => 1000000,
            'business_class_price' => 2000000,
            'first_class_price' => 4000000,
            'available_seats' => 184,
            'status' => 'scheduled',
            'flight_duration' => '2h 00m',
        ]);

        $seats = collect(['10A', '10B', '10C', '10D', '10E'])->map(fn (string $number) => Seat::create([
            'airplane_id' => $airplane->id,
            'seat_number' => $number,
            'class' => 'economy',
            'status' => 'available',
        ]));

        return [$customer, $flight, $seats];
    }

    private function passengerPayload(): array
    {
        return collect(range(1, 5))->map(fn (int $index): array => [
            'full_name' => 'Passenger ' . $index,
            'email' => 'passenger' . $index . '@example.com',
            'phone' => '0812345678' . $index,
            'gender' => in_array($index, [2, 4], true) ? 'female' : 'male',
            'date_of_birth' => '199' . $index . '-01-01',
            'nationality' => 'Indonesia',
            'passport_number' => 'PASS' . $index,
            'emergency_contact' => 'Contact ' . $index,
            'emergency_phone' => '0898765432' . $index,
        ])->all();
    }
}
