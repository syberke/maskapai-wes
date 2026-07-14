@extends('layouts.admin')
@section('title', 'Tambah Banner')
@section('content')
<div class="max-w-2xl mx-auto"><div class="bg-zinc-900 border border-zinc-800 rounded-xl p-8"><h2 class="text-xl font-semibold text-white mb-6">Tambah Banner Baru</h2>
<form method="POST" action="{{ route('admin.cms.banners.store') }}" class="space-y-6">@csrf
<div><label class="block text-sm font-medium text-zinc-300 mb-2">Judul</label><input type="text" name="title" value="{{ old('title') }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required></div>
<div><label class="block text-sm font-medium text-zinc-300 mb-2">Subtitle</label><input type="text" name="subtitle" value="{{ old('subtitle') }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500"></div>
<div><label class="block text-sm font-medium text-zinc-300 mb-2">URL Gambar</label><input type="url" name="image_url" value="{{ old('image_url') }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required></div>
<div><label class="flex items-center gap-3"><input type="checkbox" name="is_active" value="1" checked class="rounded bg-zinc-800 border-zinc-700 text-amber-500 focus:ring-amber-500"><span class="text-sm text-zinc-300">Aktif</span></label></div>
<div class="flex gap-3"><button type="submit" class="px-6 py-2.5 bg-amber-500 text-black rounded-lg font-semibold hover:bg-amber-600 transition">Simpan</button><a href="{{ route('admin.cms.banners.index') }}" class="px-6 py-2.5 bg-zinc-800 text-zinc-300 rounded-lg hover:bg-zinc-700 transition">Batal</a></div>
</form></div></div>
@endsection