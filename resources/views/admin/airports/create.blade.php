@extends('layouts.admin')

@section('title', 'Tambah Bandara')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-8">
        <h2 class="text-xl font-semibold text-white mb-6">Tambah Bandara Baru</h2>
        <form method="POST" action="{{ route('admin.airports.store') }}" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">Nama Bandara</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>
                @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Kota</label>
                    <input type="text" name="city" value="{{ old('city') }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>
                    @error('city') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Negara</label>
                    <input type="text" name="country" value="{{ old('country') }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>
                    @error('country') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-2">Kode IATA (3 huruf)</label>
                <input type="text" name="iata_code" value="{{ old('iata_code') }}" maxlength="5" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white uppercase focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>
                @error('iata_code') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2.5 bg-amber-500 text-black rounded-lg font-semibold hover:bg-amber-600 transition">Simpan</button>
                <a href="{{ route('admin.airports.index') }}" class="px-6 py-2.5 bg-zinc-800 text-zinc-300 rounded-lg hover:bg-zinc-700 transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection