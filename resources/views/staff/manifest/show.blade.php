@extends('layouts.staff')
@section('title', 'Passenger Manifest - ' . $flight->flight_number)
@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <a href="{{ route('staff.manifest') }}" class="text-amber-500 hover:text-amber-400 text-sm inline-flex items-center gap-1.5 transition">
                <i class="fas fa-arrow-left"></i> Back to Manifest List
            </a>
            <h2 class="text-xl font-semibold text-white tracking-tight mt-2">{{ $flight->airline->name }} - {{ $flight->flight_number }}</h2>
            <p class="text-zinc-500 text-sm mt-1">
                {{ $flight->departureAirport->city }} ({{ $flight->departureAirport->iata_code }})
                → {{ $flight->arrivalAirport->city }} ({{ $flight->arrivalAirport->iata_code }})
                · {{ $flight->departure_time->format('d M Y H:i') }}
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('staff.manifest.pdf', $flight) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-zinc-800 text-zinc-300 rounded-lg text-xs hover:bg-zinc-700 transition">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
            <a href="{{ route('staff.manifest.excel', $flight) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-500/10 text-amber-400 rounded-lg text-xs hover:bg-amber-500/20 transition">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl p-4">
            <p class="text-zinc-500 text-xs">Total Passengers</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $summary['total'] }}</p>
        </div>
        <div class="bg-zinc-900/80 border border-blue-500/20 rounded-xl p-4">
            <p class="text-zinc-500 text-xs">Male</p>
            <p class="text-2xl font-bold text-blue-400 mt-1">{{ $summary['male'] }}</p>
        </div>
        <div class="bg-zinc-900/80 border border-pink-500/20 rounded-xl p-4">
            <p class="text-zinc-500 text-xs">Female</p>
            <p class="text-2xl font-bold text-pink-400 mt-1">{{ $summary['female'] }}</p>
        </div>
        <div class="bg-zinc-900/80 border border-emerald-500/20 rounded-xl p-4">
            <p class="text-zinc-500 text-xs">Confirmed</p>
            <p class="text-2xl font-bold text-emerald-400 mt-1">{{ $summary['confirmed'] }}</p>
        </div>
        <div class="bg-zinc-900/80 border border-yellow-500/20 rounded-xl p-4">
            <p class="text-zinc-500 text-xs">Pending</p>
            <p class="text-2xl font-bold text-yellow-400 mt-1">{{ $summary['pending'] }}</p>
        </div>
    </div>

    <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl overflow-hidden">
        <div class="p-4 border-b border-zinc-800 flex items-center justify-between gap-4">
            <div class="relative w-full max-w-sm">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-sm"></i>
                <input id="manifest-search" type="text" placeholder="Search name, seat, or booking code..." class="bg-zinc-800 border border-zinc-700 rounded-lg pl-9 pr-4 py-2 text-sm text-white placeholder-zinc-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 w-full">
            </div>
            <span class="text-xs text-zinc-500">Generated {{ $generatedAt->format('d M Y H:i') }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px]">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-zinc-500 bg-zinc-800/30">
                        <th class="px-5 py-4 font-semibold">Passenger</th>
                        <th class="px-5 py-4 font-semibold">Gender</th>
                        <th class="px-5 py-4 font-semibold">Birth Date</th>
                        <th class="px-5 py-4 font-semibold">Seat</th>
                        <th class="px-5 py-4 font-semibold">Contact</th>
                        <th class="px-5 py-4 font-semibold">Nationality</th>
                        <th class="px-5 py-4 font-semibold">Passport</th>
                        <th class="px-5 py-4 font-semibold">Booking</th>
                        <th class="px-5 py-4 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody id="manifest-rows" class="divide-y divide-zinc-800">
                    @forelse($passengers as $passenger)
                    <tr class="manifest-row text-sm text-zinc-300 hover:bg-zinc-800/20 transition" data-search="{{ strtolower($passenger->full_name . ' ' . $passenger->resolved_seat_number . ' ' . ($passenger->booking->booking_code ?? '')) }}">
                        <td class="px-5 py-4">
                            <p class="text-white font-medium">{{ $passenger->full_name }}</p>
                            <p class="text-xs text-zinc-500 mt-0.5">{{ $passenger->email ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-4">{{ $passenger->gender_label }}</td>
                        <td class="px-5 py-4 text-xs">{{ optional($passenger->resolved_date_of_birth)->format('d M Y') ?? '-' }}</td>
                        <td class="px-5 py-4"><span class="font-mono text-amber-500 bg-amber-500/5 px-2 py-1 rounded text-xs">{{ $passenger->resolved_seat_number }}</span></td>
                        <td class="px-5 py-4 text-xs">{{ $passenger->phone ?? '-' }}</td>
                        <td class="px-5 py-4 text-xs">{{ $passenger->nationality ?? '-' }}</td>
                        <td class="px-5 py-4 font-mono text-xs">{{ $passenger->passport_number ?? '-' }}</td>
                        <td class="px-5 py-4 font-mono text-xs text-amber-500/80">{{ $passenger->booking->booking_code ?? '-' }}</td>
                        <td class="px-5 py-4">
                            @php($status = $passenger->booking->status ?? 'pending')
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ in_array($status, ['paid', 'issued']) ? 'bg-emerald-500/10 text-emerald-400' : 'bg-yellow-500/10 text-yellow-400' }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="px-6 py-12 text-center text-zinc-500"><i class="fas fa-user-slash text-2xl mb-2 block text-zinc-700"></i>No passengers on this flight</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('manifest-search');
    const rows = document.querySelectorAll('.manifest-row');

    search?.addEventListener('input', function () {
        const keyword = this.value.trim().toLowerCase();
        rows.forEach(row => {
            row.classList.toggle('hidden', !row.dataset.search.includes(keyword));
        });
    });
});
</script>
@endpush
@endsection
