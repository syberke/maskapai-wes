<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Passenger Manifest {{ $flight->flight_number }}</title>
    <style>
        @page { margin: 24px 28px; }
        body { font-family: DejaVu Sans, sans-serif; color: #18181b; font-size: 10px; }
        .header { border-bottom: 3px solid #d97706; padding-bottom: 12px; margin-bottom: 14px; }
        .brand { font-size: 21px; font-weight: bold; color: #b45309; }
        .subtitle { color: #52525b; font-size: 9px; margin-top: 3px; }
        .title { font-size: 17px; font-weight: bold; margin: 14px 0 5px; }
        .meta, .summary { width: 100%; border-collapse: collapse; }
        .meta td { padding: 3px 8px 3px 0; vertical-align: top; }
        .label { color: #71717a; width: 105px; }
        .summary { margin: 14px 0; }
        .summary td { border: 1px solid #d4d4d8; padding: 8px; text-align: center; }
        .summary strong { display: block; font-size: 16px; margin-top: 3px; }
        .data { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .data th { background: #27272a; color: white; padding: 7px 5px; font-size: 8px; text-align: left; }
        .data td { border: 1px solid #d4d4d8; padding: 6px 5px; vertical-align: top; word-wrap: break-word; }
        .data tr:nth-child(even) { background: #fafafa; }
        .footer { margin-top: 12px; padding-top: 8px; border-top: 1px solid #d4d4d8; color: #71717a; font-size: 8px; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">LuxuryFly</div>
        <div class="subtitle">Operational Passenger Manifest</div>
    </div>

    <div class="title">Passenger Manifest Report</div>
    <table class="meta">
        <tr>
            <td class="label">Flight</td><td><strong>{{ $flight->airline->name }} · {{ $flight->flight_number }}</strong></td>
            <td class="label">Generated</td><td>{{ $generatedAt->format('d M Y H:i') }}</td>
        </tr>
        <tr>
            <td class="label">Route</td><td>{{ $flight->departureAirport->iata_code }} → {{ $flight->arrivalAirport->iata_code }}</td>
            <td class="label">Aircraft</td><td>{{ $flight->airplane->model ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Departure</td><td>{{ $flight->departure_time->format('d M Y H:i') }}</td>
            <td class="label">Gate / Terminal</td><td>{{ $flight->gate ?? '-' }} / {{ $flight->terminal ?? '-' }}</td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td>Total<strong>{{ $summary['total'] }}</strong></td>
            <td>Male<strong>{{ $summary['male'] }}</strong></td>
            <td>Female<strong>{{ $summary['female'] }}</strong></td>
            <td>Confirmed<strong>{{ $summary['confirmed'] }}</strong></td>
            <td>Pending<strong>{{ $summary['pending'] }}</strong></td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width:3%">No</th>
                <th style="width:15%">Passenger</th>
                <th style="width:6%">Gender</th>
                <th style="width:9%">Birth Date</th>
                <th style="width:6%">Seat</th>
                <th style="width:10%">Phone</th>
                <th style="width:14%">Email</th>
                <th style="width:9%">Nationality</th>
                <th style="width:9%">Passport</th>
                <th style="width:11%">Booking Code</th>
                <th style="width:8%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($passengers as $index => $passenger)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $passenger->full_name }}</td>
                <td>{{ $passenger->gender_label }}</td>
                <td>{{ optional($passenger->resolved_date_of_birth)->format('d-m-Y') ?? '-' }}</td>
                <td>{{ $passenger->resolved_seat_number }}</td>
                <td>{{ $passenger->phone ?? '-' }}</td>
                <td>{{ $passenger->email ?? '-' }}</td>
                <td>{{ $passenger->nationality ?? '-' }}</td>
                <td>{{ $passenger->passport_number ?? '-' }}</td>
                <td>{{ $passenger->booking->booking_code ?? '-' }}</td>
                <td>{{ ucfirst($passenger->booking->status ?? 'pending') }}</td>
            </tr>
            @empty
            <tr><td colspan="11" class="text-center">No passenger records available.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        This manifest is generated from active booking records. Cancelled and refunded bookings are excluded. Staff must verify passenger identity before boarding.
    </div>
</body>
</html>
