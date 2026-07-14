<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\SearchFlightRequest;
use App\Models\Flight;
use App\Models\Airline;
use App\Models\Seat;
use Illuminate\View\View;

class FlightSearchController extends Controller
{
    public function search(SearchFlightRequest $request): View
    {
        // Simpan parameter pencarian ke dalam session untuk dibawa ke alur booking nanti
        session(['search_params' => $request->validated()]);

        $class = $request->class;
        $passengers = $request->passengers;

        // Get airplane IDs that have enough available seats in the requested class
        $airplaneIdsWithSeats = Seat::where('class', $class)
            ->where('status', 'available')
            ->selectRaw('airplane_id, COUNT(*) as available_count')
            ->groupBy('airplane_id')
            ->having('available_count', '>=', $passengers)
            ->pluck('airplane_id');

        $query = Flight::with(['airline', 'airplane', 'departureAirport', 'arrivalAirport'])
            ->where('departure_airport_id', $request->departure_airport_id)
            ->where('arrival_airport_id', $request->arrival_airport_id)
            ->whereDate('departure_time', $request->departure_date)
            ->where('available_seats', '>=', $passengers)
            ->whereIn('airplane_id', $airplaneIdsWithSeats);

        // Filter Maskapai
        if ($request->filled('airline_id')) {
            $query->where('airline_id', $request->airline_id);
        }

        // Filter Rentang Harga
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter Waktu Keberangkatan (Pagi, Siang, Malam)
        if ($request->filled('time_slat')) {
            if ($request->time_slat === 'morning') {
                $query->whereTime('departure_time', '>=', '04:00:00')
                      ->whereTime('departure_time', '<', '11:00:00');
            } elseif ($request->time_slat === 'afternoon') {
                $query->whereTime('departure_time', '>=', '11:00:00')
                      ->whereTime('departure_time', '<', '18:00:00');
            } elseif ($request->time_slat === 'night') {
                $query->where(function ($q) {
                    $q->whereTime('departure_time', '>=', '18:00:00')
                      ->orWhereTime('departure_time', '<', '04:00:00');
                });
            }
        }

        $flights = $query->orderBy('departure_time', 'asc')->get();
        $airlines = Airline::all();

        return view('customer.flights.results', compact('flights', 'airlines', 'class', 'passengers'));
    }
}