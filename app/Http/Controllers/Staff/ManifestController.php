<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Flight;
use App\Models\Passenger;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManifestController extends Controller
{
    public function index(): View
    {
        $flights = Flight::with(['airline', 'departureAirport', 'arrivalAirport'])
            ->where('departure_time', '>=', now()->subHours(6))
            ->orderBy('departure_time')
            ->paginate(10);

        return view('staff.manifest.index', compact('flights'));
    }

    public function show(Flight $flight): View
    {
        $flight->load(['airline', 'airplane', 'departureAirport', 'arrivalAirport', 
            'bookings.passengers', 'bookings.user']);
        
        $passengers = Passenger::whereHas('booking', function($q) use ($flight) {
            $q->where('flight_id', $flight->id);
        })->with('booking.user')->get();

        return view('staff.manifest.show', compact('flight', 'passengers'));
    }
}