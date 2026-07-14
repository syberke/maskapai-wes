@extends('layouts.admin')
@section('title', 'Edit Pengguna')
@section('content')
<div class="max-w-2xl mx-auto"><div class="bg-zinc-900 border border-zinc-800 rounded-xl p-8"><h2 class="text-xl font-semibold text-white mb-6">Edit Pengguna</h2>
<form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">@csrf @method('PUT')
<div><label class="block text-sm font-medium text-zinc-300 mb-2">Nama</label><input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required></div>
<div><label class="block text-sm font-medium text-zinc-300 mb-2">Email</label><input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required></div>
<div class="grid grid-cols-2 gap-4">
<div><label class="block text-sm font-medium text-zinc-300 mb-2">Password Baru (kosongkan jika tidak diubah)</label><input type="password" name="password" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500"></div>
<div><label class="block text-sm font-medium text-zinc-300 mb-2">Konfirmasi Password</label><input type="password" name="password_confirmation" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500"></div>
</div>
<div><label class="block text-sm font-medium text-zinc-300 mb-2">Role</label><select name="role" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required><option value="customer" {{ $user->role=='customer'?'selected':'' }}>Customer</option><option value="staff" {{ $user->role=='staff'?'selected':'' }}>Staff</option><option value="manager" {{ $user->role=='manager'?'selected':'' }}>Manager</option><option value="admin" {{ $user->role=='admin'?'selected':'' }}>Admin</option></select></div>
<div class="flex gap-3"><button type="submit" class="px-6 py-2.5 bg-amber-500 text-black rounded-lg font-semibold hover:bg-amber-600 transition">Update</button><a href="{{ route('admin.users.index') }}" class="px-6 py-2.5 bg-zinc-800 text-zinc-300 rounded-lg hover:bg-zinc-700 transition">Batal</a></div>
</form></div></div>
@endsection