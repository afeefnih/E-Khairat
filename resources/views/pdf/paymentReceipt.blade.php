<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resit Pembayaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
        }
        .logo {
            margin-bottom: 15px;
            /* Center the logo */
            display: flex;
            justify-content: center;
        }
        .logo img {
            width: auto;
            height: 80px;
            /* Prevent stretching */
            object-fit: contain;
            /* Add some breathing room */
            margin-bottom: 10px;
        }
        .receipt-title {
            font-size: 20px;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        .receipt-number {
            font-size: 16px;
            color: #666;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .info-table th {
            text-align: left;
            padding: 10px;
            background-color: #f9fafb;
            border-bottom: 1px solid #ddd;
            width: 40%;
        }
        .info-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .payment-details {
            margin-bottom: 30px;
        }
        .payment-details h2 {
            font-size: 18px;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .payment-table th {
            text-align: left;
            padding: 10px;
            background-color: #f9fafb;
            border-bottom: 1px solid #ddd;
        }
        .payment-table th:last-child {
            text-align: right;
        }
        .payment-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .payment-table td:last-child {
            text-align: right;
        }
        .total-row td {
            font-weight: bold;
            border-top: 2px solid #ddd;
        }
        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-paid {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-pending {
            background-color: #fff7ed;
            color: #9a3412;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="{{ public_path('images/logo.png') }}" alt="Logo e-Khairat">
            </div>
            <div class="receipt-title">RESIT RASMI</div>
            <div class="receipt-number">{{ $receiptNumber }}</div>
        </div>

        <!-- Payment Information Section -->
        <div class="payment-details">
            <h2>Maklumat Pembayaran</h2>
            <table class="info-table">
                <tr>
                    <th>Nombor Resit</th>
                    <td>{{ $receiptNumber }}</td>
                </tr>
                <tr>
                    <th>Tarikh</th>
                    <td>{{ $payment->paid_at ? date('j F, Y', strtotime($payment->paid_at)) : date('j F, Y', strtotime($payment->created_at)) }}</td>
                </tr>
                <tr>
                    <th>Kaedah Pembayaran</th>
                    <td>FPX / Perbankan Dalam Talian</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="status {{ $payment->status_id == 1 ? 'status-paid' : 'status-pending' }}">
                            {{ $payment->status_id == 1 ? 'DIBAYAR' : 'MENUNGGU' }}
                        </span>
                    </td>
                </tr>
                @if($payment->billcode)
                <tr>
                    <th>Nombor Rujukan</th>
                    <td>{{ $payment->billcode }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Member Information Section -->
        <div class="payment-details">
            <h2>Maklumat Ahli</h2>
            <table class="info-table">
                <tr>
                    <th>Nama</th>
                    <td>{{ $payment->user->name }}</td>
                </tr>
                <tr>
                    <th>Nombor IC</th>
                    <td>{{ $payment->user->ic_number }}</td>
                </tr>
                <tr>
                    <th>No Ahli</th>
                    <td>{{ $payment->user->No_Ahli ?? 'Tiada' }}</td>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td>{{ $payment->user->address }}</td>
                </tr>
            </table>
        </div>

        <!-- Payment Details Section -->
        <div class="payment-details">
            <h2>Butiran Pembayaran</h2>
            <table class="payment-table">
                <thead>
                    <tr>
                        <th>Perkara</th>
                        <th>Jumlah (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $payment->payment_category->category_name ?? 'Pembayaran' }}</td>
                        <td>{{ number_format($payment->amount, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>Jumlah Keseluruhan</td>
                        <td>{{ number_format($payment->amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <p>Ini adalah resit yang dijana komputer dan tidak memerlukan tandatangan.</p>
            <p>Untuk sebarang pertanyaan, sila hubungi pentadbiran e-Khairat.</p>
            <p>Terima kasih atas pembayaran anda.</p>
        </div>
    </div>
</body>
</html>
