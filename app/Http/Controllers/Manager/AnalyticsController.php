<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\Airline;
use App\Models\Passenger;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(): View
    {
        // Revenue Analytics
        $dailyRevenue = Booking::where('status', 'confirmed')
            ->whereDate('created_at', today())
            ->sum('total_price');

        $weeklyRevenue = Booking::where('status', 'confirmed')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('total_price');

        $monthlyRevenue = Booking::where('status', 'confirmed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_price');

        // Booking Analytics
        $totalBookings = Booking::count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();
        $successRate = $totalBookings > 0 ? round(($confirmedBookings / $totalBookings) * 100, 2) : 0;
        $cancellationRate = $totalBookings > 0 ? round(($cancelledBookings / $totalBookings) * 100, 2) : 0;

        // Occupancy Rate per Airline
        $airlines = Airline::withCount(['flights', 'airplanes'])->get();
        $occupancyData = [];
        foreach ($airlines as $airline) {
            $totalSeats = $airline->airplanes()->sum('capacity');
            $bookedSeats = Booking::whereHas('flight', function($q) use ($airline) {
                $q->where('airline_id', $airline->id);
            })->where('status', 'confirmed')->sum('total_passengers');
            $occupancyRate = $totalSeats > 0 ? round(($bookedSeats / $totalSeats) * 100, 2) : 0;
            $occupancyData[] = [
                'name' => $airline->name,
                'total_flights' => $airline->flights_count,
                'occupancy_rate' => $occupancyRate,
                'revenue' => Booking::whereHas('flight', function($q) use ($airline) {
                    $q->where('airline_id', $airline->id);
                })->where('status', 'confirmed')->sum('total_price'),
            ];
        }

        // Top Routes
        $topRoutes = Flight::selectRaw('CONCAT(departure_airport_id, "-", arrival_airport_id) as route, COUNT(*) as total')
            ->groupBy('route')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // Monthly revenue chart data
        $monthlyRevenueData = Booking::selectRaw('MONTH(created_at) as month, SUM(total_price) as total')
            ->where('status', 'confirmed')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month');

        return view('manager.analytics', compact(
            'dailyRevenue', 'weeklyRevenue', 'monthlyRevenue',
            'totalBookings', 'confirmedBookings', 'cancelledBookings',
            'successRate', 'cancellationRate',
            'occupancyData', 'topRoutes', 'monthlyRevenueData'
        ));
    }
}