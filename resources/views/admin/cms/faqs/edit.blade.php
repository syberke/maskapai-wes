@extends('layouts.admin')
@section('title', 'Edit FAQ')
@section('content')
<div class="max-w-2xl mx-auto"><div class="bg-zinc-900 border border-zinc-800 rounded-xl p-8"><h2 class="text-xl font-semibold text-white mb-6">Edit FAQ</h2>
<form method="POST" action="{{ route('admin.cms.faqs.update', $faq) }}" class="space-y-6">@csrf @method('PUT')
<div><label class="block text-sm font-medium text-zinc-300 mb-2">Pertanyaan</label><input type="text" name="question" value="{{ old('question', $faq->question) }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required></div>
<div><label class="block text-sm font-medium text-zinc-300 mb-2">Jawaban</label><textarea name="answer" rows="4" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500" required>{{ old('answer', $faq->answer) }}</textarea></div>
<div class="flex gap-3"><button type="submit" class="px-6 py-2.5 bg-amber-500 text-black rounded-lg font-semibold hover:bg-amber-600 transition">Update</button><a href="{{ route('admin.cms.faqs.index') }}" class="px-6 py-2.5 bg-zinc-800 text-zinc-300 rounded-lg hover:bg-zinc-700 transition">Batal</a></div>
</form></div></div>
@endsection