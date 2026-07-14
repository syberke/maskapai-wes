<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\User;
use App\Models\Airline;
use App\Models\Airport;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_flights' => Flight::count(),
            'total_bookings' => Booking::count(),
            'total_users' => User::where('role', 'customer')->count(),
            'total_airlines' => Airline::count(),
            'total_airports' => Airport::count(),
            'total_revenue' => Booking::where('status', 'confirmed')->sum('total_price'),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
        ];

        $recent_bookings = Booking::with(['user', 'flight.departureAirport', 'flight.arrivalAirport'])
            ->latest()
            ->take(10)
            ->get();

        $monthly_revenue = Booking::selectRaw('MONTH(created_at) as month, SUM(total_price) as total')
            ->where('status', 'confirmed')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return view('admin.dashboard', compact('stats', 'recent_bookings', 'monthly_revenue'));
    }
}