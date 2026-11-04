<!DOCTYPE html>
<html>
<head>
    <title>Low Stock Warning</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <h2 style="color: #e11d48;">⚠️ Low Stock Alert</h2>
    <p>The following asset has reached or fallen below its minimum stock level:</p>
    <ul>
        <li><strong>Asset Name:</strong> {{ $asset->name }}</li>
        <li><strong>SKU:</strong> {{ $asset->sku }}</li>
        <li><strong>Available Quantity:</strong> {{ $asset->items()->where('status', 'available')->count() }}</li>
        <li><strong>Minimum Stock Level:</strong> {{ $asset->min_stock_level }}</li>
    </ul>
    <p>Please restock this item as soon as possible.</p>
</body>
</html>