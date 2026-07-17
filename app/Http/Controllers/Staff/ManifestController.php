<?php

namespace App\Http\Controllers\Staff;

use App\Exports\ReportArrayExport;
use App\Http\Controllers\Controller;
use App\Models\Flight;
use App\Models\Passenger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ManifestController extends Controller
{
    private const MANIFEST_BOOKING_STATUSES = ['pending', 'paid', 'issued'];

    public function index(): View
    {
        $flights = Flight::with(['airline', 'departureAirport', 'arrivalAirport'])
            ->withSum([
                'bookings as manifest_passengers_count' => fn ($query) =>
                    $query->whereIn('status', self::MANIFEST_BOOKING_STATUSES),
            ], 'total_passengers')
            ->where('departure_time', '>=', now()->subHours(6))
            ->orderBy('departure_time')
            ->paginate(10);

        return view('staff.manifest.index', compact('flights'));
    }

    public function show(Flight $flight): View
    {
        return view('staff.manifest.show', $this->manifestData($flight));
    }

    public function pdf(Flight $flight): Response
    {
        $data = $this->manifestData($flight);
        $filename = 'manifest-' . strtolower($flight->flight_number) . '-' . $flight->departure_time->format('Y-m-d') . '.pdf';

        return Pdf::loadView('staff.manifest.pdf', $data)
            ->setPaper('a4', 'landscape')
            ->download($filename);
    }

    public function excel(Flight $flight): BinaryFileResponse
    {
        $data = $this->manifestData($flight);
        $rows = $data['passengers']->values()->map(function (Passenger $passenger, int $index): array {
            return [
                $index + 1,
                $passenger->full_name,
                $passenger->gender_label,
                optional($passenger->resolved_date_of_birth)->format('d-m-Y') ?? '-',
                $passenger->resolved_seat_number,
                $passenger->phone ?? '-',
                $passenger->email ?? '-',
                $passenger->nationality ?? '-',
                $passenger->passport_number ?? '-',
                $passenger->booking?->booking_code ?? '-',
                ucfirst($passenger->booking?->status ?? '-'),
            ];
        })->all();

        $headings = [
            'No',
            'Passenger Name',
            'Gender',
            'Date of Birth',
            'Seat',
            'Phone',
            'Email',
            'Nationality',
            'Passport',
            'Booking Code',
            'Booking Status',
        ];

        $filename = 'manifest-' . strtolower($flight->flight_number) . '-' . $flight->departure_time->format('Y-m-d') . '.xlsx';

        return Excel::download(new ReportArrayExport($rows, $headings), $filename);
    }

    private function manifestData(Flight $flight): array
    {
        $flight->load(['airline', 'airplane', 'departureAirport', 'arrivalAirport']);

        $passengers = Passenger::query()
            ->whereHas('booking', function ($query) use ($flight) {
                $query->where('flight_id', $flight->id)
                    ->whereIn('status', self::MANIFEST_BOOKING_STATUSES);
            })
            ->with(['booking.user', 'seat'])
            ->orderBy('full_name')
            ->get();

        return [
            'flight' => $flight,
            'passengers' => $passengers,
            'summary' => $this->summary($passengers),
            'generatedAt' => now(),
        ];
    }

    private function summary(Collection $passengers): array
    {
        return [
            'total' => $passengers->count(),
            'male' => $passengers->where('gender_label', 'Male')->count(),
            'female' => $passengers->where('gender_label', 'Female')->count(),
            'pending' => $passengers->filter(fn (Passenger $passenger): bool => $passenger->booking?->status === 'pending')->count(),
            'confirmed' => $passengers->filter(fn (Passenger $passenger): bool => in_array($passenger->booking?->status, ['paid', 'issued'], true))->count(),
        ];
    }
}
