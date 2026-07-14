@extends('layouts.admin')
@section('title', 'Edit Testimoni')
@section('content')
<div class="max-w-2xl mx-auto"><div class="bg-zinc-900 border border-zinc-800 rounded-xl p-8"><h2 class="text-xl font-semibold text-white mb-6">Edit Testimoni</h2>
<form method="POST" action="{{ route('admin.cms.testimonials.update', $testimonial) }}" class="space-y-6">@csrf @method('PUT')
<div><label class="block text-sm font-medium text-zinc-300 mb-2">Nama</label><input type="text" name="name" value="{{ old('name', $testimonial->name) }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required></div>
<div><label class="block text-sm font-medium text-zinc-300 mb-2">Avatar URL</label><input type="url" name="avatar_url" value="{{ old('avatar_url', $testimonial->avatar_url) }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500"></div>
<div><label class="block text-sm font-medium text-zinc-300 mb-2">Rating</label><select name="rating" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>@for($i=1;$i<=5;$i++)<option value="{{ $i }}" {{ old('rating',$testimonial->rating)==$i?'selected':'' }}>{{ $i }}★</option>@endfor</select></div>
<div><label class="block text-sm font-medium text-zinc-300 mb-2">Review</label><textarea name="review" rows="4" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>{{ old('review', $testimonial->review) }}</textarea></div>
<div class="flex gap-3"><button type="submit" class="px-6 py-2.5 bg-amber-500 text-black rounded-lg font-semibold hover:bg-amber-600 transition">Update</button><a href="{{ route('admin.cms.testimonials.index') }}" class="px-6 py-2.5 bg-zinc-800 text-zinc-300 rounded-lg hover:bg-zinc-700 transition">Batal</a></div>
</form></div></div>
@endsection