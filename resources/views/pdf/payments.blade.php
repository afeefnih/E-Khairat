<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payments Report</title>
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
        .paid {
            color: green;
            font-weight: bold;
        }
        .pending {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Payments Report</h1>
        <p>Generated on: {{ date('F j, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Member Name</th>
                <th>Payment Category</th>
                <th>Amount (RM)</th>
                <th>Status</th>
                <th>Billcode</th>
                <th>Order ID</th>
                <th>Paid At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td>{{ $payment->user ? $payment->user->name : 'N/A' }}</td>
                <td>{{ $payment->payment_category ? $payment->payment_category->category_name : 'N/A' }}</td>
                <td>RM {{ number_format($payment->amount, 2) }}</td>
                <td class="{{ $payment->status_id == '1' ? 'paid' : 'pending' }}">
                    {{ $payment->status_id == '1' ? 'Paid' : 'Pending' }}
                </td>
                <td>{{ $payment->billcode ?? 'N/A' }}</td>
                <td>{{ $payment->order_id ?? 'N/A' }}</td>
                <td>
                    @if($payment->paid_at)
                        @if(is_string($payment->paid_at))
                            {{ $payment->paid_at }}
                        @else
                            {{ $payment->paid_at->format('Y-m-d H:i:s') }}
                        @endif
                    @else
                        N/A
                    @endif
                </td>            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report is confidential and for internal use only.</p>
    </div>
</body>
</html>
