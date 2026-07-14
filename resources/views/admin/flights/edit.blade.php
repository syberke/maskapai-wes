@extends('layouts.admin')
@section('title', 'Edit Penerbangan')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-8">
        <h2 class="text-xl font-semibold text-white mb-6">Edit Penerbangan</h2>
        <form method="POST" action="{{ route('admin.flights.update', $flight) }}" class="space-y-6">@csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Maskapai</label>
                    <select name="airline_id" id="airline_id" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>
                        <option value="">Pilih Maskapai</option>
                        @foreach($airlines as $al)<option value="{{ $al->id }}" {{ old('airline_id',$flight->airline_id)==$al->id?'selected':'' }}>{{ $al->name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Pesawat</label>
                    <select name="airplane_id" id="airplane_id" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>
                        <option value="">Pilih Pesawat</option>
                        @foreach($airplanes as $ap)<option value="{{ $ap->id }}" {{ old('airplane_id',$flight->airplane_id)==$ap->id?'selected':'' }} data-airline="{{ $ap->airline_id }}">{{ $ap->model }} ({{ $ap->registration_number }})</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">No. Penerbangan</label>
                    <input type="text" name="flight_number" id="flight_number" value="{{ old('flight_number', $flight->flight_number) }}" placeholder="Auto-generated" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    <p class="text-xs text-zinc-500 mt-1">Kosongkan untuk generate otomatis</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Harga (Rp)</label>
                    <input type="number" name="price" value="{{ old('price', $flight->price) }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Bandara Asal</label>
                    <select name="departure_airport_id" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>
                        <option value="">Pilih Bandara</option>
                        @foreach($airports as $ap)<option value="{{ $ap->id }}" {{ old('departure_airport_id',$flight->departure_airport_id)==$ap->id?'selected':'' }}>{{ $ap->city }} ({{ $ap->iata_code }})</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Bandara Tujuan</label>
                    <select name="arrival_airport_id" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>
                        <option value="">Pilih Bandara</option>
                        @foreach($airports as $ap)<option value="{{ $ap->id }}" {{ old('arrival_airport_id',$flight->arrival_airport_id)==$ap->id?'selected':'' }}>{{ $ap->city }} ({{ $ap->iata_code }})</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Waktu Berangkat</label>
                    <input type="datetime-local" name="departure_time" value="{{ old('departure_time', \Carbon\Carbon::parse($flight->departure_time)->format('Y-m-d\TH:i')) }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Waktu Tiba</label>
                    <input type="datetime-local" name="arrival_time" value="{{ old('arrival_time', \Carbon\Carbon::parse($flight->arrival_time)->format('Y-m-d\TH:i')) }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Gate</label>
                    <input type="text" name="gate" value="{{ old('gate', $flight->gate) }}" maxlength="10" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Terminal</label>
                    <input type="text" name="terminal" value="{{ old('terminal', $flight->terminal) }}" maxlength="10" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Durasi (jam)</label>
                    <input type="text" name="flight_duration" value="{{ old('flight_duration', $flight->flight_duration) }}" placeholder="2h 30m" maxlength="10" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Kursi Tersedia</label>
                    <input type="number" name="available_seats" value="{{ old('available_seats', $flight->available_seats) }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-2">Status</label>
                    <select name="status" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>
                        <option value="scheduled" {{ old('status', $flight->status)=='scheduled'?'selected':'' }}>Scheduled</option>
                        <option value="boarding" {{ old('status', $flight->status)=='boarding'?'selected':'' }}>Boarding</option>
                        <option value="delayed" {{ old('status', $flight->status)=='delayed'?'selected':'' }}>Delayed</option>
                        <option value="departed" {{ old('status', $flight->status)=='departed'?'selected':'' }}>Departed</option>
                        <option value="arrived" {{ old('status', $flight->status)=='arrived'?'selected':'' }}>Arrived</option>
                        <option value="cancelled" {{ old('status', $flight->status)=='cancelled'?'selected':'' }}>Cancelled</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2.5 bg-amber-500 text-black rounded-lg font-semibold hover:bg-amber-600 transition">Update</button>
                <a href="{{ route('admin.flights.index') }}" class="px-6 py-2.5 bg-zinc-800 text-zinc-300 rounded-lg hover:bg-zinc-700 transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection