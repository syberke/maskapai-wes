<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seat;
use App\Models\Airplane;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class SeatController extends Controller
{
    public function index(): View
    {
        $seats = Seat::with('airplane.airline')->latest()->paginate(20);
        $airplanes = Airplane::with('airline')->get();
        return view('admin.seats.index', compact('seats', 'airplanes'));
    }

    public function create(): View
    {
        $airplanes = Airplane::with('airline')->get();
        return view('admin.seats.create', compact('airplanes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'airplane_id' => ['required', 'exists:airplanes,id'],
            'seat_number' => ['required', 'string', 'max:10'],
            'class' => ['required', 'in:economy,business,first'],
            'status' => ['required', 'in:available,booked'],
        ]);

        Seat::create($validated);
        return redirect()->route('admin.seats.index')->with('success', 'Kursi berhasil ditambahkan.');
    }

    public function edit(Seat $seat): View
    {
        $airplanes = Airplane::with('airline')->get();
        return view('admin.seats.edit', compact('seat', 'airplanes'));
    }

    public function update(Request $request, Seat $seat): RedirectResponse
    {
        $validated = $request->validate([
            'airplane_id' => ['required', 'exists:airplanes,id'],
            'seat_number' => ['required', 'string', 'max:10'],
            'class' => ['required', 'in:economy,business,first'],
            'status' => ['required', 'in:available,booked'],
        ]);

        $seat->update($validated);
        return redirect()->route('admin.seats.index')->with('success', 'Kursi berhasil diperbarui.');
    }

    public function destroy(Seat $seat): RedirectResponse
    {
        $seat->delete();
        return redirect()->route('admin.seats.index')->with('success', 'Kursi berhasil dihapus.');
    }

    /**
     * Auto-generate 184 seats for a Boeing 737-800NG configuration.
     * First Class: rows 1-2 (2 seats/row = 4 seats)
     * Business: rows 3-5 (4 seats/row = 12 seats)
     * Economy: rows 6-33 (6 seats/row = 168 seats)
     * Total: 184 seats
     */
    public function generate(Request $request): RedirectResponse
    {
        $request->validate([
            'airplane_id' => ['required', 'exists:airplanes,id'],
        ]);

        $airplaneId = $request->airplane_id;
        $airplane = Airplane::findOrFail($airplaneId);

        // Check if seats already exist for this airplane
        if (Seat::where('airplane_id', $airplaneId)->exists()) {
            return redirect()->route('admin.seats.index')
                ->with('error', 'Seats already exist for this airplane (' . $airplane->model . ').');
        }

        $seats = [];

        // First Class: Rows 1-2, 2 seats per row (A, B)
        for ($row = 1; $row <= 2; $row++) {
            foreach (['A', 'B'] as $col) {
                $seats[] = [
                    'airplane_id' => $airplaneId,
                    'seat_number' => $row . $col,
                    'class' => 'first',
                    'status' => 'available',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Business Class: Rows 3-5, 4 seats per row (A, B, C, D)
        for ($row = 3; $row <= 5; $row++) {
            foreach (['A', 'B', 'C', 'D'] as $col) {
                $seats[] = [
                    'airplane_id' => $airplaneId,
                    'seat_number' => $row . $col,
                    'class' => 'business',
                    'status' => 'available',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Economy Class: Rows 6-33, 6 seats per row (A, B, C, D, E, F)
        for ($row = 6; $row <= 33; $row++) {
            foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $col) {
                $seats[] = [
                    'airplane_id' => $airplaneId,
                    'seat_number' => $row . $col,
                    'class' => 'economy',
                    'status' => 'available',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Bulk insert for performance
        Seat::insert($seats);

        $totalSeats = count($seats);
        $firstCount = 4;
        $businessCount = 12;
        $economyCount = 168;

        return redirect()->route('admin.seats.index')
            ->with('success', "Seats generated successfully. {$totalSeats} seats have been created ({$firstCount} First Class, {$businessCount} Business, {$economyCount} Economy).");
    }
}