<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $report['title'] }}</title>
    <style>
        @page { margin: 20mm 16mm 18mm; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; color: #18181b; font-size: 10px; line-height: 1.45; }
        .header { border-bottom: 3px solid #d97706; padding-bottom: 12px; margin-bottom: 18px; }
        .brand { font-size: 22px; font-weight: 700; letter-spacing: 2px; color: #b45309; }
        .brand span { color: #18181b; font-weight: 400; }
        .document-label { margin-top: 3px; color: #71717a; font-size: 9px; text-transform: uppercase; letter-spacing: 1.5px; }
        .title { margin: 18px 0 4px; font-size: 19px; color: #18181b; }
        .subtitle { color: #71717a; margin: 0; }
        .meta { width: 100%; margin-top: 14px; border-collapse: collapse; }
        .meta td { padding: 3px 0; vertical-align: top; }
        .meta .label { width: 110px; color: #71717a; }
        .section-title { margin: 20px 0 8px; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #92400e; }
        .summary { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .summary td { border: 1px solid #e4e4e7; padding: 10px 12px; background: #fafafa; }
        .summary-label { color: #71717a; font-size: 8px; text-transform: uppercase; letter-spacing: .5px; }
        .summary-value { margin-top: 4px; font-size: 13px; font-weight: 700; color: #18181b; }
        .report-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .report-table thead { display: table-header-group; }
        .report-table tr { page-break-inside: avoid; }
        .report-table th { background: #27272a; color: #fff; padding: 7px 6px; text-align: left; font-size: 8px; text-transform: uppercase; letter-spacing: .4px; }
        .report-table td { border-bottom: 1px solid #e4e4e7; padding: 7px 6px; vertical-align: top; }
        .report-table tbody tr:nth-child(even) td { background: #fafafa; }
        .empty { text-align: center; color: #71717a; padding: 18px !important; }
        .footer { position: fixed; bottom: -10mm; left: 0; right: 0; border-top: 1px solid #d4d4d8; padding-top: 6px; color: #71717a; font-size: 8px; }
        .footer-right { float: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">LUXURY<span>FLY</span></div>
        <div class="document-label">Manager Report</div>
        <h1 class="title">{{ $report['title'] }}</h1>
        <p class="subtitle">{{ $report['subtitle'] }}</p>

        <table class="meta">
            <tr>
                <td class="label">Tanggal laporan</td>
                <td>: {{ $generatedAt->format('d F Y, H:i') }}</td>
                <td class="label">Disiapkan oleh</td>
                <td>: {{ auth()->user()->name }}</td>
            </tr>
            <tr>
                <td class="label">Unit</td>
                <td>: Manager Panel</td>
                <td class="label">Status dokumen</td>
                <td>: Laporan sistem</td>
            </tr>
        </table>
    </div>

    <div class="section-title">Ringkasan Eksekutif</div>
    <table class="summary">
        <tr>
            @foreach($report['summary'] as $label => $value)
                <td>
                    <div class="summary-label">{{ $label }}</div>
                    <div class="summary-value">{{ $value }}</div>
                </td>
            @endforeach
        </tr>
    </table>

    <div class="section-title">Rincian Data</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 28px;">No.</th>
                @foreach($report['headings'] as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($report['rows'] as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    @foreach($row as $index => $value)
                        <td>
                            @if(in_array($index, $report['money_columns'], true))
                                Rp {{ number_format((float) $value, 0, ',', '.') }}
                            @else
                                {{ $value }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td class="empty" colspan="{{ count($report['headings']) + 1 }}">Belum ada data untuk laporan ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        LuxuryFly Management Information System
        <span class="footer-right">Dokumen dibuat otomatis pada {{ $generatedAt->format('d/m/Y H:i') }}</span>
    </div>
</body>
</html>
