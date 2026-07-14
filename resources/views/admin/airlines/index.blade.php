@extends('layouts.admin')
@section('title', 'Data Maskapai')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div><h2 class="text-xl font-semibold text-white">Data Maskapai</h2><p class="text-zinc-400 text-sm mt-1">Kelola data maskapai penerbangan</p></div>
        <a href="{{ route('admin.airlines.create') }}" class="px-4 py-2.5 bg-amber-500 text-black rounded-lg font-semibold text-sm hover:bg-amber-600 transition">+ Tambah Maskapai</a>
    </div>
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
        <table class="w-full">
            <thead><tr class="text-left text-xs uppercase tracking-wider text-zinc-500 bg-zinc-800/50"><th class="px-6 py-4">Kode</th><th class="px-6 py-4">Nama</th><th class="px-6 py-4">No. Registrasi</th><th class="px-6 py-4">Pesawat</th><th class="px-6 py-4">Penerbangan</th><th class="px-6 py-4">Aksi</th></tr></thead>
            <tbody class="divide-y divide-zinc-800">
                @forelse($airlines as $airline)
                <tr class="text-sm text-zinc-300 hover:bg-zinc-800/30 transition">
                    <td class="px-6 py-4 font-mono text-amber-500 font-bold">{{ $airline->code }}</td>
                    <td class="px-6 py-4">{{ $airline->name }}</td>
                    <td class="px-6 py-4">{{ $airline->registration_number ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $airline->airplanes_count }}</td>
                    <td class="px-6 py-4">{{ $airline->flights_count }}</td>
                    <td class="px-6 py-4"><div class="flex gap-2"><a href="{{ route('admin.airlines.edit', $airline) }}" class="px-3 py-1.5 bg-zinc-800 text-zinc-300 rounded-lg text-xs hover:bg-zinc-700 transition">Edit</a><form method="POST" action="{{ route('admin.airlines.destroy', $airline) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="px-3 py-1.5 bg-red-500/10 text-red-400 rounded-lg text-xs hover:bg-red-500/20 transition">Hapus</button></form></div></td>
                </tr>
                @empty <tr><td colspan="6" class="px-6 py-8 text-center text-zinc-500">Belum ada data maskapai</td></tr> @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-zinc-800">{{ $airlines->links() }}</div>
    </div>
</div>
@endsection