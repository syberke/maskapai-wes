<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\Passenger;
use App\Models\Payment;
use App\Models\Seat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookingController extends Controller
{
    private const MAX_PASSENGERS = 5;

    private const ACTIVE_BOOKING_STATUSES = ['pending', 'paid', 'issued'];

    public function selectSeats(Flight $flight): View|RedirectResponse
    {
        $searchParams = session('search_params', []);
        $cabinClass = $searchParams['class'] ?? 'economy';
        $passengerCount = (int) ($searchParams['passengers'] ?? 1);

        if ($passengerCount < 1 || $passengerCount > self::MAX_PASSENGERS) {
            return redirect()->route('homepage')
                ->with('error', 'Jumlah penumpang harus antara 1 sampai 5 orang.');
        }

        $classPrice = $flight->getPriceForClass($cabinClass);
        $reserved = $this->reservedSeatsForFlight($flight);

        $firstClassSeats = $this->seatsForClass($flight, 'first', $reserved);
        $businessClassSeats = $this->seatsForClass($flight, 'business', $reserved);
        $economyClassSeats = $this->seatsForClass($flight, 'economy', $reserved);

        return view('customer.bookings.seats', compact(
            'flight',
            'cabinClass',
            'passengerCount',
            'classPrice',
            'firstClassSeats',
            'businessClassSeats',
            'economyClassSeats'
        ));
    }

    public function passengerForm(Request $request, Flight $flight): View|RedirectResponse
    {
        $validated = $request->validate([
            'cabin_class' => ['required', 'in:economy,business,first'],
            'seats' => ['required', 'array', 'min:1', 'max:' . self::MAX_PASSENGERS],
            'seats.*' => ['required', 'integer', 'distinct', 'exists:seats,id'],
        ]);

        $expectedPassengers = (int) (session('search_params.passengers') ?? count($validated['seats']));

        if ($expectedPassengers < 1 || $expectedPassengers > self::MAX_PASSENGERS || count($validated['seats']) !== $expectedPassengers) {
            return back()->with('error', 'Pilih tepat ' . $expectedPassengers . ' kursi sesuai jumlah penumpang.');
        }

        $selectedSeats = Seat::query()
            ->whereIn('id', $validated['seats'])
            ->where('airplane_id', $flight->airplane_id)
            ->where('class', $validated['cabin_class'])
            ->orderBy('seat_number')
            ->get();

        if ($selectedSeats->count() !== $expectedPassengers) {
            return back()->with('error', 'Kursi tidak sesuai dengan pesawat atau kelas penerbangan yang dipilih.');
        }

        $reserved = $this->reservedSeatsForFlight($flight);
        $hasConflict = $selectedSeats->contains(fn (Seat $seat): bool =>
            in_array($seat->id, $reserved['ids'], true)
            || in_array($seat->seat_number, $reserved['numbers'], true)
        );

        if ($hasConflict) {
            return back()->with('error', 'Beberapa kursi sudah dipesan. Silakan pilih kursi lain.');
        }

        $passengerCount = $selectedSeats->count();
        $cabinClass = $validated['cabin_class'];
        $classPrice = $flight->getPriceForClass($cabinClass);

        return view('customer.bookings.passengers', compact(
            'flight',
            'selectedSeats',
            'passengerCount',
            'cabinClass',
            'classPrice'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'flight_id' => ['required', 'exists:flights,id'],
            'cabin_class' => ['required', 'in:economy,business,first'],
            'seats' => ['required', 'array', 'min:1', 'max:' . self::MAX_PASSENGERS],
            'seats.*' => ['required', 'integer', 'distinct', 'exists:seats,id'],
            'passengers' => ['required', 'array', 'min:1', 'max:' . self::MAX_PASSENGERS],
            'passengers.*.full_name' => ['required', 'string', 'max:255'],
            'passengers.*.email' => ['required', 'email', 'max:255'],
            'passengers.*.phone' => ['required', 'string', 'max:20'],
            'passengers.*.gender' => ['required', 'in:male,female,L,P'],
            'passengers.*.date_of_birth' => ['required', 'date', 'before:today'],
            'passengers.*.nationality' => ['required', 'string', 'max:100'],
            'passengers.*.passport_number' => ['nullable', 'string', 'max:50'],
            'passengers.*.emergency_contact' => ['nullable', 'string', 'max:100'],
            'passengers.*.emergency_phone' => ['nullable', 'string', 'max:20'],
        ]);

        $passengerCount = count($validated['passengers']);

        if ($passengerCount !== count($validated['seats'])) {
            throw ValidationException::withMessages([
                'passengers' => 'Jumlah data penumpang harus sama dengan jumlah kursi yang dipilih.',
            ]);
        }

        try {
            $booking = DB::transaction(function () use ($validated, $passengerCount): Booking {
                $flight = Flight::query()->lockForUpdate()->findOrFail($validated['flight_id']);

                if ($flight->available_seats < $passengerCount) {
                    throw ValidationException::withMessages([
                        'seats' => 'Kursi penerbangan tidak mencukupi untuk jumlah penumpang.',
                    ]);
                }

                $seats = Seat::query()
                    ->whereIn('id', $validated['seats'])
                    ->where('airplane_id', $flight->airplane_id)
                    ->where('class', $validated['cabin_class'])
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                if ($seats->count() !== $passengerCount) {
                    throw ValidationException::withMessages([
                        'seats' => 'Kursi tidak valid untuk pesawat atau kelas penerbangan ini.',
                    ]);
                }

                $seatIds = array_map('intval', $validated['seats']);
                $seatNumbers = $seats->pluck('seat_number')->all();

                $conflictQuery = Passenger::query()
                    ->whereHas('booking', function ($query) use ($flight) {
                        $query->where('flight_id', $flight->id)
                            ->whereIn('status', self::ACTIVE_BOOKING_STATUSES);
                    })
                    ->where(function ($query) use ($seatIds, $seatNumbers) {
                        if (Schema::hasColumn('passengers', 'seat_id')) {
                            $query->whereIn('seat_id', $seatIds)
                                ->orWhereIn('seat_number', $seatNumbers);
                        } else {
                            $query->whereIn('seat_number', $seatNumbers);
                        }
                    });

                if ($conflictQuery->exists()) {
                    throw ValidationException::withMessages([
                        'seats' => 'Salah satu kursi baru saja dipesan oleh pengguna lain.',
                    ]);
                }

                $classPrice = $flight->getPriceForClass($validated['cabin_class']);
                $totalPrice = $classPrice * $passengerCount;

                $booking = Booking::create([
                    'user_id' => auth()->id(),
                    'flight_id' => $flight->id,
                    'booking_code' => $this->generateBookingCode(),
                    'cabin_class' => $validated['cabin_class'],
                    'total_passengers' => $passengerCount,
                    'total_price' => $totalPrice,
                    'status' => 'pending',
                ]);

                foreach (array_values($validated['passengers']) as $index => $passenger) {
                    $seat = $seats->get((int) $validated['seats'][$index]);
                    $payload = [
                        'booking_id' => $booking->id,
                        'full_name' => $passenger['full_name'],
                        'gender' => $this->normalizeGender($passenger['gender']),
                        'birth_date' => $passenger['date_of_birth'],
                        'passport_number' => $passenger['passport_number'] ?? null,
                        'seat_number' => $seat->seat_number,
                    ];

                    $optionalColumns = [
                        'seat_id' => $seat->id,
                        'email' => $passenger['email'],
                        'phone' => $passenger['phone'],
                        'date_of_birth' => $passenger['date_of_birth'],
                        'nationality' => $passenger['nationality'],
                        'emergency_contact' => $passenger['emergency_contact'] ?? null,
                        'emergency_phone' => $passenger['emergency_phone'] ?? null,
                    ];

                    foreach ($optionalColumns as $column => $value) {
                        if (Schema::hasColumn('passengers', $column)) {
                            $payload[$column] = $value;
                        }
                    }

                    Passenger::create($payload);
                }

                $flight->decrement('available_seats', $passengerCount);

                Payment::create([
                    'booking_id' => $booking->id,
                    'amount' => $totalPrice,
                    'payment_status' => 'pending',
                    'transaction_code' => 'TRX-' . strtoupper(Str::random(10)),
                ]);

                return $booking;
            }, 3);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Pemesanan gagal disimpan. Tidak ada tiket atau kursi yang dicatat sebagian. ' . $exception->getMessage());
        }

        return redirect()->route('customer.payment.show', $booking)
            ->with('success', 'Pemesanan untuk ' . $passengerCount . ' penumpang berhasil. Silakan lanjutkan pembayaran.');
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

        $booking->load([
            'flight.departureAirport',
            'flight.arrivalAirport',
            'flight.airline',
            'flight.airplane',
            'passengers.seat',
            'payment',
        ]);

        return view('customer.bookings.show', compact('booking'));
    }

    private function seatsForClass(Flight $flight, string $class, array $reserved)
    {
        return Seat::query()
            ->where('airplane_id', $flight->airplane_id)
            ->where('class', $class)
            ->orderBy('seat_number')
            ->get()
            ->each(function (Seat $seat) use ($reserved): void {
                $seat->status = in_array($seat->id, $reserved['ids'], true)
                    || in_array($seat->seat_number, $reserved['numbers'], true)
                    ? 'booked'
                    : 'available';
            });
    }

    private function reservedSeatsForFlight(Flight $flight): array
    {
        $passengers = Passenger::query()
            ->whereHas('booking', function ($query) use ($flight) {
                $query->where('flight_id', $flight->id)
                    ->whereIn('status', self::ACTIVE_BOOKING_STATUSES);
            })
            ->get(['seat_id', 'seat_number']);

        return [
            'ids' => $passengers->pluck('seat_id')->filter()->map(fn ($id): int => (int) $id)->values()->all(),
            'numbers' => $passengers->pluck('seat_number')->filter()->values()->all(),
        ];
    }

    private function normalizeGender(string $gender): string
    {
        return in_array(strtolower($gender), ['male', 'l'], true) ? 'L' : 'P';
    }

    private function generateBookingCode(): string
    {
        do {
            $code = 'LXF-' . strtoupper(Str::random(8));
        } while (Booking::where('booking_code', $code)->exists());

        return $code;
    }
}
