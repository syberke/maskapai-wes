@extends('layouts.staff')
@section('title', 'Passenger Manifest')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-white tracking-tight">Passenger Manifest</h2>
            <p class="text-zinc-500 text-sm mt-1">View passenger details and export operational reports per flight</p>
        </div>
        <span class="text-xs text-zinc-500 bg-zinc-800 px-3 py-1.5 rounded-lg">{{ $flights->total() }} flights</span>
    </div>

    <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl overflow-hidden">
        <div class="p-4 border-b border-zinc-800">
            <div class="relative w-full max-w-sm">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-sm"></i>
                <input id="flight-search" type="text" placeholder="Search flight, airline, or route..." class="bg-zinc-800 border border-zinc-700 rounded-lg pl-9 pr-4 py-2 text-sm text-white placeholder-zinc-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 w-full">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px]">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-zinc-500 bg-zinc-800/30">
                        <th class="px-6 py-4 font-semibold">Flight</th>
                        <th class="px-6 py-4 font-semibold">Airline</th>
                        <th class="px-6 py-4 font-semibold">Route</th>
                        <th class="px-6 py-4 font-semibold">Departure</th>
                        <th class="px-6 py-4 font-semibold">Passengers</th>
                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @forelse($flights as $flight)
                    <tr class="flight-row text-sm text-zinc-300 hover:bg-zinc-800/20 transition" data-search="{{ strtolower($flight->flight_number . ' ' . $flight->airline->name . ' ' . $flight->departureAirport->iata_code . ' ' . $flight->arrivalAirport->iata_code) }}">
                        <td class="px-6 py-4"><span class="font-mono text-amber-500 font-bold bg-amber-500/5 px-2 py-1 rounded text-xs">{{ $flight->flight_number }}</span></td>
                        <td class="px-6 py-4">{{ $flight->airline->name }}</td>
                        <td class="px-6 py-4"><span class="text-xs font-mono bg-zinc-800/50 px-2 py-1 rounded">{{ $flight->departureAirport->iata_code }} → {{ $flight->arrivalAirport->iata_code }}</span></td>
                        <td class="px-6 py-4 text-xs">{{ $flight->departure_time->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4"><span class="text-white font-semibold">{{ (int) ($flight->manifest_passengers_count ?? 0) }}</span></td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('staff.manifest.show', $flight) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500/10 text-amber-400 rounded-lg text-xs hover:bg-amber-500/20 transition">
                                <i class="fas fa-eye"></i> View Manifest
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-zinc-500"><i class="fas fa-inbox text-2xl mb-2 block text-zinc-700"></i>No flights available</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($flights->hasPages())
        <div class="p-4 border-t border-zinc-800">{{ $flights->links() }}</div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('flight-search');
    const rows = document.querySelectorAll('.flight-row');

    search?.addEventListener('input', function () {
        const keyword = this.value.trim().toLowerCase();
        rows.forEach(row => row.classList.toggle('hidden', !row.dataset.search.includes(keyword)));
    });
});
</script>
@endpush
@endsection
