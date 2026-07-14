<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airplane;
use App\Models\Airline;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AirplaneController extends Controller
{
    public function index(): View
    {
        $airplanes = Airplane::with('airline')->withCount('seats')->latest()->paginate(10);
        return view('admin.airplanes.index', compact('airplanes'));
    }

    public function create(): View
    {
        $airlines = Airline::all();
        return view('admin.airplanes.create', compact('airlines'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'airline_id' => ['required', 'exists:airlines,id'],
            'model' => ['required', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'photos' => ['nullable', 'url'],
        ]);

        $airplane = Airplane::create($validated);
        
        // Auto-generate seats based on aircraft type
        $this->generateSeats($airplane);
        
        return redirect()->route('admin.airplanes.index')->with('success', 'Pesawat berhasil ditambahkan dengan ' . $this->getSeatCount($airplane->model) . ' kursi.');
    }

    public function edit(Airplane $airplane): View
    {
        $airlines = Airline::all();
        return view('admin.airplanes.edit', compact('airplane', 'airlines'));
    }

    public function update(Request $request, Airplane $airplane): RedirectResponse
    {
        $validated = $request->validate([
            'airline_id' => ['required', 'exists:airlines,id'],
            'model' => ['required', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'photos' => ['nullable', 'url'],
        ]);

        $airplane->update($validated);
        
        // Regenerate seats if model changed
        if ($airplane->wasChanged('model')) {
            Seat::where('airplane_id', $airplane->id)->delete();
            $this->generateSeats($airplane);
        }
        
        return redirect()->route('admin.airplanes.index')->with('success', 'Pesawat berhasil diperbarui.');
    }

    public function destroy(Airplane $airplane): RedirectResponse
    {
        $airplane->delete();
        return redirect()->route('admin.airplanes.index')->with('success', 'Pesawat berhasil dihapus.');
    }

    /**
     * Generate seats for an airplane based on aircraft type
     */
    private function generateSeats(Airplane $airplane): void
    {
        $seatConfig = $this->getSeatConfiguration($airplane->model);
        $seats = [];
        
        foreach ($seatConfig as $class => $config) {
            for ($row = $config['start_row']; $row <= $config['end_row']; $row++) {
                foreach ($config['seats_per_row'] as $seatLetter) {
                    $seats[] = [
                        'airplane_id' => $airplane->id,
                        'seat_number' => $row . $seatLetter,
                        'class' => $class,
                        'status' => 'available',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }
        
        Seat::insert($seats);
    }
    
    /**
     * Get seat configuration based on aircraft model
     */
    private function getSeatConfiguration(string $model): array
    {
        $model = strtolower($model);
        
        // Boeing 737-800NG: 184 seats
        if (str_contains($model, '737') || str_contains($model, 'b737')) {
            return [
                'first' => ['start_row' => 1, 'end_row' => 2, 'seats_per_row' => ['A', 'B']],
                'business' => ['start_row' => 3, 'end_row' => 5, 'seats_per_row' => ['A', 'B', 'C', 'D']],
                'economy' => ['start_row' => 6, 'end_row' => 33, 'seats_per_row' => ['A', 'B', 'C', 'D', 'E', 'F']],
            ];
        }
        
        // Airbus A320: 180 seats
        if (str_contains($model, 'a320') || str_contains($model, 'a-320')) {
            return [
                'business' => ['start_row' => 1, 'end_row' => 3, 'seats_per_row' => ['A', 'C', 'D', 'F']],
                'economy' => ['start_row' => 4, 'end_row' => 30, 'seats_per_row' => ['A', 'B', 'C', 'D', 'E', 'F']],
            ];
        }
        
        // Airbus A330: 300 seats
        if (str_contains($model, 'a330') || str_contains($model, 'a-330')) {
            return [
                'first' => ['start_row' => 1, 'end_row' => 2, 'seats_per_row' => ['A', 'B', 'C', 'D']],
                'business' => ['start_row' => 3, 'end_row' => 8, 'seats_per_row' => ['A', 'C', 'D', 'F']],
                'economy' => ['start_row' => 9, 'end_row' => 45, 'seats_per_row' => ['A', 'B', 'C', 'D', 'E', 'F']],
            ];
        }
        
        // Boeing 777: 350 seats
        if (str_contains($model, '777') || str_contains($model, 'b777')) {
            return [
                'first' => ['start_row' => 1, 'end_row' => 3, 'seats_per_row' => ['A', 'B', 'C', 'D', 'E', 'F']],
                'business' => ['start_row' => 4, 'end_row' => 12, 'seats_per_row' => ['A', 'C', 'D', 'F']],
                'economy' => ['start_row' => 13, 'end_row' => 55, 'seats_per_row' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H']],
            ];
        }
        
        // Airbus A350: 300 seats
        if (str_contains($model, 'a350') || str_contains($model, 'a-350')) {
            return [
                'first' => ['start_row' => 1, 'end_row' => 2, 'seats_per_row' => ['A', 'B', 'C', 'D']],
                'business' => ['start_row' => 3, 'end_row' => 10, 'seats_per_row' => ['A', 'C', 'D', 'F']],
                'economy' => ['start_row' => 11, 'end_row' => 45, 'seats_per_row' => ['A', 'B', 'C', 'D', 'E', 'F']],
            ];
        }
        
        // Default: Boeing 737-800NG configuration
        return [
            'first' => ['start_row' => 1, 'end_row' => 2, 'seats_per_row' => ['A', 'B']],
            'business' => ['start_row' => 3, 'end_row' => 5, 'seats_per_row' => ['A', 'B', 'C', 'D']],
            'economy' => ['start_row' => 6, 'end_row' => 33, 'seats_per_row' => ['A', 'B', 'C', 'D', 'E', 'F']],
        ];
    }
    
    /**
     * Get total seat count for a model
     */
    private function getSeatCount(string $model): int
    {
        $config = $this->getSeatConfiguration($model);
        $total = 0;
        
        foreach ($config as $classConfig) {
            $rows = $classConfig['end_row'] - $classConfig['start_row'] + 1;
            $total += $rows * count($classConfig['seats_per_row']);
        }
        
        return $total;
    }
}
