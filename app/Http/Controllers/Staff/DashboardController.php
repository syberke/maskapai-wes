<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Flight;
use App\Models\Booking;
use App\Models\Passenger;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'flights_today' => Flight::whereDate('departure_time', today())->count(),
            'active_flights' => Flight::where('departure_time', '>=', now())
                ->where('departure_time', '<=', now()->addHours(6))
                ->count(),
            'total_passengers_today' => Passenger::whereHas('booking.flight', function($q) {
                $q->whereDate('departure_time', today());
            })->count(),
            'boarding_count' => Booking::whereHas('flight', function($q) {
                $q->whereDate('departure_time', today());
            })->where('status', 'confirmed')->count(),
        ];

        $upcoming_flights = Flight::with(['airline', 'airplane', 'departureAirport', 'arrivalAirport'])
            ->where('departure_time', '>=', now())
            ->orderBy('departure_time')
            ->take(5)
            ->get();

        return view('staff.dashboard.index', compact('stats', 'upcoming_flights'));
    }
}