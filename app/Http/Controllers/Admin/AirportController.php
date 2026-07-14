<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use App\Http\Requests\Admin\AirportRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AirportController extends Controller
{
    public function index(): View
    {
        $airports = Airport::latest()->paginate(10);
        return view('admin.airports.index', compact('airports'));
    }

    public function create(): View
    {
        return view('admin.airports.create');
    }

    public function store(AirportRequest $request): RedirectResponse
    {
        Airport::create($request->validated());
        return redirect()->route('admin.airports.index')->with('success', 'Data bandara berhasil ditambahkan.');
    }

    public function edit(Airport $airport): View
    {
        return view('admin.airports.edit', compact('airport'));
    }

    public function update(AirportRequest $request, Airport $airport): RedirectResponse
    {
        $airport->update($request->validated());
        return redirect()->route('admin.airports.index')->with('success', 'Data bandara berhasil diperbarui.');
    }

    public function destroy(Airport $airport): RedirectResponse
    {
        $airport->delete();
        return redirect()->route('admin.airports.index')->with('success', 'Data bandara berhasil dihapus.');
    }
}