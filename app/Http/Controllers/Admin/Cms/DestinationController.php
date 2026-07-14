<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\Cms\Destination;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DestinationController extends Controller
{
    public function index(): View
    {
        $destinations = Destination::latest()->paginate(10);
        return view('admin.cms.destinations.index', compact('destinations'));
    }

    public function create(): View
    {
        return view('admin.cms.destinations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'city_name' => ['required', 'string', 'max:255'],
            'image_url' => ['required', 'url'],
            'description' => ['nullable', 'string'],
            'is_featured' => ['boolean'],
        ]);

        Destination::create($validated);
        return redirect()->route('admin.cms.destinations.index')->with('success', 'Destinasi berhasil ditambahkan.');
    }

    public function edit(Destination $destination): View
    {
        return view('admin.cms.destinations.edit', compact('destination'));
    }

    public function update(Request $request, Destination $destination): RedirectResponse
    {
        $validated = $request->validate([
            'city_name' => ['required', 'string', 'max:255'],
            'image_url' => ['required', 'url'],
            'description' => ['nullable', 'string'],
            'is_featured' => ['boolean'],
        ]);

        $destination->update($validated);
        return redirect()->route('admin.cms.destinations.index')->with('success', 'Destinasi berhasil diperbarui.');
    }

    public function destroy(Destination $destination): RedirectResponse
    {
        $destination->delete();
        return redirect()->route('admin.cms.destinations.index')->with('success', 'Destinasi berhasil dihapus.');
    }
}