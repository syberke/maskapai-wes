@extends('layouts.admin')
@section('title', 'Tambah Maskapai')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-8">
        <h2 class="text-xl font-semibold text-white mb-6">Tambah Maskapai Baru</h2>
        <form method="POST" action="{{ route('admin.airlines.store') }}" class="space-y-6">
            @csrf
            <div><label class="block text-sm font-medium text-zinc-300 mb-2">Nama Maskapai</label><input type="text" name="name" value="{{ old('name') }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>@error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror</div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-zinc-300 mb-2">Kode</label><input type="text" name="code" value="{{ old('code') }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white uppercase focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>@error('code')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-zinc-300 mb-2">No. Registrasi</label><input type="text" name="registration_number" value="{{ old('registration_number') }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500"></div>
            </div>
            <div><label class="block text-sm font-medium text-zinc-300 mb-2">Deskripsi</label><textarea name="description" rows="3" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">{{ old('description') }}</textarea></div>
            <div><label class="block text-sm font-medium text-zinc-300 mb-2">Logo URL</label><input type="url" name="logo" value="{{ old('logo') }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500"></div>
            <div class="flex gap-3"><button type="submit" class="px-6 py-2.5 bg-amber-500 text-black rounded-lg font-semibold hover:bg-amber-600 transition">Simpan</button><a href="{{ route('admin.airlines.index') }}" class="px-6 py-2.5 bg-zinc-800 text-zinc-300 rounded-lg hover:bg-zinc-700 transition">Batal</a></div>
        </form>
    </div>
</div>
@endsection