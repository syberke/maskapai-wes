<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Airline;
use App\Models\Booking;
use App\Models\Flight;
use App\Support\SqlDateExpression;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    private const COMPLETED = ['paid', 'issued'];

    public function index(): View
    {
        $completed = fn () => Booking::whereIn('status', self::COMPLETED);
        $dailyRevenue = $completed()->whereDate('created_at', today())->sum('total_price');
        $weeklyRevenue = $completed()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_price');
        $monthlyRevenue = $completed()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_price');

        $totalBookings = Booking::count();
        $confirmedBookings = Booking::whereIn('status', self::COMPLETED)->count();
        $cancelledBookings = Booking::whereIn('status', ['cancelled', 'refunded'])->count();
        $successRate = $totalBookings > 0 ? round(($confirmedBookings / $totalBookings) * 100, 2) : 0;
        $cancellationRate = $totalBookings > 0 ? round(($cancelledBookings / $totalBookings) * 100, 2) : 0;

        $occupancyData = Airline::withCount(['flights', 'airplanes'])->get()->map(function (Airline $airline): array {
            $totalSeats = $airline->airplanes()->sum('capacity');
            $completed = Booking::whereHas('flight', fn ($query) => $query->where('airline_id', $airline->id))
                ->whereIn('status', self::COMPLETED);
            $bookedSeats = (clone $completed)->sum('total_passengers');

            return [
                'name' => $airline->name,
                'total_flights' => $airline->flights_count,
                'occupancy_rate' => $totalSeats > 0 ? round(($bookedSeats / $totalSeats) * 100, 2) : 0,
                'revenue' => (clone $completed)->sum('total_price'),
            ];
        })->all();

        $topRoutes = Flight::selectRaw('departure_airport_id, arrival_airport_id, COUNT(*) as total')
            ->groupBy('departure_airport_id', 'arrival_airport_id')
            ->orderByDesc('total')->take(5)->get()
            ->each(fn ($route) => $route->route = $route->departure_airport_id . '-' . $route->arrival_airport_id);

        $monthExpression = SqlDateExpression::month();
        $monthlyRevenueData = Booking::selectRaw("{$monthExpression} as month, SUM(total_price) as total")
            ->whereIn('status', self::COMPLETED)
            ->whereYear('created_at', date('Y'))
            ->groupByRaw($monthExpression)
            ->pluck('total', 'month');

        return view('manager.analytics', compact(
            'dailyRevenue', 'weeklyRevenue', 'monthlyRevenue',
            'totalBookings', 'confirmedBookings', 'cancelledBookings',
            'successRate', 'cancellationRate',
            'occupancyData', 'topRoutes', 'monthlyRevenueData'
        ));
    }
}
