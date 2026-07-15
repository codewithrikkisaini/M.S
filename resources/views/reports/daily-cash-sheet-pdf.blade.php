<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Cash Sheet</title>
    <style>
        body { font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 12px; }
        .sheet { padding: 20px; page-break-after: always; }
        .sheet:last-child { page-break-after: auto; }
        .header { text-align: center; margin-bottom: 10px; }
        .header h1 { font-size: 18px; margin: 0; }
        .header h2 { font-size: 14px; margin: 4px 0 0; letter-spacing: 1px; }
        .meta { width: 100%; margin-bottom: 14px; font-size: 12px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #888; padding: 5px 6px; text-align: left; }
        .table th { background: #eef0f3; font-size: 11px; }
        .text-right { text-align: right; }
        .totals { width: 60%; margin-top: 18px; }
        .totals td { padding: 4px 6px; }
    </style>
</head>
<body>
    @foreach($sheets as $sheet)
    <div class="sheet">
        <div class="header">
            <h1>{{ $hotelName }}</h1>
            <h2>DAILY CASH SHEET</h2>
        </div>
        <table class="meta">
            <tr>
                <td><strong>Date:</strong> {{ \Carbon\Carbon::parse($sheet['date'])->format('d M Y') }}</td>
                <td class="text-right"><strong>Day:</strong> {{ \Carbon\Carbon::parse($sheet['date'])->format('l') }}</td>
            </tr>
        </table>

        <table class="table">
            <thead>
                <tr>
                    <th>Room No.</th>
                    <th>Name</th>
                    <th>Rent</th>
                    <th>Tax</th>
                    <th>Misc. Chgs</th>
                    <th>Arrv. Date</th>
                    <th>Dept. Date</th>
                    <th>Bal. Due</th>
                    <th>Paid</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sheet['rows'] as $row)
                <tr>
                    <td>{{ $row['room_number'] }}</td>
                    <td>{{ $row['name'] ?? '' }}</td>
                    <td>{{ $row['rent'] !== null ? number_format($row['rent'], 2) : '' }}</td>
                    <td>{{ $row['tax'] !== null ? number_format($row['tax'], 2) : '' }}</td>
                    <td>{{ $row['misc'] ?? '' }}</td>
                    <td>{{ $row['arrival_date'] ? \Carbon\Carbon::parse($row['arrival_date'])->format('d/m/Y') : '' }}</td>
                    <td>{{ $row['departure_date'] ? \Carbon\Carbon::parse($row['departure_date'])->format('d/m/Y') : '' }}</td>
                    <td>{{ $row['balance_due'] !== null ? number_format($row['balance_due'], 2) : '' }}</td>
                    <td>{{ $row['paid'] !== null ? number_format($row['paid'], 2) : '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals">
            <tr><td><strong>Total Cash:</strong></td><td class="text-right">{{ number_format($sheet['totals']['cash'], 2) }}</td></tr>
            <tr><td><strong>Total Credit Card:</strong></td><td class="text-right">{{ number_format($sheet['totals']['card'], 2) }}</td></tr>
            <tr><td><strong>Total UPI:</strong></td><td class="text-right">{{ number_format($sheet['totals']['upi'], 2) }}</td></tr>
            <tr><td><strong>Grand Total Collected:</strong></td><td class="text-right">{{ number_format($sheet['totals']['grand_total'], 2) }}</td></tr>
            <tr><td><strong>Bank Dep.:</strong></td><td class="text-right">&nbsp;</td></tr>
        </table>
    </div>
    @endforeach
</body>
</html>
