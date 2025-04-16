<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Death Records Report</title>
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
        <h1>Death Records Report</h1>
        <p>Generated on: {{ date('F j, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Dependent Name</th>
                <th>IC Number</th>
                <th>Date of Death</th>
                <th>Place of Death</th>
                <th>Cause of Death</th>
                <th>Notes</th>
                <th>Certificate</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            <tr>
                <td>{{ $record->dependent ? $record->dependent->full_name : 'N/A' }}</td>
                <td>{{ $record->dependent ? $record->dependent->ic_number : 'N/A' }}</td>
                <td>{{ $record->date_of_death ? $record->date_of_death->format('Y-m-d') : 'N/A' }}</td>
                <td>{{ $record->place_of_death ?? 'N/A' }}</td>
                <td>{{ $record->cause_of_death ?? 'N/A' }}</td>
                <td>{{ $record->death_notes ?? 'N/A' }}</td>
                <td>{{ $record->death_attachment_path ? 'Available' : 'Not Available' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report is confidential and for internal use only.</p>
    </div>
</body>
</html>
