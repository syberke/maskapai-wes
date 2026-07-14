@extends('layouts.admin')
@section('title', 'Airport Management')
@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-white tracking-tight">Airports</h2>
            <p class="text-zinc-500 text-sm mt-1">Manage all airport destinations</p>
        </div>
        <a href="{{ route('admin.airports.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 text-black rounded-xl font-semibold text-sm hover:from-amber-600 hover:to-amber-700 transition-all shadow-lg shadow-amber-500/20">
            <i class="fas fa-plus"></i>
            <span>Add Airport</span>
        </a>
    </div>

    <!-- Table Card -->
    <div class="bg-zinc-900/80 border border-zinc-800 rounded-xl overflow-hidden">
        <div class="p-4 border-b border-zinc-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-sm"></i>
                    <input type="text" placeholder="Search airports..." class="bg-zinc-800 border border-zinc-700 rounded-lg pl-9 pr-4 py-2 text-sm text-white placeholder-zinc-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 w-64">
                </div>
            </div>
            <span class="text-xs text-zinc-500">{{ $airports->total() }} total airports</span>
        </div>
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-zinc-500 bg-zinc-800/30">
                    <th class="px-6 py-4 font-semibold">IATA Code</th>
                    <th class="px-6 py-4 font-semibold">Airport Name</th>
                    <th class="px-6 py-4 font-semibold">City</th>
                    <th class="px-6 py-4 font-semibold">Country</th>
                    <th class="px-6 py-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @forelse($airports as $airport)
                <tr class="text-sm text-zinc-300 hover:bg-zinc-800/20 transition">
                    <td class="px-6 py-4">
                        <span class="font-mono text-amber-500 font-bold bg-amber-500/5 px-2.5 py-1 rounded text-xs">{{ $airport->iata_code }}</span>
                    </td>
                    <td class="px-6 py-4 font-medium text-white">{{ $airport->name }}</td>
                    <td class="px-6 py-4">{{ $airport->city }}</td>
                    <td class="px-6 py-4">
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-globe-asia text-zinc-600 text-xs"></i>
                            {{ $airport->country }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.airports.edit', $airport) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-zinc-800 text-zinc-300 rounded-lg text-xs hover:bg-zinc-700 hover:text-white transition">
                                <i class="fas fa-edit"></i>
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.airports.destroy', $airport) }}" onsubmit="return confirm('Are you sure you want to delete this airport?')">
                                @csrf @method('DELETE')
                                <button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-500/10 text-red-400 rounded-lg text-xs hover:bg-red-500/20 transition">
                                    <i class="fas fa-trash-alt"></i>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <i class="fas fa-plane-slash text-3xl text-zinc-700 mb-3 block"></i>
                        <p class="text-zinc-500">No airports found</p>
                        <a href="{{ route('admin.airports.create') }}" class="text-amber-500 hover:text-amber-400 text-sm mt-2 inline-block">Add your first airport</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($airports->hasPages())
        <div class="p-4 border-t border-zinc-800">
            {{ $airports->links() }}
        </div>
        @endif
    </div>
</div>
@endsection