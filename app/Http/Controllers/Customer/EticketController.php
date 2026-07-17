<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class EticketController extends Controller
{
    public function pdf(Booking $booking): Response
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if (! in_array($booking->status, ['paid', 'issued'], true)) {
            abort(403, 'E-Ticket is only available for confirmed bookings.');
        }

        $booking->load([
            'flight.departureAirport',
            'flight.arrivalAirport',
            'flight.airline',
            'flight.airplane',
            'passengers.seat',
            'payment',
        ]);

        return Pdf::loadView('customer.bookings.eticket-pdf', compact('booking'))
            ->setPaper('a4', 'portrait')
            ->download('eticket-' . strtolower($booking->booking_code) . '.pdf');
    }
}
