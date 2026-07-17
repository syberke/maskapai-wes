<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class EticketController extends Controller
{
    public function show(Booking $booking): View
    {
        $this->authorizeTicket($booking);
        $this->loadTicket($booking);

        return view('customer.bookings.eticket-v2', compact('booking'));
    }

    public function pdf(Booking $booking): Response
    {
        $this->authorizeTicket($booking);
        $this->loadTicket($booking);

        return Pdf::loadView('customer.bookings.eticket-pdf', compact('booking'))
            ->setPaper('a4', 'portrait')
            ->download('eticket-' . strtolower($booking->booking_code) . '.pdf');
    }

    private function authorizeTicket(Booking $booking): void
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if (! in_array($booking->status, ['paid', 'issued'], true)) {
            abort(403, 'E-Ticket is only available for confirmed bookings.');
        }
    }

    private function loadTicket(Booking $booking): void
    {
        $booking->load([
            'flight.departureAirport',
            'flight.arrivalAirport',
            'flight.airline',
            'flight.airplane',
            'passengers.seat',
            'payment',
        ]);
    }
}
