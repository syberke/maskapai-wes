<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\Airline;
use App\Models\Passenger;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function revenue(): View
    {
        $daily = Booking::where('status', 'confirmed')->whereDate('created_at', today())->sum('total_price');
        $weekly = Booking::where('status', 'confirmed')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_price');
        $monthly = Booking::where('status', 'confirmed')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_price');
        $yearly = Booking::where('status', 'confirmed')->whereYear('created_at', now()->year)->sum('total_price');

        $monthlyData = Booking::selectRaw('MONTH(created_at) as month, SUM(total_price) as total')
            ->where('status', 'confirmed')->whereYear('created_at', date('Y'))->groupBy('month')->pluck('total', 'month');

        $recentTransactions = Booking::with(['flight.departureAirport', 'flight.arrivalAirport', 'user'])
            ->where('status', 'confirmed')->latest()->take(10)->get();

        return view('manager.reports.revenue', compact('daily', 'weekly', 'monthly', 'yearly', 'monthlyData', 'recentTransactions'));
    }

    public function bookings(): View
    {
        $total = Booking::count();
        $confirmed = Booking::where('status', 'confirmed')->count();
        $pending = Booking::where('status', 'pending')->count();
        $cancelled = Booking::where('status', 'cancelled')->count();
        
        $monthlyBookings = Booking::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', date('Y'))->groupBy('month')->pluck('total', 'month');

        $recentBookings = Booking::with(['flight.departureAirport', 'flight.arrivalAirport', 'user'])->latest()->take(15)->get();

        return view('manager.reports.bookings', compact('total', 'confirmed', 'pending', 'cancelled', 'monthlyBookings', 'recentBookings'));
    }

    public function passengers(): View
    {
        $totalPassengers = Passenger::count();
        $passengersToday = Passenger::whereHas('booking.flight', fn($q) => $q->whereDate('departure_time', today()))->count();
        $male = Passenger::where('gender', 'L')->count();
        $female = Passenger::where('gender', 'P')->count();

        $topPassengers = Passenger::selectRaw('full_name, COUNT(*) as total')
            ->groupBy('full_name')->orderByDesc('total')->take(10)->get();

        return view('manager.reports.passengers', compact('totalPassengers', 'passengersToday', 'male', 'female', 'topPassengers'));
    }

    public function occupancy(): View
    {
        $airlines = Airline::withCount('flights')->get();
        $occupancyData = [];
        foreach ($airlines as $al) {
            $totalSeats = $al->airplanes()->sum('capacity') ?: 1;
            $booked = Booking::whereHas('flight', fn($q) => $q->where('airline_id', $al->id))
                ->where('status', 'confirmed')->sum('total_passengers');
            $occupancyData[] = [
                'name' => $al->name,
                'total_flights' => $al->flights_count,
                'occupancy_rate' => round(($booked / $totalSeats) * 100, 2),
                'revenue' => Booking::whereHas('flight', fn($q) => $q->where('airline_id', $al->id))
                    ->where('status', 'confirmed')->sum('total_price'),
            ];
        }
        return view('manager.reports.occupancy', compact('occupancyData'));
    }

    public function airlinePerformance(): View
    {
        $airlines = Airline::withCount('flights')->get();
        $performance = [];
        foreach ($airlines as $al) {
            $flights = Flight::where('airline_id', $al->id)->count();
            $revenue = Booking::whereHas('flight', fn($q) => $q->where('airline_id', $al->id))
                ->where('status', 'confirmed')->sum('total_price');
            $bookings = Booking::whereHas('flight', fn($q) => $q->where('airline_id', $al->id))->count();
            $performance[] = [
                'name' => $al->name,
                'code' => $al->code,
                'total_flights' => $flights,
                'total_bookings' => $bookings,
                'revenue' => $revenue,
            ];
        }
        return view('manager.reports.airline-performance', compact('performance'));
    }

    public function routePerformance(): View
    {
        $routes = Flight::selectRaw('CONCAT(departure_airport_id, "-", arrival_airport_id) as route_code, departure_airport_id, arrival_airport_id, COUNT(*) as total_flights')
            ->groupBy('route_code', 'departure_airport_id', 'arrival_airport_id')
            ->orderByDesc('total_flights')
            ->get()
            ->map(function ($item) {
                $dep = \App\Models\Airport::find($item->departure_airport_id);
                $arr = \App\Models\Airport::find($item->arrival_airport_id);
                $item->route_name = ($dep ? $dep->iata_code : '?') . ' → ' . ($arr ? $arr->iata_code : '?');
                $item->total_bookings = Booking::whereHas('flight', fn($q) => $q->where('departure_airport_id', $item->departure_airport_id)->where('arrival_airport_id', $item->arrival_airport_id))->count();
                $item->revenue = Booking::whereHas('flight', fn($q) => $q->where('departure_airport_id', $item->departure_airport_id)->where('arrival_airport_id', $item->arrival_airport_id))->where('status', 'confirmed')->sum('total_price');
                return $item;
            });

        return view('manager.reports.route-performance', compact('routes'));
    }
}