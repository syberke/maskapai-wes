@extends('layouts.admin')
@section('title', 'Data Pengguna')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div><h2 class="text-xl font-semibold text-white">Data Pengguna</h2><p class="text-zinc-400 text-sm mt-1">Kelola semua pengguna sistem</p></div>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2.5 bg-amber-500 text-black rounded-lg font-semibold text-sm hover:bg-amber-600 transition">+ Tambah Pengguna</a>
    </div>
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
        <table class="w-full">
            <thead><tr class="text-left text-xs uppercase tracking-wider text-zinc-500 bg-zinc-800/50"><th class="px-6 py-4">Nama</th><th class="px-6 py-4">Email</th><th class="px-6 py-4">Role</th><th class="px-6 py-4">Verifikasi</th><th class="px-6 py-4">Aksi</th></tr></thead>
            <tbody class="divide-y divide-zinc-800">
                @forelse($users as $u)
                <tr class="text-sm text-zinc-300"><td>{{ $u->name }}</td><td>{{ $u->email }}</td>
                <td><span class="px-2 py-1 rounded text-xs @if($u->role=='admin') bg-amber-500/10 text-amber-400 @elseif($u->role=='manager') bg-purple-500/10 text-purple-400 @elseif($u->role=='staff') bg-blue-500/10 text-blue-400 @else bg-zinc-500/10 text-zinc-400 @endif">{{ ucfirst($u->role) }}</span></td>
                <td>@if($u->email_verified_at)<span class="text-emerald-400">Verified</span>@else<span class="text-red-400">Unverified</span>@endif</td>
                <td><div class="flex gap-2"><a href="{{ route('admin.users.edit', $u) }}" class="px-3 py-1.5 bg-zinc-800 text-zinc-300 rounded-lg text-xs hover:bg-zinc-700 transition">Edit</a>@if($u->id !== auth()->id())<form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="px-3 py-1.5 bg-red-500/10 text-red-400 rounded-lg text-xs hover:bg-red-500/20 transition">Hapus</button></form>@endif</div></td></tr>
                @empty <tr><td colspan="5" class="px-6 py-8 text-center text-zinc-500">Belum ada data pengguna</td></tr> @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-zinc-800">{{ $users->links() }}</div>
    </div>
</div>
@endsection