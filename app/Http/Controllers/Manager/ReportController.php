<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Airline;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\Passenger;
use App\Support\SqlDateExpression;
use Illuminate\View\View;

class ReportController extends Controller
{
    private const COMPLETED = ['paid', 'issued'];

    public function revenue(): View
    {
        $completed = fn () => Booking::whereIn('status', self::COMPLETED);
        $daily = $completed()->whereDate('created_at', today())->sum('total_price');
        $weekly = $completed()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_price');
        $monthly = $completed()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_price');
        $yearly = $completed()->whereYear('created_at', now()->year)->sum('total_price');
        $monthExpression = SqlDateExpression::month();
        $monthlyData = Booking::selectRaw("{$monthExpression} as month, SUM(total_price) as total")
            ->whereIn('status', self::COMPLETED)->whereYear('created_at', date('Y'))
            ->groupByRaw($monthExpression)->pluck('total', 'month');
        $recentTransactions = Booking::with(['flight.departureAirport', 'flight.arrivalAirport', 'user'])
            ->whereIn('status', self::COMPLETED)->latest()->take(10)->get();

        return view('manager.reports.revenue', compact('daily', 'weekly', 'monthly', 'yearly', 'monthlyData', 'recentTransactions'));
    }

    public function bookings(): View
    {
        $total = Booking::count();
        $confirmed = Booking::whereIn('status', self::COMPLETED)->count();
        $pending = Booking::where('status', 'pending')->count();
        $cancelled = Booking::whereIn('status', ['cancelled', 'refunded'])->count();
        $monthExpression = SqlDateExpression::month();
        $monthlyBookings = Booking::selectRaw("{$monthExpression} as month, COUNT(*) as total")
            ->whereYear('created_at', date('Y'))->groupByRaw($monthExpression)->pluck('total', 'month');
        $recentBookings = Booking::with(['flight.departureAirport', 'flight.arrivalAirport', 'user'])->latest()->take(15)->get();

        return view('manager.reports.bookings', compact('total', 'confirmed', 'pending', 'cancelled', 'monthlyBookings', 'recentBookings'));
    }

    public function passengers(): View
    {
        $totalPassengers = Passenger::count();
        $passengersToday = Passenger::whereHas('booking.flight', fn ($q) => $q->whereDate('departure_time', today()))->count();
        $male = Passenger::where('gender', 'L')->count();
        $female = Passenger::where('gender', 'P')->count();
        $topPassengers = Passenger::selectRaw('full_name, COUNT(*) as total')->groupBy('full_name')->orderByDesc('total')->take(10)->get();

        return view('manager.reports.passengers', compact('totalPassengers', 'passengersToday', 'male', 'female', 'topPassengers'));
    }

    public function occupancy(): View
    {
        $occupancyData = Airline::withCount('flights')->get()->map(function (Airline $airline): array {
            $totalSeats = $airline->airplanes()->sum('capacity') ?: 1;
            $completed = Booking::whereHas('flight', fn ($q) => $q->where('airline_id', $airline->id))->whereIn('status', self::COMPLETED);

            return [
                'name' => $airline->name,
                'total_flights' => $airline->flights_count,
                'occupancy_rate' => round(((clone $completed)->sum('total_passengers') / $totalSeats) * 100, 2),
                'revenue' => (clone $completed)->sum('total_price'),
            ];
        })->all();

        return view('manager.reports.occupancy', compact('occupancyData'));
    }

    public function airlinePerformance(): View
    {
        $performance = Airline::withCount('flights')->get()->map(function (Airline $airline): array {
            $bookings = Booking::whereHas('flight', fn ($q) => $q->where('airline_id', $airline->id));

            return [
                'name' => $airline->name,
                'code' => $airline->code,
                'total_flights' => $airline->flights_count,
                'total_bookings' => (clone $bookings)->count(),
                'revenue' => (clone $bookings)->whereIn('status', self::COMPLETED)->sum('total_price'),
            ];
        })->all();

        return view('manager.reports.airline-performance', compact('performance'));
    }

    public function routePerformance(): View
    {
        $routes = Flight::selectRaw('departure_airport_id, arrival_airport_id, COUNT(*) as total_flights')
            ->groupBy('departure_airport_id', 'arrival_airport_id')->orderByDesc('total_flights')->get()
            ->map(function ($route) {
                $dep = \App\Models\Airport::find($route->departure_airport_id);
                $arr = \App\Models\Airport::find($route->arrival_airport_id);
                $bookings = Booking::whereHas('flight', fn ($q) => $q->where('departure_airport_id', $route->departure_airport_id)->where('arrival_airport_id', $route->arrival_airport_id));
                $route->route_name = ($dep?->iata_code ?? '?') . ' → ' . ($arr?->iata_code ?? '?');
                $route->total_bookings = (clone $bookings)->count();
                $route->revenue = (clone $bookings)->whereIn('status', self::COMPLETED)->sum('total_price');
                return $route;
            });

        return view('manager.reports.route-performance', compact('routes'));
    }
}
