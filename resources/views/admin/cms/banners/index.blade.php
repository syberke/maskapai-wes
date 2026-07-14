@extends('layouts.admin')
@section('title', 'Banners CMS')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div><h2 class="text-xl font-semibold text-white">Banner Landing Page</h2></div>
        <a href="{{ route('admin.cms.banners.create') }}" class="px-4 py-2.5 bg-amber-500 text-black rounded-lg font-semibold text-sm hover:bg-amber-600 transition">+ Tambah Banner</a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($banners as $b)
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
            <img src="{{ $b->image_url }}" class="w-full h-40 object-cover" onerror="this.src='https://via.placeholder.com/400x200?text=Banner'">
            <div class="p-4"><h3 class="text-white font-semibold">{{ $b->title }}</h3><p class="text-zinc-400 text-sm">{{ $b->subtitle }}</p>
            <div class="flex items-center gap-2 mt-2"><span class="text-xs {{ $b->is_active ? 'text-emerald-400' : 'text-red-400' }}">{{ $b->is_active ? 'Active' : 'Inactive' }}</span>
            <div class="flex gap-2 ml-auto"><a href="{{ route('admin.cms.banners.edit', $b) }}" class="px-3 py-1 bg-zinc-800 text-zinc-300 rounded text-xs hover:bg-zinc-700">Edit</a>
            <form method="POST" action="{{ route('admin.cms.banners.destroy', $b) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="px-3 py-1 bg-red-500/10 text-red-400 rounded text-xs hover:bg-red-500/20">Hapus</button></form></div></div></div>
        </div>
        @empty <div class="col-span-3 text-center py-12 text-zinc-500">Belum ada banner</div> @endforelse
    </div>
</div>
@endsection