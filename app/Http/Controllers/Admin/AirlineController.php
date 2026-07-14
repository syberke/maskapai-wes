<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airline;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AirlineController extends Controller
{
    public function index(): View
    {
        $airlines = Airline::withCount(['flights', 'airplanes'])->latest()->paginate(10);
        return view('admin.airlines.index', compact('airlines'));
    }

    public function create(): View
    {
        return view('admin.airlines.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:10', 'unique:airlines'],
            'registration_number' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'url'],
            'photos' => ['nullable', 'url'],
        ]);

        Airline::create($validated);
        return redirect()->route('admin.airlines.index')->with('success', 'Maskapai berhasil ditambahkan.');
    }

    public function edit(Airline $airline): View
    {
        return view('admin.airlines.edit', compact('airline'));
    }

    public function update(Request $request, Airline $airline): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:10', 'unique:airlines,code,' . $airline->id],
            'registration_number' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'url'],
            'photos' => ['nullable', 'url'],
        ]);

        $airline->update($validated);
        return redirect()->route('admin.airlines.index')->with('success', 'Maskapai berhasil diperbarui.');
    }

    public function destroy(Airline $airline): RedirectResponse
    {
        $airline->delete();
        return redirect()->route('admin.airlines.index')->with('success', 'Maskapai berhasil dihapus.');
    }
}