<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\SearchFlightRequest;
use App\Models\Airline;
use App\Models\Flight;
use App\Models\Passenger;
use App\Models\Seat;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class FlightSearchController extends Controller
{
    private const ACTIVE_BOOKING_STATUSES = ['pending', 'paid', 'issued'];

    public function search(SearchFlightRequest $request): View
    {
        session(['search_params' => $request->validated()]);

        $class = $request->class;
        $passengers = (int) $request->passengers;

        $query = Flight::with(['airline', 'airplane', 'departureAirport', 'arrivalAirport'])
            ->where('departure_airport_id', $request->departure_airport_id)
            ->where('arrival_airport_id', $request->arrival_airport_id)
            ->whereDate('departure_time', $request->departure_date)
            ->where('available_seats', '>=', $passengers);

        if ($request->filled('airline_id')) {
            $query->where('airline_id', $request->airline_id);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('time_slat')) {
            if ($request->time_slat === 'morning') {
                $query->whereTime('departure_time', '>=', '04:00:00')
                    ->whereTime('departure_time', '<', '11:00:00');
            } elseif ($request->time_slat === 'afternoon') {
                $query->whereTime('departure_time', '>=', '11:00:00')
                    ->whereTime('departure_time', '<', '18:00:00');
            } elseif ($request->time_slat === 'night') {
                $query->where(function ($timeQuery) {
                    $timeQuery->whereTime('departure_time', '>=', '18:00:00')
                        ->orWhereTime('departure_time', '<', '04:00:00');
                });
            }
        }

        $flights = $query->orderBy('departure_time')
            ->get()
            ->filter(fn (Flight $flight): bool => $this->availableSeatsForClass($flight, $class) >= $passengers)
            ->values();

        $airlines = Airline::all();

        return view('customer.flights.results', compact('flights', 'airlines', 'class', 'passengers'));
    }

    private function availableSeatsForClass(Flight $flight, string $class): int
    {
        $classSeats = Seat::query()
            ->where('airplane_id', $flight->airplane_id)
            ->where('class', $class)
            ->get(['id', 'seat_number']);

        if ($classSeats->isEmpty()) {
            return 0;
        }

        $seatIds = $classSeats->pluck('id')->all();
        $seatNumbers = $classSeats->pluck('seat_number')->all();

        $reserved = Passenger::query()
            ->whereHas('booking', function ($bookingQuery) use ($flight) {
                $bookingQuery->where('flight_id', $flight->id)
                    ->whereIn('status', self::ACTIVE_BOOKING_STATUSES);
            })
            ->where(function ($passengerQuery) use ($seatIds, $seatNumbers) {
                if (Schema::hasColumn('passengers', 'seat_id')) {
                    $passengerQuery->whereIn('seat_id', $seatIds)
                        ->orWhereIn('seat_number', $seatNumbers);
                } else {
                    $passengerQuery->whereIn('seat_number', $seatNumbers);
                }
            })
            ->count();

        return max(0, $classSeats->count() - $reserved);
    }
}
