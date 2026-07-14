<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Flight;
use App\Models\Airline;
use App\Models\Airplane;
use App\Models\Airport;
use App\Models\Seat;
use App\Http\Requests\Admin\FlightRequest;
use Illuminate\Http\RedirectResponse;
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
            \Log::info('=== FLIGHT STORE REACHED ===', ['method' => $request->method(), 'url' => $request->fullUrl(), 'all' => $request->all()]);
            
            $validated = $request->validated();
            
            \Log::info('Flight store attempt', ['validated' => $validated]);
            
            // Auto-generate flight_number if not provided
            if (empty($validated['flight_number'])) {
                $validated['flight_number'] = $this->generateFlightNumber($validated['airline_id']);
            }
            
            // Auto-calculate cabin class prices if not provided
            $basePrice = $validated['price'];
            $airplane = Airplane::findOrFail($validated['airplane_id']);
            
            if (empty($validated['economy_class_price'])) {
                $validated['economy_class_price'] = $basePrice;
            }
            if (empty($validated['business_class_price'])) {
                $validated['business_class_price'] = round($basePrice * 2.33);
            }
            if (empty($validated['first_class_price'])) {
                $validated['first_class_price'] = round($basePrice * 4.33);
            }
            
            \Log::info('Creating flight with data', $validated);
            
            $flight = Flight::create($validated);
            
            \Log::info('Flight created', ['id' => $flight->id, 'flight_number' => $flight->flight_number]);
            
            // Auto-generate seats for this flight based on airplane config
            $this->generateFlightSeats($flight, $airplane);
            
            return redirect()->route('admin.flights.index')->with('success', 'Jadwal penerbangan berhasil dirilis dengan ' . $airplane->getTotalSeatsCount() . ' kursi.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Flight validation failed', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Flight creation failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal menyimpan penerbangan: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Auto-generate seats for a flight based on airplane cabin config
     */
    private function generateFlightSeats(Flight $flight, Airplane $airplane): void
    {
        $cabinConfig = $airplane->getCabinConfig();
        $priceConfig = [
            'first' => $flight->first_class_price,
            'business' => $flight->business_class_price,
            'economy' => $flight->economy_class_price,
        ];
        
        foreach ($cabinConfig as $class => $config) {
            list($startRow, $endRow) = $config['rows'];
            $letters = $config['letters'];
            
            for ($row = $startRow; $row <= $endRow; $row++) {
                foreach ($letters as $letter) {
                    $seatNumber = $row . $letter;
                    
                    // Check if seat already exists (for updates)
                    Seat::firstOrCreate(
                        [
                            'airplane_id' => $flight->airplane_id,
                            'seat_number' => $seatNumber,
                        ],
                        [
                            'class' => $class,
                            'status' => 'available',
                        ]
                    );
                }
            }
        }
        
        \Log::info("Seats generated for flight {$flight->flight_number} from airplane {$airplane->model}");
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
        $validated = $request->validated();
        
        // Auto-generate flight_number if not provided
        if (empty($validated['flight_number'])) {
            $validated['flight_number'] = $this->generateFlightNumber($validated['airline_id']);
        }
        
        $flight->update($validated);
        return redirect()->route('admin.flights.index')->with('success', 'Jadwal penerbangan berhasil disunting.');
    }

    public function destroy(Flight $flight): RedirectResponse
    {
        $flight->delete();
        return redirect()->route('admin.flights.index')->with('success', 'Jadwal penerbangan berhasil dihapus.');
    }

    /**
     * Generate unique flight number based on airline code
     * Example: GA101, JT610, ID712
     */
    private function generateFlightNumber(int $airlineId): string
    {
        $airline = Airline::findOrFail($airlineId);
        $airlineCode = strtoupper(substr($airline->name, 0, 2));
        
        // Find the highest flight number for this airline
        $lastFlight = Flight::where('flight_number', 'like', $airlineCode . '%')
                            ->orderByRaw('CAST(SUBSTRING(flight_number, 3) AS UNSIGNED) DESC')
                            ->first();
        
        if ($lastFlight) {
            $lastNumber = (int) substr($lastFlight->flight_number, 2);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 101; // Start from 101
        }
        
        return $airlineCode . $newNumber;
    }
}