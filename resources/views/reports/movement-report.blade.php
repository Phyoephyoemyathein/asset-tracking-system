<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ ucfirst($type) }} Movement Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #1f2937; }
        .date { font-size: 10px; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; color: #111827; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Asset & Warehouse Tracking System</h2>
        <h3>{{ ucfirst($type) }} Stock Movement Report</h3>
        <span class="date">Generated on: {{ $date }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Asset Name</th>
                <th>Type</th>
                <th class="text-center">Quantity</th>
                <th>Reference / Details</th>
                <th>User</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $trx)
            <tr>
                <td>{{ $trx->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $trx->asset->name ?? '-' }}</td>
                <td>{{ $trx->type }}</td>
                <td class="text-center">{{ $trx->quantity }}</td>
                <td>{{ $trx->reference }}</td>
                <td>{{ $trx->user->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>