<div class="flex gap-2">
    <a href="{{ route('manager.reports.export.pdf', ['report' => $report]) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-zinc-800 text-zinc-300 rounded-lg text-xs hover:bg-zinc-700 transition">
        <i class="fas fa-file-pdf"></i> Export PDF
    </a>
    <a href="{{ route('manager.reports.export.excel', ['report' => $report]) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-500/10 text-amber-400 rounded-lg text-xs hover:bg-amber-500/20 transition">
        <i class="fas fa-file-excel"></i> Export Excel
    </a>
</div>
