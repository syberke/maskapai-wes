<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Flight;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FlightMonitoringController extends Controller
{
    public function index(): View
    {
        $flights = Flight::with(['airline', 'airplane', 'departureAirport', 'arrivalAirport'])
            ->orderBy('departure_time')
            ->paginate(10);

        return view('staff.flights.index', compact('flights'));
    }
}