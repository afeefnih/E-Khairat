<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dependents Report</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Dependents Report</h1>
        <p>Generated on: {{ date('F j, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Ahli</th>
                <th>Nama Penuh</th>
                <th>Hubungan</th>
                <th>Umur</th>
                <th>Nombor KP</th>
                <th>Tarikh Daftar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dependents as $dependent)
            <tr>
                <td>{{ $dependent->user ? $dependent->user->name : 'N/A' }}</td>
                <td>{{ $dependent->full_name }}</td>
                <td>{{ $dependent->relationship }}</td>
                <td>{{ $dependent->age }}</td>
                <td>{{ $dependent->ic_number }}</td>
                <td>{{ $dependent->created_at->format('Y-m-d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report is confidential and for internal use only.</p>
    </div>
</body>
</html>
