<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><style>td,th{border:1px solid #999;padding:6px}th{background:#e8f1eb}.text{mso-number-format:"\@"}</style></head>
<body>
    <table>
        <thead>
            <tr><th colspan="6">Plant Nursery Inventory - {{ str_replace('_', ' ', $filters['report']) }}</th></tr>
            <tr><th>Plant name</th><th>Category</th><th>Unit price</th><th>Description</th><th>Stock status</th><th>Created by</th></tr>
        </thead>
        <tbody>
            @foreach ($plants as $plant)
                <tr>
                    <td class="text">{{ $plant->name }}</td>
                    <td class="text">{{ $plant->category }}</td>
                    <td>{{ number_format((float) $plant->unit_price, 2, '.', '') }}</td>
                    <td class="text">{{ $plant->description }}</td>
                    <td class="text">{{ $plant->stock_status === 'in_stock' ? 'In stock' : 'Out of stock' }}</td>
                    <td class="text">{{ $plant->user->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
