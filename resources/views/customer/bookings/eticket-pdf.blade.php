<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>E-Ticket {{ $booking->booking_code }}</title>
<style>
@page{margin:28px}body{font-family:DejaVu Sans,sans-serif;color:#18181b;font-size:10px}.header{background:#d97706;padding:20px}.brand{font-size:22px;font-weight:bold}.code{float:right;font-size:14px;font-weight:bold}.content{padding:20px;border:1px solid #d4d4d8}.meta,.passengers{width:100%;border-collapse:collapse}.meta td,.passengers td,.passengers th{border:1px solid #d4d4d8;padding:7px}.passengers th{background:#27272a;color:#fff;text-align:left}.route{text-align:center;font-size:22px;font-weight:bold;margin:18px 0}.muted{color:#71717a;font-size:8px}.paid{color:#047857;font-weight:bold}.footer{margin-top:15px;padding-top:10px;border-top:1px solid #d4d4d8;color:#71717a;font-size:8px}
</style>
</head>
<body>
<div class="header"><div class="code">{{ $booking->booking_code }}</div><div class="brand">LUXURYFLY</div><div>Official Multi-Passenger E-Ticket</div></div>
<div class="content">
<table class="meta"><tr><td><span class="muted">AIRLINE</span><br><strong>{{ $booking->flight->airline->name }} · {{ $booking->flight->flight_number }}</strong></td><td><span class="muted">DEPARTURE</span><br><strong>{{ $booking->flight->departure_time->format('d M Y H:i') }}</strong></td><td><span class="muted">STATUS</span><br><span class="paid">{{ strtoupper($booking->status) }}</span></td></tr></table>
<div class="route">{{ $booking->flight->departureAirport->iata_code }} &nbsp; → &nbsp; {{ $booking->flight->arrivalAirport->iata_code }}</div>
<h3>Passenger Details ({{ $booking->passengers->count() }})</h3>
<table class="passengers"><thead><tr><th>No</th><th>Name</th><th>Gender</th><th>Date of Birth</th><th>Seat</th><th>Nationality</th><th>Passport</th></tr></thead><tbody>
@foreach($booking->passengers as $index => $passenger)
<tr><td>{{ $index + 1 }}</td><td>{{ $passenger->full_name }}</td><td>{{ $passenger->gender_label }}</td><td>{{ optional($passenger->resolved_date_of_birth)->format('d-m-Y') ?? '-' }}</td><td>{{ $passenger->resolved_seat_number }}</td><td>{{ $passenger->nationality ?? '-' }}</td><td>{{ $passenger->passport_number ?? '-' }}</td></tr>
@endforeach
</tbody></table>
<table class="meta" style="margin-top:15px"><tr><td>Total passengers: <strong>{{ $booking->total_passengers }}</strong></td><td>Total paid: <strong>Rp {{ number_format($booking->total_price,0,',','.') }}</strong></td><td>Payment: <span class="paid">{{ strtoupper($booking->payment->payment_status ?? 'paid') }}</span></td></tr></table>
<div class="footer">This E-Ticket confirms every passenger listed above. Passenger identification must match the manifest.</div>
</div>
</body>
</html>
