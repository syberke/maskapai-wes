<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\Passenger;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const ACTIVE_BOOKING_STATUSES = ['pending', 'paid', 'issued'];

    public function index(): View
    {
        $todayPassengers = Passenger::query()
            ->whereHas('booking', function ($bookingQuery) {
                $bookingQuery->whereIn('status', self::ACTIVE_BOOKING_STATUSES)
                    ->whereHas('flight', fn ($flightQuery) => $flightQuery->whereDate('departure_time', today()));
            });

        $stats = [
            'flights_today' => Flight::whereDate('departure_time', today())->count(),
            'active_flights' => Flight::whereBetween('departure_time', [now(), now()->addHours(6)])->count(),
            'total_passengers_today' => (clone $todayPassengers)->count(),
            'male_today' => (clone $todayPassengers)->whereIn('gender', ['L', 'M', 'male'])->count(),
            'female_today' => (clone $todayPassengers)->whereIn('gender', ['P', 'F', 'female'])->count(),
            'boarding_count' => Booking::query()
                ->whereIn('status', ['paid', 'issued'])
                ->whereHas('flight', fn ($query) => $query->whereDate('departure_time', today()))
                ->sum('total_passengers'),
        ];

        $upcoming_flights = Flight::with(['airline', 'airplane', 'departureAirport', 'arrivalAirport'])
            ->where('departure_time', '>=', now())
            ->orderBy('departure_time')
            ->take(5)
            ->get();

        return view('staff.dashboard.index', compact('stats', 'upcoming_flights'));
    }
}
