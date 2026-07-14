<?php

namespace App\Http\Controllers\Manager;

use App\Exports\ReportArrayExport;
use App\Http\Controllers\Controller;
use App\Services\ManagerReport\PerformanceReportDataset;
use App\Services\ManagerReport\TransactionReportDataset;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportExportController extends Controller
{
    public function __construct(
        private readonly TransactionReportDataset $transactionReports,
        private readonly PerformanceReportDataset $performanceReports,
    ) {
    }

    public function excel(string $report)
    {
        $dataset = $this->dataset($report);
        $filename = $dataset['filename'] . '-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(
            new ReportArrayExport($dataset['rows'], $dataset['headings']),
            $filename
        );
    }

    public function pdf(string $report)
    {
        $dataset = $this->dataset($report);
        $filename = $dataset['filename'] . '-' . now()->format('Ymd-His') . '.pdf';

        return Pdf::loadView('manager.reports.exports.pdf', [
            'report' => $dataset,
            'generatedAt' => now(),
        ])
            ->setPaper('a4', 'landscape')
            ->download($filename);
    }

    private function dataset(string $report): array
    {
        return in_array($report, ['revenue', 'bookings', 'passengers'], true)
            ? $this->transactionReports->make($report)
            : $this->performanceReports->make($report);
    }
}
