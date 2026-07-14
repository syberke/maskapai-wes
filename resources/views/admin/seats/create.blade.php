@extends('layouts.admin')
@section('title', 'Tambah Kursi')
@section('content')
<div class="max-w-2xl mx-auto"><div class="bg-zinc-900 border border-zinc-800 rounded-xl p-8"><h2 class="text-xl font-semibold text-white mb-6">Tambah Kursi Baru</h2>
<form method="POST" action="{{ route('admin.seats.store') }}" class="space-y-6">@csrf
<div><label class="block text-sm font-medium text-zinc-300 mb-2">Pesawat</label><select name="airplane_id" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>@foreach($airplanes as $a)<option value="{{ $a->id }}">{{ $a->airline->name }} - {{ $a->model }} ({{ $a->registration_number }})</option>@endforeach</select></div>
<div class="grid grid-cols-3 gap-4">
<div><label class="block text-sm font-medium text-zinc-300 mb-2">No Kursi</label><input type="text" name="seat_number" value="{{ old('seat_number') }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required></div>
<div><label class="block text-sm font-medium text-zinc-300 mb-2">Kelas</label><select name="class" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required><option value="economy">Economy</option><option value="business">Business</option><option value="first">First Class</option></select></div>
<div><label class="block text-sm font-medium text-zinc-300 mb-2">Status</label><select name="status" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required><option value="available">Available</option><option value="booked">Booked</option></select></div>
</div>
<div class="flex gap-3"><button type="submit" class="px-6 py-2.5 bg-amber-500 text-black rounded-lg font-semibold hover:bg-amber-600 transition">Simpan</button><a href="{{ route('admin.seats.index') }}" class="px-6 py-2.5 bg-zinc-800 text-zinc-300 rounded-lg hover:bg-zinc-700 transition">Batal</a></div>
</form></div></div>
@endsection