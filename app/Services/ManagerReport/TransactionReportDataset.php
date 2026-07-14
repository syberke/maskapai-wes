<?php

namespace App\Services\ManagerReport;

use App\Models\Booking;
use App\Models\Flight;
use App\Models\Passenger;

class TransactionReportDataset
{
    private const COMPLETED = ['paid', 'issued'];

    public function make(string $report): array
    {
        return match ($report) {
            'revenue' => $this->revenue(),
            'bookings' => $this->bookings(),
            'passengers' => $this->passengers(),
            default => abort(404),
        };
    }

    private function revenue(): array
    {
        $bookings = Booking::with(['user', 'flight.departureAirport', 'flight.arrivalAirport'])
            ->whereIn('status', self::COMPLETED)->latest()->get();

        return $this->dataset(
            'Laporan Pendapatan',
            'Ringkasan pendapatan dan transaksi penerbangan',
            'laporan-pendapatan',
            [
                'Pendapatan Hari Ini' => $this->rupiah(Booking::whereIn('status', self::COMPLETED)->whereDate('created_at', today())->sum('total_price')),
                'Pendapatan Minggu Ini' => $this->rupiah(Booking::whereIn('status', self::COMPLETED)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_price')),
                'Pendapatan Bulan Ini' => $this->rupiah(Booking::whereIn('status', self::COMPLETED)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_price')),
                'Pendapatan Tahun Ini' => $this->rupiah(Booking::whereIn('status', self::COMPLETED)->whereYear('created_at', now()->year)->sum('total_price')),
            ],
            ['Kode Booking', 'Pelanggan', 'Rute', 'Jumlah', 'Status', 'Tanggal Transaksi'],
            $bookings->map(fn (Booking $booking) => [
                $booking->booking_code,
                $booking->user?->name ?? '-',
                $this->routeName($booking->flight),
                (float) $booking->total_price,
                ucfirst($booking->status),
                $booking->created_at->format('d/m/Y H:i'),
            ])->all(),
            [3],
        );
    }

    private function bookings(): array
    {
        $bookings = Booking::with(['user', 'flight.departureAirport', 'flight.arrivalAirport'])->latest()->get();

        return $this->dataset(
            'Laporan Booking',
            'Rekapitulasi seluruh pemesanan penerbangan',
            'laporan-booking',
            [
                'Total Booking' => Booking::count(),
                'Paid / Issued' => Booking::whereIn('status', self::COMPLETED)->count(),
                'Pending' => Booking::where('status', 'pending')->count(),
                'Cancelled / Refunded' => Booking::whereIn('status', ['cancelled', 'refunded'])->count(),
            ],
            ['Kode Booking', 'Pelanggan', 'Rute', 'Penumpang', 'Kelas Kabin', 'Status', 'Total Harga', 'Tanggal Booking'],
            $bookings->map(fn (Booking $booking) => [
                $booking->booking_code,
                $booking->user?->name ?? '-',
                $this->routeName($booking->flight),
                (int) $booking->total_passengers,
                $booking->cabin_class_label,
                ucfirst($booking->status),
                (float) $booking->total_price,
                $booking->created_at->format('d/m/Y H:i'),
            ])->all(),
            [6],
        );
    }

    private function passengers(): array
    {
        $passengers = Passenger::with(['booking.flight.departureAirport', 'booking.flight.arrivalAirport'])->latest()->get();

        return $this->dataset(
            'Laporan Penumpang',
            'Rekap data dan demografi penumpang',
            'laporan-penumpang',
            [
                'Total Penumpang' => Passenger::count(),
                'Berangkat Hari Ini' => Passenger::whereHas('booking.flight', fn ($query) => $query->whereDate('departure_time', today()))->count(),
                'Laki-laki' => Passenger::where('gender', 'L')->count(),
                'Perempuan' => Passenger::where('gender', 'P')->count(),
            ],
            ['Nama Lengkap', 'Jenis Kelamin', 'Tanggal Lahir', 'No. Paspor', 'Kode Booking', 'Rute', 'Kursi'],
            $passengers->map(fn (Passenger $passenger) => [
                $passenger->full_name,
                $passenger->gender === 'L' ? 'Laki-laki' : 'Perempuan',
                $passenger->birth_date ? date('d/m/Y', strtotime($passenger->birth_date)) : '-',
                $passenger->passport_number ?: '-',
                $passenger->booking?->booking_code ?? '-',
                $this->routeName($passenger->booking?->flight),
                $passenger->seat_number ?: '-',
            ])->all(),
        );
    }

    private function dataset(string $title, string $subtitle, string $filename, array $summary, array $headings, array $rows, array $moneyColumns = []): array
    {
        return compact('title', 'subtitle', 'filename', 'summary', 'headings', 'rows') + ['money_columns' => $moneyColumns];
    }

    private function routeName(?Flight $flight): string
    {
        return $flight
            ? (($flight->departureAirport?->iata_code ?? '?') . ' - ' . ($flight->arrivalAirport?->iata_code ?? '?'))
            : '-';
    }

    private function rupiah(int|float|string|null $amount): string
    {
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }
}
