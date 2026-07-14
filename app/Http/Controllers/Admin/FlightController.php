<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FlightRequest;
use App\Models\Airline;
use App\Models\Airplane;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\Seat;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FlightController extends Controller
{
    public function index(): View
    {
        $flights = Flight::with(['airline', 'airplane', 'departureAirport', 'arrivalAirport'])
            ->latest()
            ->paginate(10);

        return view('admin.flights.index', compact('flights'));
    }

    public function create(): View
    {
        $airlines = Airline::all();
        $airplanes = Airplane::all();
        $airports = Airport::all();

        return view('admin.flights.create', compact('airlines', 'airplanes', 'airports'));
    }

    public function store(FlightRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $airplane = Airplane::findOrFail($validated['airplane_id']);

            if (empty($validated['flight_number'])) {
                $validated['flight_number'] = $this->generateFlightNumber((int) $validated['airline_id']);
            }

            $basePrice = (float) $validated['price'];
            $validated['economy_class_price'] = $validated['economy_class_price'] ?? $basePrice;
            $validated['business_class_price'] = $validated['business_class_price'] ?? round($basePrice * 2.33);
            $validated['first_class_price'] = $validated['first_class_price'] ?? round($basePrice * 4.33);
            $validated['available_seats'] = $airplane->getTotalSeatsCount();
            $validated['flight_duration'] = $this->calculateFlightDuration(
                $validated['departure_time'],
                $validated['arrival_time']
            );

            DB::transaction(function () use ($validated, $airplane): void {
                $flight = Flight::create($validated);
                $this->generateFlightSeats($flight, $airplane);
            });

            return redirect()
                ->route('admin.flights.index')
                ->with('success', 'Jadwal penerbangan berhasil dirilis dengan ' . $airplane->getTotalSeatsCount() . ' kursi.');
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', 'Gagal menyimpan penerbangan: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function generateFlightSeats(Flight $flight, Airplane $airplane): void
    {
        $cabinConfig = $airplane->getCabinConfig();

        foreach ($cabinConfig as $class => $config) {
            [$startRow, $endRow] = $config['rows'];

            for ($row = $startRow; $row <= $endRow; $row++) {
                foreach ($config['letters'] as $letter) {
                    Seat::firstOrCreate(
                        [
                            'airplane_id' => $flight->airplane_id,
                            'seat_number' => $row . $letter,
                        ],
                        [
                            'class' => $class,
                            'status' => 'available',
                        ]
                    );
                }
            }
        }
    }

    public function edit(Flight $flight): View
    {
        $airlines = Airline::all();
        $airplanes = Airplane::where('airline_id', $flight->airline_id)->get();
        $airports = Airport::all();

        return view('admin.flights.edit', compact('flight', 'airlines', 'airplanes', 'airports'));
    }

    public function update(FlightRequest $request, Flight $flight): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $airplane = Airplane::findOrFail($validated['airplane_id']);

            if (empty($validated['flight_number'])) {
                $validated['flight_number'] = $this->generateFlightNumber((int) $validated['airline_id']);
            }

            $basePrice = (float) $validated['price'];
            $validated['economy_class_price'] = $validated['economy_class_price'] ?? $basePrice;
            $validated['business_class_price'] = $validated['business_class_price'] ?? round($basePrice * 2.33);
            $validated['first_class_price'] = $validated['first_class_price'] ?? round($basePrice * 4.33);
            $validated['available_seats'] = $airplane->getTotalSeatsCount();
            $validated['flight_duration'] = $this->calculateFlightDuration(
                $validated['departure_time'],
                $validated['arrival_time']
            );

            $flight->update($validated);

            return redirect()
                ->route('admin.flights.index')
                ->with('success', 'Jadwal penerbangan berhasil disunting.');
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', 'Gagal memperbarui penerbangan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Flight $flight): RedirectResponse
    {
        $flight->delete();

        return redirect()
            ->route('admin.flights.index')
            ->with('success', 'Jadwal penerbangan berhasil dihapus.');
    }

    private function generateFlightNumber(int $airlineId): string
    {
        $airline = Airline::findOrFail($airlineId);
        $airlineCode = strtoupper(substr($airline->name, 0, 2));

        $lastNumber = Flight::where('flight_number', 'like', $airlineCode . '%')
            ->pluck('flight_number')
            ->map(function (string $flightNumber) use ($airlineCode): ?int {
                $suffix = substr(strtoupper($flightNumber), strlen($airlineCode));

                return ctype_digit($suffix) ? (int) $suffix : null;
            })
            ->filter(fn (?int $number): bool => $number !== null)
            ->max();

        return $airlineCode . (($lastNumber ?? 100) + 1);
    }

    private function calculateFlightDuration(string $departureTime, string $arrivalTime): string
    {
        $departure = Carbon::parse($departureTime);
        $arrival = Carbon::parse($arrivalTime);
        $totalMinutes = (int) $departure->diffInMinutes($arrival);

        return intdiv($totalMinutes, 60) . ' Jam ' . ($totalMinutes % 60) . ' Menit';
    }
}
