<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $upcomingBookings = Booking::with(['flight.departureAirport', 'flight.arrivalAirport', 'flight.airline'])
            ->where('user_id', auth()->id())
            ->whereHas('flight', fn($q) => $q->where('departure_time', '>=', now()))
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentBookings = Booking::with(['flight.departureAirport', 'flight.arrivalAirport', 'flight.airline'])
            ->where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total' => Booking::where('user_id', auth()->id())->count(),
            'confirmed' => Booking::where('user_id', auth()->id())->where('status', 'confirmed')->count(),
            'pending' => Booking::where('user_id', auth()->id())->where('status', 'pending')->count(),
        ];

        return view('customer.dashboard.index', compact('upcomingBookings', 'recentBookings', 'stats'));
    }
}