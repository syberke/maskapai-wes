<?php

namespace Tests\Feature;

use App\Models\Airline;
use App\Models\Airplane;
use App\Models\Airport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFlightCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_flight_with_server_generated_values(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $airline = Airline::create(['name' => 'Garuda Test', 'code' => 'GT']);
        $airplane = Airplane::create([
            'airline_id' => $airline->id,
            'model' => 'Boeing 737-800',
            'registration_number' => 'PK-TST',
            'capacity' => 184,
        ]);
        $departureAirport = Airport::create(['name' => 'Airport A', 'city' => 'Jakarta', 'country' => 'Indonesia', 'iata_code' => 'AAA']);
        $arrivalAirport = Airport::create(['name' => 'Airport B', 'city' => 'Malang', 'country' => 'Indonesia', 'iata_code' => 'BBB']);
        $departureTime = now()->addDay()->setTime(8, 0, 0);
        $arrivalTime = $departureTime->copy()->addHours(2)->addMinutes(30);

        $response = $this->actingAs($admin)->post(route('admin.flights.store'), [
            'airline_id' => $airline->id,
            'airplane_id' => $airplane->id,
            'departure_airport_id' => $departureAirport->id,
            'arrival_airport_id' => $arrivalAirport->id,
            'departure_time' => $departureTime->format('Y-m-d H:i:s'),
            'arrival_time' => $arrivalTime->format('Y-m-d H:i:s'),
            'price' => 1000000,
            'status' => 'scheduled',
        ]);

        $response->assertRedirect(route('admin.flights.index'));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('flights', [
            'airline_id' => $airline->id,
            'airplane_id' => $airplane->id,
            'available_seats' => 184,
            'flight_duration' => '2 Jam 30 Menit',
        ]);
    }
}
