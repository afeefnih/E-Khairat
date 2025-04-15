<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transactions Report</title>
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
        .pendapatan {
            color: green;
            font-weight: bold;
        }
        .perbelanjaan {
            color: red;
            font-weight: bold;
        }
        .completed {
            color: green;
        }
        .pending {
            color: orange;
        }
        .cancelled {
            color: red;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Transactions Report</h1>
        <p>Generated on: {{ date('F j, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Transaction Name</th>
                <th>Member</th>
                <th>Type</th>
                <th>Amount (RM)</th>
                <th>Payment Method</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->transaction_date ? $transaction->transaction_date->format('Y-m-d') : 'N/A' }}</td>
                <td>{{ $transaction->name }}</td>
                <td>{{ $transaction->user ? $transaction->user->name : 'N/A' }}</td>
                <td class="{{ $transaction->type }}">
                    @if($transaction->type == 'pendapatan')
                        Pendapatan
                    @elseif($transaction->type == 'perbelanjaan')
                        Perbelanjaan
                    @else
                        {{ $transaction->type }}
                    @endif
                </td>
                <td>RM {{ number_format($transaction->amount, 2) }}</td>
                <td>{{ $transaction->payment_method ?? 'N/A' }}</td>
                <td class="{{ $transaction->status }}">
                    @if($transaction->status == 'completed')
                        Selesai
                    @elseif($transaction->status == 'pending')
                        Belum Selesai
                    @elseif($transaction->status == 'cancelled')
                        Batal
                    @else
                        {{ $transaction->status }}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report is confidential and for internal use only.</p>
    </div>
</body>
</html>
