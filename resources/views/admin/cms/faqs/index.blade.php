@extends('layouts.admin')
@section('title', 'FAQ')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between"><div><h2 class="text-xl font-semibold text-white">FAQ</h2></div><a href="{{ route('admin.cms.faqs.create') }}" class="px-4 py-2.5 bg-amber-500 text-black rounded-lg font-semibold text-sm hover:bg-amber-600 transition">+ Tambah FAQ</a></div>
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
        <table class="w-full">
            <thead><tr class="text-left text-xs uppercase tracking-wider text-zinc-500 bg-zinc-800/50"><th class="px-6 py-4">Pertanyaan</th><th class="px-6 py-4">Jawaban</th><th class="px-6 py-4">Aksi</th></tr></thead>
            <tbody class="divide-y divide-zinc-800">
                @forelse($faqs as $f)
                <tr class="text-sm text-zinc-300"><td class="font-medium">{{ $f->question }}</td><td class="max-w-md truncate text-zinc-400">{{ $f->answer }}</td>
                <td><div class="flex gap-2"><a href="{{ route('admin.cms.faqs.edit', $f) }}" class="px-3 py-1.5 bg-zinc-800 text-zinc-300 rounded-lg text-xs hover:bg-zinc-700">Edit</a><form method="POST" action="{{ route('admin.cms.faqs.destroy', $f) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="px-3 py-1.5 bg-red-500/10 text-red-400 rounded-lg text-xs hover:bg-red-500/20">Hapus</button></form></div></td></tr>
                @empty <tr><td colspan="3" class="px-6 py-8 text-center text-zinc-500">Belum ada FAQ</td></tr> @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-zinc-800">{{ $faqs->links() }}</div>
    </div>
</div>
@endsection