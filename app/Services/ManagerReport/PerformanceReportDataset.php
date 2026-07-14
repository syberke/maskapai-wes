<?php

namespace App\Services\ManagerReport;

use App\Models\Airline;
use App\Models\Booking;
use App\Models\Flight;

class PerformanceReportDataset
{
    private const COMPLETED = ['paid', 'issued'];

    public function make(string $report): array
    {
        return match ($report) {
            'occupancy' => $this->occupancy(),
            'airline-performance' => $this->airlinePerformance(),
            'route-performance' => $this->routePerformance(),
            default => abort(404),
        };
    }

    private function occupancy(): array
    {
        $rows = Airline::withCount('flights')->get()->map(function (Airline $airline): array {
            $totalSeats = (int) $airline->airplanes()->sum('capacity');
            $completed = Booking::whereHas('flight', fn ($query) => $query->where('airline_id', $airline->id))
                ->whereIn('status', self::COMPLETED);
            $bookedSeats = (int) (clone $completed)->sum('total_passengers');

            return [
                $airline->name,
                (int) $airline->flights_count,
                $totalSeats,
                $bookedSeats,
                $totalSeats > 0 ? round(($bookedSeats / $totalSeats) * 100, 2) : 0,
                (float) (clone $completed)->sum('total_price'),
            ];
        })->all();

        return $this->dataset(
            'Laporan Okupansi',
            'Tingkat keterisian kursi per maskapai',
            'laporan-okupansi',
            [
                'Jumlah Maskapai' => Airline::count(),
                'Total Penerbangan' => Flight::count(),
                'Penumpang Paid / Issued' => Booking::whereIn('status', self::COMPLETED)->sum('total_passengers'),
                'Total Pendapatan' => $this->rupiah(Booking::whereIn('status', self::COMPLETED)->sum('total_price')),
            ],
            ['Maskapai', 'Total Penerbangan', 'Total Kapasitas', 'Kursi Terisi', 'Okupansi (%)', 'Pendapatan'],
            $rows,
            [5],
        );
    }

    private function airlinePerformance(): array
    {
        $rows = Airline::withCount('flights')->get()->map(function (Airline $airline): array {
            $bookings = Booking::whereHas('flight', fn ($query) => $query->where('airline_id', $airline->id));

            return [
                $airline->name,
                $airline->code,
                (int) $airline->flights_count,
                (clone $bookings)->count(),
                (float) (clone $bookings)->whereIn('status', self::COMPLETED)->sum('total_price'),
            ];
        })->all();

        return $this->dataset(
            'Laporan Kinerja Maskapai',
            'Perbandingan penerbangan, booking, dan pendapatan maskapai',
            'laporan-kinerja-maskapai',
            [
                'Jumlah Maskapai' => Airline::count(),
                'Total Penerbangan' => Flight::count(),
                'Total Booking' => Booking::count(),
                'Total Pendapatan' => $this->rupiah(Booking::whereIn('status', self::COMPLETED)->sum('total_price')),
            ],
            ['Maskapai', 'Kode', 'Total Penerbangan', 'Total Booking', 'Pendapatan'],
            $rows,
            [4],
        );
    }

    private function routePerformance(): array
    {
        $routes = Flight::with(['departureAirport', 'arrivalAirport'])
            ->selectRaw('departure_airport_id, arrival_airport_id, COUNT(*) as total_flights')
            ->groupBy('departure_airport_id', 'arrival_airport_id')
            ->orderByDesc('total_flights')
            ->get();

        $rows = $routes->map(function (Flight $route): array {
            $bookings = Booking::whereHas('flight', fn ($query) => $query
                ->where('departure_airport_id', $route->departure_airport_id)
                ->where('arrival_airport_id', $route->arrival_airport_id));

            return [
                ($route->departureAirport?->iata_code ?? '?') . ' - ' . ($route->arrivalAirport?->iata_code ?? '?'),
                (int) $route->total_flights,
                (clone $bookings)->count(),
                (float) (clone $bookings)->whereIn('status', self::COMPLETED)->sum('total_price'),
            ];
        })->all();

        return $this->dataset(
            'Laporan Kinerja Rute',
            'Analisis popularitas dan pendapatan setiap rute',
            'laporan-kinerja-rute',
            [
                'Jumlah Rute' => count($rows),
                'Total Penerbangan' => Flight::count(),
                'Total Booking' => Booking::count(),
                'Total Pendapatan' => $this->rupiah(Booking::whereIn('status', self::COMPLETED)->sum('total_price')),
            ],
            ['Rute', 'Total Penerbangan', 'Total Booking', 'Pendapatan'],
            $rows,
            [3],
        );
    }

    private function dataset(string $title, string $subtitle, string $filename, array $summary, array $headings, array $rows, array $moneyColumns = []): array
    {
        return compact('title', 'subtitle', 'filename', 'summary', 'headings', 'rows') + ['money_columns' => $moneyColumns];
    }

    private function rupiah(int|float|string|null $amount): string
    {
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }
}
