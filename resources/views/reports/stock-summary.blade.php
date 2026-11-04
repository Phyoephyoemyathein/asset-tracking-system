<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Summary Report</title>
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
        <h3>Stock Summary Report</h3>
        <span class="date">Generated on: {{ $date }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Asset Name</th>
                <th>Category</th>
                <th>Warehouse</th>
                <th class="text-center">Total Items</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $asset)
            <tr>
                <td>{{ $asset->sku }}</td>
                <td>{{ $asset->name }}</td>
                <td>{{ $asset->category->name ?? '-' }}</td>
                <td>{{ $asset->warehouse->name ?? '-' }}</td>
                <td class="text-center">{{ $asset->items->count() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>