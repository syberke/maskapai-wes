@extends('layouts.admin')
@section('title', 'Data Kursi')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-white">Seat Management</h2>
            <p class="text-zinc-400 text-sm mt-1">Manage seats per aircraft</p>
        </div>
        <div class="flex gap-3">
            <button type="button" onclick="document.getElementById('generateModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-500/10 text-emerald-400 rounded-xl font-semibold text-sm hover:bg-emerald-500/20 transition border border-emerald-500/20">
                <i class="fas fa-cogs"></i>
                <span>Generate Seats</span>
            </button>
            <a href="{{ route('admin.seats.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 text-black rounded-xl font-semibold text-sm hover:from-amber-600 hover:to-amber-700 transition-all shadow-lg shadow-amber-500/20">
                <i class="fas fa-plus"></i>
                <span>Add Seat</span>
            </a>
        </div>
    </div>

    <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl overflow-hidden">
        <div class="p-4 border-b border-zinc-800">
            <span class="text-xs text-zinc-500">{{ $seats->total() }} total seats</span>
        </div>
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-zinc-500 bg-zinc-800/30">
                    <th class="px-6 py-4 font-semibold">Aircraft</th>
                    <th class="px-6 py-4 font-semibold">Airline</th>
                    <th class="px-6 py-4 font-semibold">Seat No</th>
                    <th class="px-6 py-4 font-semibold">Class</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @forelse($seats as $s)
                <tr class="text-sm text-zinc-300 hover:bg-zinc-800/20 transition">
                    <td class="px-6 py-4">{{ $s->airplane->model }}</td>
                    <td class="px-6 py-4">{{ $s->airplane->airline->name }}</td>
                    <td class="px-6 py-4 font-mono font-bold text-white">{{ $s->seat_number }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold
                            @if($s->class=='first') bg-purple-500/10 text-purple-400
                            @elseif($s->class=='business') bg-blue-500/10 text-blue-400
                            @else bg-zinc-500/10 text-zinc-400 @endif">
                            <i class="fas fa-circle text-[6px] @if($s->class=='first') text-purple-400 @elseif($s->class=='business') text-blue-400 @else text-zinc-400 @endif"></i>
                            {{ ucfirst($s->class) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold
                            @if($s->status=='available') bg-emerald-500/10 text-emerald-400
                            @else bg-red-500/10 text-red-400 @endif">
                            <i class="fas fa-circle text-[6px] @if($s->status=='available') text-emerald-400 @else text-red-400 @endif"></i>
                            {{ ucfirst($s->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.seats.edit', $s) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-zinc-800 text-zinc-300 rounded-lg text-xs hover:bg-zinc-700 hover:text-white transition">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('admin.seats.destroy', $s) }}" onsubmit="return confirm('Delete this seat?')">
                                @csrf @method('DELETE')
                                <button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-500/10 text-red-400 rounded-lg text-xs hover:bg-red-500/20 transition">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <i class="fas fa-chair text-3xl text-zinc-700 mb-3 block"></i>
                        <p class="text-zinc-500">No seats found</p>
                        <p class="text-zinc-600 text-xs mt-1">Click "Generate Seats" to auto-create 184 seats per aircraft</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($seats->hasPages())
        <div class="p-4 border-t border-zinc-800">{{ $seats->links() }}</div>
        @endif
    </div>
</div>

<!-- Generate Seats Modal -->
<div id="generateModal" class="fixed inset-0 z-50 hidden flex items-center justify-center" style="background: rgba(0,0,0,0.7); backdrop-filter: blur(4px);">
    <div class="bg-zinc-900 border border-amber-500/20 rounded-2xl p-8 w-full max-w-md mx-4 shadow-2xl">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-white">Generate Seats</h3>
            <button type="button" onclick="document.getElementById('generateModal').classList.add('hidden')" class="text-zinc-500 hover:text-white transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="mb-6 p-4 bg-amber-500/5 border border-amber-500/10 rounded-xl">
            <h4 class="text-white font-semibold text-sm mb-2">Boeing 737-800NG Configuration</h4>
            <div class="space-y-1 text-xs text-zinc-400">
                <div class="flex justify-between"><span>First Class (Rows 1-2)</span><span class="text-purple-400">4 seats</span></div>
                <div class="flex justify-between"><span>Business Class (Rows 3-5)</span><span class="text-blue-400">12 seats</span></div>
                <div class="flex justify-between"><span>Economy Class (Rows 6-33)</span><span class="text-zinc-400">168 seats</span></div>
                <div class="flex justify-between pt-2 border-t border-zinc-800 text-white font-semibold">
                    <span>Total</span><span>184 seats</span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.seats.generate') }}">
            @csrf
            <div class="mb-6">
                <label class="block text-sm font-medium text-zinc-300 mb-2">Select Aircraft</label>
                <select name="airplane_id" required class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-3 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    <option value="">Choose an aircraft...</option>
                    @foreach($airplanes as $ap)
                        <option value="{{ $ap->id }}">{{ $ap->airline->name }} - {{ $ap->model }} ({{ $ap->registration_number }})</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl font-semibold text-sm hover:from-emerald-600 hover:to-emerald-700 transition shadow-lg shadow-emerald-500/20">
                    <i class="fas fa-cogs mr-2"></i>Generate
                </button>
                <button type="button" onclick="document.getElementById('generateModal').classList.add('hidden')" class="px-6 py-2.5 bg-zinc-800 text-zinc-300 rounded-xl hover:bg-zinc-700 transition text-sm">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('generateModal').classList.add('hidden');
    }
});
// Close modal when clicking outside
document.getElementById('generateModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});
</script>
@endpush
@endsection