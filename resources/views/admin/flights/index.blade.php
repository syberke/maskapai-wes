@extends('layouts.admin')
@section('title', 'Data Penerbangan')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div><h2 class="text-xl font-semibold text-white">Data Penerbangan</h2><p class="text-zinc-400 text-sm mt-1">Kelola jadwal penerbangan</p></div>
        <a href="{{ route('admin.flights.create') }}" class="px-4 py-2.5 bg-amber-500 text-black rounded-lg font-semibold text-sm hover:bg-amber-600 transition">+ Tambah Penerbangan</a>
    </div>
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
        <table class="w-full">
            <thead><tr class="text-left text-xs uppercase tracking-wider text-zinc-500 bg-zinc-800/50">
                <th class="px-6 py-4">No. Penerbangan</th>
                <th class="px-6 py-4">Maskapai</th>
                <th class="px-6 py-4">Rute</th>
                <th class="px-6 py-4">Berangkat</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4">Gate</th>
                <th class="px-6 py-4">Terminal</th>
                <th class="px-6 py-4">Harga</th>
                <th class="px-6 py-4">Kursi</th>
                <th class="px-6 py-4">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-zinc-800">
                @forelse($flights as $f)
                <tr class="text-sm text-zinc-300">
                    <td class="font-mono text-amber-500">{{ $f->flight_number }}</td>
                    <td>{{ $f->airline->name }}</td>
                    <td>{{ $f->departureAirport->iata_code }} → {{ $f->arrivalAirport->iata_code }}</td>
                    <td class="text-xs">{{ \Carbon\Carbon::parse($f->departure_time)->format('d M Y H:i') }}</td>
                    <td>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                            @if($f->status=='scheduled') bg-blue-500/10 text-blue-400
                            @elseif($f->status=='boarding') bg-emerald-500/10 text-emerald-400
                            @elseif($f->status=='delayed') bg-yellow-500/10 text-yellow-400
                            @elseif($f->status=='departed') bg-purple-500/10 text-purple-400
                            @elseif($f->status=='arrived') bg-cyan-500/10 text-cyan-400
                            @else bg-red-500/10 text-red-400 @endif">
                            {{ ucfirst($f->status) }}
                        </span>
                    </td>
                    <td>{{ $f->gate ?? '-' }}</td>
                    <td>{{ $f->terminal ?? '-' }}</td>
                    <td>Rp {{ number_format($f->price,0,',','.') }}</td>
                    <td>{{ $f->available_seats }}</td>
                    <td><div class="flex gap-2">
                        <a href="{{ route('admin.flights.edit', $f) }}" class="px-3 py-1.5 bg-zinc-800 text-zinc-300 rounded-lg text-xs hover:bg-zinc-700 transition">Edit</a>
                        <form method="POST" action="{{ route('admin.flights.destroy', $f) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="px-3 py-1.5 bg-red-500/10 text-red-400 rounded-lg text-xs hover:bg-red-500/20 transition">Hapus</button></form>
                    </div></td>
                </tr>
                @empty <tr><td colspan="10" class="px-6 py-8 text-center text-zinc-500">Belum ada data penerbangan</td></tr> @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-zinc-800">{{ $flights->links() }}</div>
    </div>
</div>
@endsection