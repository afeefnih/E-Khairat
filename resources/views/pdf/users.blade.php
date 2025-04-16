<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 10pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #000;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
        }
        td {
            word-wrap: break-word; /* Ensures long words will break */
            max-width: 100px; /* Limits column width */
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .footer {
            text-align: center;
            font-size: 8pt;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Senarai Ahli Khairat Kematian Masjid Taman Sutera, Kajang</h1>
        <p>Generated on: {{ \Carbon\Carbon::now()->format('F j, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No. Ahli</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Nombor IC</th>
                <th>Nombor Telefon</th>
                <th>Alamat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->No_Ahli ?? 'N/A' }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->ic_number }}</td>
                <td>{{ $user->phone_number }}</td>
                <td>{{ $user->address }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report is confidential and for internal use only.</p>
    </div>
</body>
</html>
