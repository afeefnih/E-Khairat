<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Categories Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .header {
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
        }
        .active {
            color: green;
            font-weight: bold;
        }
        .inactive {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Payment Categories Report</h1>
        <p>Generated on: {{ date('F j, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Category Name</th>
                <th>Description</th>
                <th>Amount (RM)</th>
                <th>Status</th>
                <th>Number of Payments</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
            <tr>
                <td>{{ $category->category_name }}</td>
                <td>{{ $category->category_description ?? 'N/A' }}</td>
                <td>RM {{ number_format($category->amount, 2) }}</td>
                <td class="{{ $category->category_status }}">
                    {{ ucfirst($category->category_status) }}
                </td>
                <td>{{ $category->payments_count ?? 0 }}</td>
                <td>{{ $category->created_at->format('Y-m-d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report is confidential and for internal use only.</p>
    </div>
</body>
</html>
