<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Flight;
use App\Models\Booking;
use App\Models\Passenger;
use App\Models\Payment;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function selectSeats(Flight $flight): View
    {
        $searchParams = session('search_params', []);
        $cabinClass = $searchParams['class'] ?? 'economy';
        $passengerCount = $searchParams['passengers'] ?? 1;
        
        // Get seat configuration for this class
        $seatConfig = $flight->getSeatsPerClass($cabinClass);
        $classPrice = $flight->getPriceForClass($cabinClass);
        
        // Get all seats for this airplane grouped by class
        $firstClassSeats = Seat::where('airplane_id', $flight->airplane_id)
            ->where('class', 'first')
            ->orderBy('seat_number')
            ->get();
            
        $businessClassSeats = Seat::where('airplane_id', $flight->airplane_id)
            ->where('class', 'business')
            ->orderBy('seat_number')
            ->get();
            
        $economyClassSeats = Seat::where('airplane_id', $flight->airplane_id)
            ->where('class', 'economy')
            ->orderBy('seat_number')
            ->get();
        
        return view('customer.bookings.seats', compact(
            'flight', 'cabinClass', 'passengerCount', 'classPrice',
            'firstClassSeats', 'businessClassSeats', 'economyClassSeats'
        ));
    }

    public function passengerForm(Request $request, Flight $flight)
    {
        $selectedSeatIds = $request->input('seats', []);
        
        if (empty($selectedSeatIds)) {
            return back()->with('error', 'Silakan pilih kursi terlebih dahulu.');
        }

        $selectedSeats = Seat::whereIn('id', $selectedSeatIds)
                             ->where('status', 'available')
                             ->get();
        
        if ($selectedSeats->count() !== count($selectedSeatIds)) {
            return back()->with('error', 'Beberapa kursi sudah tidak tersedia. Silakan pilih kursi lain.');
        }
        
        $passengerCount = $selectedSeats->count();
        $cabinClass = $selectedSeats->first()->class;
        $classPrice = $flight->getPriceForClass($cabinClass);
        
        return view('customer.bookings.passengers', compact('flight', 'selectedSeats', 'passengerCount', 'cabinClass', 'classPrice'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'flight_id' => ['required', 'exists:flights,id'],
            'cabin_class' => ['required', 'in:economy,business,first'],
            'seats' => ['required', 'array', 'min:1'],
            'seats.*' => ['exists:seats,id'],
            'passengers' => ['required', 'array', 'min:1'],
            'passengers.*.full_name' => ['required', 'string', 'max:255'],
            'passengers.*.email' => ['required', 'email', 'max:255'],
            'passengers.*.phone' => ['required', 'string', 'max:20'],
            'passengers.*.gender' => ['required', 'in:male,female'],
            'passengers.*.date_of_birth' => ['required', 'date'],
            'passengers.*.nationality' => ['required', 'string', 'max:100'],
            'passengers.*.passport_number' => ['nullable', 'string', 'max:50'],
            'passengers.*.emergency_contact' => ['nullable', 'string', 'max:100'],
            'passengers.*.emergency_phone' => ['nullable', 'string', 'max:20'],
        ]);

        $flight = Flight::findOrFail($validated['flight_id']);
        $cabinClass = $validated['cabin_class'];
        $totalPassengers = count($validated['passengers']);
        
        // Calculate price based on cabin class
        $classPrice = $flight->getPriceForClass($cabinClass);
        $totalPrice = $classPrice * $totalPassengers;

        // Verify seats are still available AND belong to the correct cabin class
        $seats = Seat::whereIn('id', $validated['seats'])
                     ->where('status', 'available')
                     ->where('class', $cabinClass)
                     ->get();
        
        if ($seats->count() !== $totalPassengers) {
            return back()->with('error', 'Maaf, beberapa kursi sudah tidak tersedia atau tidak sesuai dengan kelas yang dipilih. Silakan coba lagi.');
        }

        // Generate unique booking code
        $bookingCode = 'LXF-' . strtoupper(Str::random(8));

        // Create booking with cabin class
        $booking = Booking::create([
            'user_id' => auth()->id(),
            'flight_id' => $flight->id,
            'booking_code' => $bookingCode,
            'cabin_class' => $cabinClass,
            'total_passengers' => $totalPassengers,
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        // Create passengers and mark seats as booked
        foreach ($validated['passengers'] as $index => $passengerData) {
            Passenger::create([
                'booking_id' => $booking->id,
                'seat_id' => $validated['seats'][$index],
                'full_name' => $passengerData['full_name'],
                'email' => $passengerData['email'],
                'phone' => $passengerData['phone'],
                'gender' => $passengerData['gender'],
                'date_of_birth' => $passengerData['date_of_birth'],
                'nationality' => $passengerData['nationality'],
                'passport_number' => $passengerData['passport_number'] ?? null,
                'emergency_contact' => $passengerData['emergency_contact'] ?? null,
                'emergency_phone' => $passengerData['emergency_phone'] ?? null,
            ]);

            // Mark seat as booked
            Seat::where('id', $validated['seats'][$index])
                ->update(['status' => 'booked']);
        }

        // Update available seats
        $flight->decrement('available_seats', $totalPassengers);

        // Create payment record
        Payment::create([
            'booking_id' => $booking->id,
            'amount' => $totalPrice,
            'payment_status' => 'pending',
            'transaction_code' => 'TRX-' . strtoupper(Str::random(10)),
        ]);

        return redirect()->route('customer.payment.show', $booking->id)
            ->with('success', 'Pemesanan berhasil! Silakan lanjutkan pembayaran.');
    }

    public function history(): View
    {
        $bookings = Booking::with(['flight.departureAirport', 'flight.arrivalAirport', 'flight.airline', 'payment'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('customer.bookings.history', compact('bookings'));
    }

    public function show(Booking $booking): View
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $booking->load(['flight.departureAirport', 'flight.arrivalAirport', 'flight.airline', 'flight.airplane', 'passengers.seat', 'payment']);
        return view('customer.bookings.show', compact('booking'));
    }
}